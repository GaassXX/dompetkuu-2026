<?php

namespace App\Filament\Child\Widgets;

use App\Models\Budget;
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
        $user   = auth()->user();

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

        $incomeChart  = $this->getMonthlyData(Income::class, $userId);
        $expenseChart = $this->getMonthlyData(Expense::class, $userId);

        $lastStat = $user->is_independent
            ? $this->getSisaAnggaranStat($userId)
            : $this->getPendingApprovalStat($userId);

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

            Stat::make('Saldo Bersih', 'Rp ' . number_format($saldo, 0, ',', '.'))
                ->description('Pemasukan - Pengeluaran')
                ->descriptionIcon('heroicon-m-wallet')
                ->color($saldo >= 0 ? 'success' : 'danger')
                ->chart($incomeChart),

            $lastStat,
        ];
    }

    // ✅ Hitung semua budget (semua kategori, semua periode)
    private function getSisaAnggaranStat(int $userId): Stat
    {
        $budgets = Budget::where('user_id', $userId)->get();

        if ($budgets->isEmpty()) {
            return Stat::make('Sisa Anggaran', 'Belum diset')
                ->description('Buat anggaran di menu Anggaran Saya')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('gray');
        }

        $totalLimit = (float) $budgets->sum('limit_amount');
        $totalSpent = (float) $budgets->sum(fn($b) => $b->getSpentAmount());
        $sisa       = max(0, $totalLimit - $totalSpent);
        $pct        = $totalLimit > 0 ? ($totalSpent / $totalLimit) * 100 : 0;

        return Stat::make('Sisa Anggaran', 'Rp ' . number_format($sisa, 0, ',', '.'))
            ->description(number_format($pct, 0) . '% anggaran terpakai ' . now()->translatedFormat('F Y'))
            ->descriptionIcon('heroicon-m-banknotes')
            ->color($pct >= 90 ? 'danger' : ($pct >= 75 ? 'warning' : 'success'));
    }

    // ✅ Untuk anak biasa: pending approval dari parent
    private function getPendingApprovalStat(int $userId): Stat
    {
        $pendingCount = Income::where('user_id', $userId)->where('status', 'pending')->count()
                      + Expense::where('user_id', $userId)->where('status', 'pending')->count();

        return Stat::make('Menunggu Persetujuan', $pendingCount)
            ->description('Transaksi pending')
            ->descriptionIcon('heroicon-m-clock')
            ->color($pendingCount > 0 ? 'warning' : 'success');
    }

    private function getMonthlyData(string $model, int $userId): array
    {
        return collect(range(5, 0))
            ->map(fn($i) => (float) $model::where('user_id', $userId)
                ->whereMonth('date', now()->startOfMonth()->subMonths($i)->month)
                ->whereYear('date', now()->startOfMonth()->subMonths($i)->year)
                ->where('status', 'approved')
                ->sum('amount')
            )->toArray();
    }
}
