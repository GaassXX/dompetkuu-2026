<?php

namespace App\Filament\Child\Widgets;

use App\Models\Expense;
use Filament\Widgets\Widget;

class ExpenseByCategory extends Widget
{
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '400px';
    protected static string $view = 'filament.child.widgets.expense-by-category';

    public function getColumnSpan(): int | string | array
    {
        return 5;
    }

    public array $categoryData = [];
    public float $totalExpense = 0;
    public string $currentMonth = '';

    public string $monthFilter = '0';

    public function mount(): void
    {
        $this->loadData();
    }

    public function updatedMonthFilter(): void
    {
        $this->loadData();
    }

    public function getMonthOptions(): array
    {
        return collect(range(0, 5))
            ->mapWithKeys(fn($i) => [
                (string) $i => now()->subMonths($i)->translatedFormat('F Y'),
            ])
            ->toArray();
    }

    private function loadData(): void
    {
        $userId = auth()->id();

        $targetDate = now()->subMonths((int) $this->monthFilter);
        $this->currentMonth = $targetDate->translatedFormat('F Y');

        $expenses = Expense::where('user_id', $userId)
            ->whereMonth('date', $targetDate->month)
            ->whereYear('date', $targetDate->year)
            ->where('status', 'approved')
            ->with('category')
            ->get();

        $grouped = $expenses->groupBy(fn($e) => $e->category->name ?? 'Tanpa Kategori')
            ->map(fn($items) => (float) $items->sum('amount'))
            ->sortByDesc(fn($v) => $v);

        $this->totalExpense = (float) $grouped->sum();

        if ($this->totalExpense <= 0) {
            $this->categoryData = [];
            return;
        }

        $top5      = $grouped->take(5);
        $remainder = $grouped->skip(5)->sum();

        $colors = ['#EF4444', '#F97316', '#EAB308', '#22C55E', '#3B82F6'];
        $i = 0;

        $data = $top5->map(function ($amount, $name) use ($colors, &$i) {
            $pct = $this->totalExpense > 0 ? round(($amount / $this->totalExpense) * 100) : 0;
            return [
                'name'   => $name,
                'amount' => $amount,
                'pct'    => $pct,
                'color'  => $colors[$i++] ?? '#6B7280',
            ];
        })->values()->toArray();

        if ($remainder > 0) {
            $pct = round(($remainder / $this->totalExpense) * 100);
            $data[] = [
                'name'   => 'Lainnya',
                'amount' => $remainder,
                'pct'    => $pct,
                'color'  => '#6B7280',
            ];
        }

        $this->categoryData = $data;
    }
}
