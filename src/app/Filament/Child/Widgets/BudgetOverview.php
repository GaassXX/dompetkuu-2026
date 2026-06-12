<?php

namespace App\Filament\Child\Widgets;

use App\Models\Budget;
use App\Models\Expense;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class BudgetOverview extends Widget
{
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '300px';
    protected static string $view = 'filament.child.widgets.budget-overview';

    public bool $showAll = false;

    public function toggleShowAll(): void
    {
        $this->showAll = !$this->showAll;
    }

    public function getBudgets(): Collection
    {
        $userId = auth()->id();

        $budgets = Budget::where('user_id', $userId)
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

        return $this->showAll ? $budgets : $budgets->take(5);
    }

    public function getTotalBudgets(): int
    {
        return Budget::where('user_id', auth()->id())->count();
    }
}
