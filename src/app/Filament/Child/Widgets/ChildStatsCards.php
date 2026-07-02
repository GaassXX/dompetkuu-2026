<?php

namespace App\Filament\Child\Widgets;

use App\Models\Budget;
use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\Widget;

class ChildStatsCards extends Widget
{
    protected static ?int $sort = 1;

    protected static string $view = 'filament.child.widgets.child-stats-cards';

    // Biar span-nya bisa dipakai di getColumnSpan() Dashboard
    protected int|string|array $columnSpan = [
        'default' => 1,
        'md'      => 2,
        'lg'      => 6,
    ];

    public array $cards = [];

    public function mount(): void
    {
        $this->cards = $this->buildCards();
    }

    private function buildCards(): array
    {
        $userId = auth()->id();

        $thisMonth = now();
        $lastMonth = now()->subMonthNoOverflow();

        $incomeThis  = $this->sumApproved(Income::class, $userId, $thisMonth);
        $incomeLast  = $this->sumApproved(Income::class, $userId, $lastMonth);

        $expenseThis = $this->sumApproved(Expense::class, $userId, $thisMonth);
        $expenseLast = $this->sumApproved(Expense::class, $userId, $lastMonth);

        $saldoThis = $incomeThis - $expenseThis;
        $saldoLast = $incomeLast - $expenseLast;

        [$sisaAnggaran, $pctAnggaranTerpakai] = $this->getBudgetSummary($userId);

        return [
            [
                'label'       => 'Pemasukan',
                'sub'         => 'Bulan ini',
                'amount'      => $incomeThis,
                'trend'       => $this->trendPercent($incomeThis, $incomeLast),
                'icon'        => 'heroicon-o-arrow-down-tray',
                'bg'          => '#DCFCE7',
                'iconColor'   => '#16A34A',
                'amountColor' => '#16A34A',
            ],
            [
                'label'       => 'Pengeluaran',
                'sub'         => 'Bulan ini',
                'amount'      => $expenseThis,
                'trend'       => $this->trendPercent($expenseThis, $expenseLast),
                'icon'        => 'heroicon-o-arrow-up-tray',
                'bg'          => '#FEE2E2',
                'iconColor'   => '#DC2626',
                'amountColor' => '#DC2626',
            ],
            [
                'label'       => 'Saldo',
                'sub'         => 'Pemasukan - Pengeluaran',
                'amount'      => $saldoThis,
                'trend'       => $this->trendPercent($saldoThis, $saldoLast),
                'icon'        => 'heroicon-o-wallet',
                'bg'          => '#FFEDD5',
                'iconColor'   => '#EA580C',
                'amountColor' => '#111827',
            ],
            [
                'label'       => 'Anggaran',
                'sub'         => 'Sisa anggaran',
                'amount'      => $sisaAnggaran,
                'trend'       => (int) round(100 - $pctAnggaranTerpakai),
                'icon'        => 'heroicon-o-flag',
                'bg'          => '#EDE9FE',
                'iconColor'   => '#7C3AED',
                'amountColor' => '#111827',
            ],
        ];
    }

    private function sumApproved(string $model, int $userId, \Carbon\Carbon $date): float
    {
        return (float) $model::where('user_id', $userId)
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->where('status', 'approved')
            ->sum('amount');
    }

    private function trendPercent(float $current, float $previous): int
    {
        if ($previous <= 0) {
            return $current > 0 ? 100 : 0;
        }

        return (int) round((($current - $previous) / $previous) * 100);
    }

    private function getBudgetSummary(int $userId): array
    {
        $budgets = Budget::where('user_id', $userId)->get();

        if ($budgets->isEmpty()) {
            return [0, 0];
        }

        $totalLimit = (float) $budgets->sum('limit_amount');
        $totalSpent = (float) $budgets->sum(fn ($b) => $b->getSpentAmount());
        $sisa       = max(0, $totalLimit - $totalSpent);
        $pct        = $totalLimit > 0 ? ($totalSpent / $totalLimit) * 100 : 0;

        return [$sisa, $pct];
    }
}
