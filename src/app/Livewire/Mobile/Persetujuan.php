<?php

namespace App\Livewire\Mobile;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use App\Notifications\BudgetLimit;
use App\Notifications\TransactionProcessed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class Persetujuan extends Component
{
    public $filter = 'all';

    public function getPendingTransactions()
    {
        $childIds = User::where('parent_id', Auth::id())->pluck('id');

        $incomes = Income::whereIn('user_id', $childIds)
            ->where('status', 'pending')
            ->get()
            ->map(fn ($i) => [
                'id'          => 'I-' . $i->id,
                'child_name'  => $i->user?->name ?? '-',
                'type'        => 'Pemasukan',
                'amount'      => $i->amount,
                'category'    => $i->category?->name ?? '-',
                'description' => $i->description,
                'date'        => $i->date,
                'model_type'  => 'income',
                'model_id'    => $i->id,
            ]);

        $expenses = Expense::whereIn('user_id', $childIds)
            ->where('status', 'pending')
            ->get()
            ->map(fn ($e) => [
                'id'          => 'E-' . $e->id,
                'child_name'  => $e->user?->name ?? '-',
                'type'        => 'Pengeluaran',
                'amount'      => $e->amount,
                'category'    => $e->category?->name ?? '-',
                'description' => $e->description,
                'date'        => $e->date,
                'model_type'  => 'expense',
                'model_id'    => $e->id,
            ]);

        $all = $incomes->concat($expenses)->sortByDesc('date')->values();

        if ($this->filter === 'income') {
            return $all->where('type', 'Pemasukan');
        }
        if ($this->filter === 'expense') {
            return $all->where('type', 'Pengeluaran');
        }

        return $all;
    }

    public function approve($id)
    {
        $parts = explode('-', $id, 2);
        $type = $parts[0] === 'I' ? 'income' : 'expense';
        $modelId = $parts[1] ?? null;

        if (!$modelId) return;

        if ($type === 'income') {
            Income::where('id', $modelId)->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
            ]);
        } else {
            Expense::where('id', $modelId)->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
            ]);
        }

        $model = $type === 'income'
            ? Income::with('category')->where('id', $modelId)->first()
            : Expense::with('category')->where('id', $modelId)->first();

        if ($model) {
            $child = User::find($model->user_id);
            if ($child) {
                $child->notify(new TransactionProcessed(
                    status: 'approved',
                    type: $type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    amount: $model->amount,
                ));

                if ($child->telegram_chat_id) {
                    $label = $type === 'income' ? 'Pemasukan' : 'Pengeluaran';
                    Http::post(
                        "https://api.telegram.org/bot" . config('services.telegram.bot_token') . "/sendMessage",
                        [
                            'chat_id'    => $child->telegram_chat_id,
                            'text'       => "✅ *{$label} Berhasil Dicatat*\n\n"
                                . "💰 Rp" . number_format($model->amount, 0, ',', '.') . "\n"
                                . "📂 {$model->category?->name}\n"
                                . "📝 {$model->description}",
                            'parse_mode' => 'Markdown',
                        ]
                    );
                }

                if ($type === 'expense') {
                    $this->checkBudgetLimit($child, $model->category_id, $model->amount);
                }
            }
        }
    }

    public function reject($id)
    {
        $parts = explode('-', $id, 2);
        $type = $parts[0] === 'I' ? 'income' : 'expense';
        $modelId = $parts[1] ?? null;

        if (!$modelId) return;

        if ($type === 'income') {
            Income::where('id', $modelId)->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
            ]);
        } else {
            Expense::where('id', $modelId)->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
            ]);
        }

        $model = $type === 'income'
            ? Income::with('category')->where('id', $modelId)->first()
            : Expense::with('category')->where('id', $modelId)->first();

        if ($model) {
            $child = User::find($model->user_id);
            if ($child) {
                $child->notify(new TransactionProcessed(
                    status: 'rejected',
                    type: $type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    amount: $model->amount,
                ));

                if ($child->telegram_chat_id) {
                    $label = $type === 'income' ? 'Pemasukan' : 'Pengeluaran';
                    Http::post(
                        "https://api.telegram.org/bot" . config('services.telegram.bot_token') . "/sendMessage",
                        [
                            'chat_id'    => $child->telegram_chat_id,
                            'text'       => "❌ *{$label} Ditolak*\n\n"
                                . "💰 Rp" . number_format($model->amount, 0, ',', '.') . "\n"
                                . "📂 {$model->category?->name}",
                            'parse_mode' => 'Markdown',
                        ]
                    );
                }
            }
        }
    }

    protected function checkBudgetLimit($user, $categoryId, $amount)
    {
        if (!$categoryId) return;

        $budgets = Budget::where('user_id', $user->id)
            ->where('category_id', $categoryId)
            ->get();

        foreach ($budgets as $budget) {
            $totalSpent = $budget->getSpentAmount();
            $limitAmount = (float) $budget->limit_amount;

            if ($limitAmount > 0 && $totalSpent >= $limitAmount * 0.8) {
                $user->notify(new BudgetLimit(
                    categoryName: $budget->category?->name ?? 'Kategori',
                    totalSpent: $totalSpent,
                    limitAmount: $limitAmount,
                ));
            }
        }
    }

    public function render()
    {
        $user = Auth::user();
        $childIds = User::where('parent_id', $user->id)->pluck('id');
        $pendingCount = Income::whereIn('user_id', $childIds)->where('status', 'pending')->count()
            + Expense::whereIn('user_id', $childIds)->where('status', 'pending')->count();

        return view('livewire.mobile.persetujuan', [
            'transactions' => $this->getPendingTransactions(),
            'pendingCount' => $pendingCount,
        ])->layout('layouts.mobile', ['title' => 'Persetujuan']);
    }
}
