<?php

namespace App\Filament\Child\Widgets;

use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ChildStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $userId = auth()->id();

        $totalIncome = (float) Income::where('user_id', $userId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'approved')
            ->sum('amount');

        $totalExpense = (float) Expense::where('user_id', $userId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'approved')
            ->sum('amount');

        $saldo = $totalIncome - $totalExpense;

        $pendingCount = Income::where('user_id', $userId)->where('status', 'pending')->count()
                      + Expense::where('user_id', $userId)->where('status', 'pending')->count();

        $incomeChart  = $this->getMonthlyData(Income::class, $userId);
        $expenseChart = $this->getMonthlyData(Expense::class, $userId);

        return [
            Stat::make('Pemasukan Bulan Ini', 'Rp ' . number_format($totalIncome, 0, ',', '.'))
                ->description('Bulan ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($incomeChart),

            Stat::make('Pengeluaran Bulan Ini', 'Rp ' . number_format($totalExpense, 0, ',', '.'))
                ->description('Bulan ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart($expenseChart),

            Stat::make('Saldo', 'Rp ' . number_format($saldo, 0, ',', '.'))
                ->description('Pemasukan - Pengeluaran')
                ->descriptionIcon('heroicon-m-wallet')
                ->color($saldo >= 0 ? 'success' : 'danger')
                ->chart($incomeChart),

            Stat::make('Menunggu Persetujuan', $pendingCount)
                ->description('Transaksi pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingCount > 0 ? 'warning' : 'success'),
        ];
    }

    private function getMonthlyData(string $model, int $userId): array
    {
        return collect(range(5, 0))
            ->map(fn($i) => (float) $model::where('user_id', $userId)
                ->whereMonth('date', now()->subMonths($i)->month)
                ->whereYear('date', now()->subMonths($i)->year)
                ->where('status', 'approved')
                ->sum('amount')
            )->toArray();
    }
}
