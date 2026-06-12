<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Expense;
use App\Models\User;
use Filament\Widgets\Widget;

class FamilyExpenseByCategory extends Widget
{
    protected static ?int $sort = 3; // Baris kedua, sebelah kanan
    protected static ?string $maxHeight = '300px'; // Selaraskan dengan FamilyFinanceChart

    public function getColumnSpan(): int | string | array
    {
        return 5;
    }

    protected static string $view = 'filament.parent.widgets.family-expense-by-category';
    protected int | string | array $columnSpan = 5;

    public array $categoryData  = [];
    public float $totalExpense  = 0;
    public string $currentMonth = '';

    // Filter bulan: 0 = bulan ini, 1 = bulan lalu, dst (5 bulan ke belakang)
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
        $parentId = auth()->id();
        $childIds = User::where('parent_id', $parentId)->pluck('id')->toArray();
        $allIds   = array_merge([$parentId], $childIds);

        $targetDate = now()->subMonths((int) $this->monthFilter);
        $this->currentMonth = $targetDate->translatedFormat('F Y');

        // 1. Ambil semua pengeluaran bulan terpilih yang di-approve
        $expenses = Expense::whereIn('user_id', $allIds)
            ->whereMonth('date', $targetDate->month)
            ->whereYear('date', $targetDate->year)
            ->where('status', 'approved')
            ->with('category')
            ->get();

        // 2. Kelompokkan per kategori, urutkan terbesar
        $grouped = $expenses->groupBy(fn($e) => $e->category->name ?? 'Tanpa Kategori')
            ->map(fn($items) => (float) $items->sum('amount'))
            ->sortByDesc(fn($v) => $v);

        // 3. Total = dari SEMUA kategori (bukan hanya top 5)
        $this->totalExpense = (float) $grouped->sum();

        if ($this->totalExpense <= 0) {
            $this->categoryData = [];
            return;
        }

        // 4. Ambil top 5, sisanya digabung jadi "Lainnya"
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

        // 5. Tambahkan baris "Lainnya" jika ada sisa kategori
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
