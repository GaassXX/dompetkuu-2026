<?php

namespace App\Livewire\Mobile;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Income;
use App\Notifications\BudgetLimit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AddTransaction extends Component
{
    public ?string $type = null;
    public $category_id = '';
    public $amount = '';
    public $source = 'Cash';
    public $date;
    public $description = '';
    public $editId = null;

    public array $sources = [
        'Cash'          => '💵 Cash',
        'Transfer Bank' => '🏦 Transfer Bank',
        'E-Wallet'      => '📱 E-Wallet',
        'Lainnya'       => '📦 Lainnya',
    ];

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d\TH:i');

        if ($editId = request('edit')) {
            $type = request('type', 'Pemasukan');
            $model = $type === 'Pemasukan'
                ? Income::where('user_id', Auth::id())->findOrFail($editId)
                : Expense::where('user_id', Auth::id())->findOrFail($editId);

            $this->editId = $model->id;
            $this->type = $type;
            $this->category_id = (string) $model->category_id;
            $this->amount = (string) (int) $model->amount;
            $this->source = $model->source ?? 'Cash';
            $this->date = \Carbon\Carbon::parse($model->date)->format('Y-m-d\TH:i');
            $this->description = $model->description ?? '';
        }
    }

    public function getCategoriesProperty()
    {
        if (!$this->type) return collect();

        $dbType = $this->type === 'Pemasukan' ? 'income' : 'expense';

        return Category::query()
            ->where('type', $dbType)
            ->where(function ($q) {
                $q->where('is_global', true)
                  ->orWhere('created_by', Auth::id());
            })
            ->orderBy('name')
            ->get();
    }

    public function selectType(?string $type): void
    {
        $this->type = $type;
        $this->category_id = '';
    }

    public function save()
    {
        $this->validate([
            'category_id' => 'required|exists:categories,id',
            'amount'      => 'required|numeric|min:1|max:999999999999999999',
            'source'      => 'required|string|in:Cash,Transfer Bank,E-Wallet,Lainnya',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:255',
        ], [
            'amount.max' => 'Jumlah tidak boleh lebih dari 999.999.999.999.999.999',
        ]);

        $data = [
            'user_id'     => Auth::id(),
            'category_id' => $this->category_id,
            'amount'      => $this->amount,
            'source'      => $this->source,
            'description' => $this->description,
            'date'        => \Carbon\Carbon::parse($this->date)->format('Y-m-d H:i:s'),
            'status'      => 'approved',
        ];

        if ($this->editId) {
            $model = $this->type === 'Pemasukan'
                ? Income::where('user_id', Auth::id())->findOrFail($this->editId)
                : Expense::where('user_id', Auth::id())->findOrFail($this->editId);
            $model->update($data);
            session()->flash('success', 'Transaksi berhasil diperbarui.');
        } else {
            if ($this->type === 'Pemasukan') {
                Income::create($data);
            } else {
                Expense::create($data);
                $this->checkBudgetLimit($this->category_id, $this->amount);
            }
            session()->flash('success', 'Transaksi berhasil dicatat.');
        }

        return redirect()->route('mobile.dashboard');
    }

    protected function checkBudgetLimit($categoryId, $amount)
    {
        if (!$categoryId || $this->type !== 'Pengeluaran') return;

        $user = Auth::user();
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
        return view('livewire.mobile.add-transaction', [
            'categories' => $this->categories,
            'sources'    => $this->sources,
        ])->layout('layouts.mobile', ['noNavbar' => true]);
    }
}
