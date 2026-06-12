<?php

namespace App\Filament\Child\Pages;

use App\Models\Budget;
use App\Models\Expense;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class BudgetView extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationLabel = 'Anggaran Saya';
    protected static ?string $title           = 'Anggaran Saya';
    protected static ?int    $navigationSort  = 4;
    protected static string  $view            = 'filament.child.pages.budget-view';

    public function getAllBudgets(): Collection
    {
        $userId = auth()->id();

        return Budget::where('user_id', $userId)
            ->with('category')
            ->get()
            ->map(function ($budget) use ($userId) {
                $spent = Expense::where('user_id', $userId)
                    ->where('category_id', $budget->category_id)
                    ->where('status', 'approved')
                    ->when($budget->period === 'monthly', function ($q) {
                        $q->whereMonth('date', now()->month)
                          ->whereYear('date', now()->year);
                    })
                    ->when($budget->period === 'weekly', function ($q) {
                        $q->whereBetween('date', [
                            now()->startOfWeek(),
                            now()->endOfWeek(),
                        ]);
                    })
                    ->sum('amount');

                $percentage = $budget->limit_amount > 0
                    ? min(100, round(($spent / $budget->limit_amount) * 100))
                    : 0;

                return [
                    'category'    => $budget->category->name,
                    'period'      => $budget->period === 'monthly' ? 'Bulanan' : 'Mingguan',
                    'limit'       => $budget->limit_amount,
                    'spent'       => $spent,
                    'remaining'   => max(0, $budget->limit_amount - $spent),
                    'percentage'  => $percentage,
                    'is_exceeded' => $spent > $budget->limit_amount,
                ];
            });
    }
}
