<?php

namespace App\Filament\Child\Widgets;

use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\Widget;

class LatestTransactions extends Widget
{
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '300px';
    protected static string $view = 'filament.child.widgets.latest-transactions';

    public function getColumnSpan(): int | string | array
    {
        return 7;
    }

    public array $transactions = [];

    public function mount(): void
    {
        $userId = auth()->id();

        $incomes = Income::where('user_id', $userId)
            ->with('category')
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type'       => 'Pemasukan',
                'category'   => $item->category->name ?? '-',
                'amount'     => (float) $item->amount,
                'date'       => $item->date?->format('Y-m-d') ?? '',
                'created_at' => $item->created_at,
                'status'     => $item->status,
            ])->toArray();

        $expenses = Expense::where('user_id', $userId)
            ->with('category')
            ->latest('created_at')
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type'       => 'Pengeluaran',
                'category'   => $item->category->name ?? '-',
                'amount'     => (float) $item->amount,
                'date'       => $item->date?->format('Y-m-d') ?? '',
                'created_at' => $item->created_at,
                'status'     => $item->status,
            ])->toArray();

        $merged = array_merge($incomes, $expenses);

        usort($merged, fn($a, $b) => $b['created_at'] <=> $a['created_at']);

        $this->transactions = array_slice($merged, 0, 5);
    }
}
