<?php

namespace App\Filament\Child\Widgets;

use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class LatestTransactions extends Widget
{
    protected static ?int $sort = 3;
    protected static string $view = 'filament.child.widgets.latest-transactions';

    public function getTransactions(): Collection
    {
        $userId = auth()->id();

        $incomes = Income::where('user_id', $userId)
            ->with('category')
            ->latest('date')
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type'     => 'Pemasukan',
                'category' => $item->category->name,
                'amount'   => $item->amount,
                'date'     => $item->date,
                'status'   => $item->status,
            ]);

        $expenses = Expense::where('user_id', $userId)
            ->with('category')
            ->latest('date')
            ->take(5)
            ->get()
            ->map(fn($item) => [
                'type'     => 'Pengeluaran',
                'category' => $item->category->name,
                'amount'   => $item->amount,
                'date'     => $item->date,
                'status'   => $item->status,
            ]);

        return $incomes->merge($expenses)
            ->sortByDesc('date')
            ->take(5)
            ->values();
    }
}
