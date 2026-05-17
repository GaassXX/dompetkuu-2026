<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Filament\Widgets\Widget;

class PendingApprovalWidget extends Widget
{
    protected static ?int $sort  = 0;
    protected static string $view = 'filament.parent.widgets.pending-approval-widget';

    public static function canView(): bool
    {
        $childIds = User::where('parent_id', auth()->id())->pluck('id')->toArray();
        if (empty($childIds)) return false;

        $pendingIncome  = Income::whereIn('user_id', $childIds)->where('status', 'pending')->count();
        $pendingExpense = Expense::whereIn('user_id', $childIds)->where('status', 'pending')->count();

        return ($pendingIncome + $pendingExpense) > 0;
    }

    public array $pendingItems = [];

    public function mount(): void
    {
        $childIds = User::where('parent_id', auth()->id())->pluck('id')->toArray();
        if (empty($childIds)) return;

        $incomes = Income::whereIn('user_id', $childIds)
            ->where('status', 'pending')
            ->with(['user', 'category'])
            ->latest('date')
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type'     => 'Pemasukan',
                'name'     => $item->user->name,
                'category' => $item->category->name,
                'amount'   => (float) $item->amount,
                'date'     => $item->date?->format('d M Y'),
            ])->toArray();

        $expenses = Expense::whereIn('user_id', $childIds)
            ->where('status', 'pending')
            ->with(['user', 'category'])
            ->latest('date')
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type'     => 'Pengeluaran',
                'name'     => $item->user->name,
                'category' => $item->category->name,
                'amount'   => (float) $item->amount,
                'date'     => $item->date?->format('d M Y'),
            ])->toArray();

        $merged = array_merge($incomes, $expenses);
        usort($merged, fn($a, $b) => strcmp($b['date'], $a['date']));
        $this->pendingItems = array_slice($merged, 0, 5);
    }
}
