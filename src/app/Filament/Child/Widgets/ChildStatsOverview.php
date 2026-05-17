<?php

namespace App\Filament\Child\Widgets;

use App\Models\Expense;
use App\Models\Income;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ChildStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $userId = auth()->id();

        $totalIncome = Income::where('user_id', $userId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'approved')
            ->sum('amount');

        $totalExpense = Expense::where('user_id', $userId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'approved')
            ->sum('amount');

        $saldo = $totalIncome - $totalExpense;

        $pendingCount = Income::where('user_id', $userId)
            ->where('status', 'pending')
            ->count()
            +
            Expense::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        return [
            Stat::make('Pemasukan Bulan Ini', 'Rp ' . number_format($totalIncome, 0, ',', '.'))
                ->description('Bulan ' . now()->format('F Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pengeluaran Bulan Ini', 'Rp ' . number_format($totalExpense, 0, ',', '.'))
                ->description('Bulan ' . now()->format('F Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Saldo', 'Rp ' . number_format($saldo, 0, ',', '.'))
                ->description('Pemasukan - Pengeluaran')
                ->descriptionIcon('heroicon-m-wallet')
                ->color($saldo >= 0 ? 'success' : 'danger'),

            Stat::make('Menunggu Persetujuan', $pendingCount)
                ->description('Transaksi pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
