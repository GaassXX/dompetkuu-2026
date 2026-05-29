<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ParentStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $parentId = auth()->id();

        // ===== Keuangan Saya =====
        $myIncome = (float) Income::where('user_id', $parentId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'approved')
            ->sum('amount');

        $myExpense = (float) Expense::where('user_id', $parentId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'approved')
            ->sum('amount');

        $mySaldo = $myIncome - $myExpense;

        // ===== Keuangan Keluarga =====
        $childIds = User::where('parent_id', $parentId)->pluck('id')->toArray();
        $allIds   = array_merge([$parentId], $childIds);

        $familyIncome = (float) Income::whereIn('user_id', $allIds)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'approved')
            ->sum('amount');

        $familyExpense = (float) Expense::whereIn('user_id', $allIds)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'approved')
            ->sum('amount');

        $familySaldo = $familyIncome - $familyExpense;

        // ===== Sparkline 6 bulan =====
        $myIncomeChart = $this->getMonthlyData(Income::class, [$parentId]);
        $myExpenseChart = $this->getMonthlyData(Expense::class, [$parentId]);
        $familyChart   = $this->getMonthlyData(Income::class, $allIds);

        return [
            Stat::make('Pemasukan Saya', 'Rp ' . number_format($myIncome, 0, ',', '.'))
                ->description('Bulan ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($myIncomeChart),

            Stat::make('Pengeluaran Saya', 'Rp ' . number_format($myExpense, 0, ',', '.'))
                ->description('Bulan ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart($myExpenseChart),

            Stat::make('Saldo Saya', 'Rp ' . number_format($mySaldo, 0, ',', '.'))
                ->description('Pemasukan - Pengeluaran')
                ->descriptionIcon('heroicon-m-wallet')
                ->color($mySaldo >= 0 ? 'success' : 'danger')
                ->chart($myIncomeChart),

            Stat::make('Total Keluarga', 'Rp ' . number_format($familySaldo, 0, ',', '.'))
                ->description('Saldo gabungan ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-home')
                ->color($familySaldo >= 0 ? 'info' : 'warning')
                ->chart($familyChart),
        ];
    }

    // ✅ Ambil data 6 bulan terakhir untuk sparkline
    private function getMonthlyData(string $model, array $userIds): array
    {
        return collect(range(5, 0))
            ->map(fn($i) => (float) $model::whereIn('user_id', $userIds)
                ->whereMonth('date', now()->subMonths($i)->month)
                ->whereYear('date', now()->subMonths($i)->year)
                ->where('status', 'approved')
                ->sum('amount')
            )->toArray();
    }
}
