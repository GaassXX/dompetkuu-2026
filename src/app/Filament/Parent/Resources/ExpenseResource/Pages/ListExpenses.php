<?php

namespace App\Filament\Parent\Resources\ExpenseResource\Pages;

use App\Filament\Parent\Resources\ExpenseResource;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Carbon;

class ListExpenses extends Page
{
    protected static string $resource = ExpenseResource::class;
    protected static string $view     = 'filament.parent.resources.expense-resource.pages.list-expenses';

    public string $filterCategory = 'all';
    public string $filterPeriod   = 'this_month';

    public function getTitle(): string { return 'Pengeluaran'; }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('Catat Pengeluaran')
                ->icon('heroicon-o-plus')
                ->color('warning')
                ->url(\App\Filament\Parent\Resources\ExpenseResource::getUrl('create')),
        ];
    }

    public function setCategory(string $category): void
    {
        $this->filterCategory = $category;
    }

    public function setPeriod(string $period): void
    {
        $this->filterPeriod = $period;
    }

    public function getStats(): array
    {
        $userId = auth()->id();
        $now    = Carbon::now();

        // Period filter
        [$startDate, $endDate] = match($this->filterPeriod) {
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            'this_year'  => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default      => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };

        $totalThisMonth = Expense::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        // Compare bulan lalu
        $lastMonthStart  = $startDate->copy()->subMonth();
        $lastMonthEnd    = $endDate->copy()->subMonth();
        $totalLastMonth  = Expense::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereBetween('date', [$lastMonthStart, $lastMonthEnd])
            ->sum('amount');

        $percentChange = 0;
        $isHigher      = false;
        if ($totalLastMonth > 0) {
            $percentChange = round((($totalThisMonth - $totalLastMonth) / $totalLastMonth) * 100, 1);
            $isHigher      = $percentChange > 0;
        }

        // Kategori terboros
        $topCategory = Expense::where('user_id', $userId)
            ->where('status', 'approved')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->with('category')
            ->first();

        $topCategoryName  = $topCategory?->category?->name ?? '-';
        $topCategoryTotal = $topCategory?->total ?? 0;
        $topCategoryPct   = $totalThisMonth > 0
            ? round(($topCategoryTotal / $totalThisMonth) * 100)
            : 0;

        // Sisa anggaran parent
        $totalBudget = Budget::where('user_id', $userId)
    ->where('period', 'monthly')
    ->sum('limit_amount');

        $sisaAnggaran   = max(0, $totalBudget - $totalThisMonth);
        $budgetPct      = $totalBudget > 0
            ? round(($totalThisMonth / $totalBudget) * 100)
            : 0;
        $isOverBudget   = $totalThisMonth > $totalBudget && $totalBudget > 0;

        return compact(
            'totalThisMonth', 'totalLastMonth', 'percentChange', 'isHigher',
            'topCategoryName', 'topCategoryPct',
            'sisaAnggaran', 'totalBudget', 'budgetPct', 'isOverBudget'
        );
    }

    public function getExpenses()
    {
        $userId = auth()->id();
        $now    = Carbon::now();

        [$startDate, $endDate] = match($this->filterPeriod) {
            'this_month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'last_month' => [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()],
            'this_year'  => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default      => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };

        $query = Expense::where('user_id', $userId)
            ->with('category')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderByDesc('date');

        if ($this->filterCategory !== 'all') {
            $query->where('category_id', $this->filterCategory);
        }

        return $query->paginate(10);
    }

    public function getCategories()
    {
        return Category::where('type', 'expense')->get();
    }
    public function deleteExpense(int $id): void
{
    $expense = \App\Models\Expense::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    $expense->delete();
}

}
