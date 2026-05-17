<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\Widget;

class ParentLatestTransactions extends Widget
{
    protected static ?int $sort   = 4;
    protected static string $view = 'filament.parent.widgets.parent-latest-transactions';

    public array $transactions = [];

    public function mount(): void
    {
        $userId = auth()->id();

        $incomes = Income::where('user_id', $userId)
            ->with('category')
            ->latest('date')
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type'     => 'Pemasukan',
                'category' => $item->category->name ?? '-',
                'amount'   => (float) $item->amount,
                'date'     => $item->date?->format('Y-m-d') ?? '',
                'date_fmt' => $item->date?->format('d M Y') ?? '-',
                'status'   => $item->status,
            ])->toArray();

        $expenses = Expense::where('user_id', $userId)
            ->with('category')
            ->latest('date')
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type'     => 'Pengeluaran',
                'category' => $item->category->name ?? '-',
                'amount'   => (float) $item->amount,
                'date'     => $item->date?->format('Y-m-d') ?? '',
                'date_fmt' => $item->date?->format('d M Y') ?? '-',
                'status'   => $item->status,
            ])->toArray();

        $merged = array_merge($incomes, $expenses);
        usort($merged, fn($a, $b) => strcmp($b['date'], $a['date']));
        $this->transactions = array_slice($merged, 0, 5);
    }
}
