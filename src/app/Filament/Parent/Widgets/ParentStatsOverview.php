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

    protected function getStats(): array
    {
        $parentId = auth()->id();

        // ===== Keuangan Orangtua Sendiri =====
        $myIncome = Income::where('user_id', $parentId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'approved')
            ->sum('amount');

        $myExpense = Expense::where('user_id', $parentId)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'approved')
            ->sum('amount');

        $mySaldo = $myIncome - $myExpense;

        // ===== Keuangan Seluruh Keluarga =====
        $childIds = User::where('parent_id', $parentId)
            ->pluck('id')
            ->toArray();

        $allIds = array_merge([$parentId], $childIds);

        $familyIncome = Income::whereIn('user_id', $allIds)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'approved')
            ->sum('amount');

        $familyExpense = Expense::whereIn('user_id', $allIds)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'approved')
            ->sum('amount');

        $familySaldo = $familyIncome - $familyExpense;

        return [
            // Keuangan Saya
            Stat::make('Pemasukan Saya', 'Rp ' . number_format($myIncome, 0, ',', '.'))
                ->description('Bulan ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Pengeluaran Saya', 'Rp ' . number_format($myExpense, 0, ',', '.'))
                ->description('Bulan ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make('Saldo Saya', 'Rp ' . number_format($mySaldo, 0, ',', '.'))
                ->description('Pemasukan - Pengeluaran')
                ->descriptionIcon('heroicon-m-wallet')
                ->color($mySaldo >= 0 ? 'success' : 'danger'),

            // Keuangan Keluarga
            Stat::make('Total Keluarga', 'Rp ' . number_format($familySaldo, 0, ',', '.'))
                ->description('Saldo gabungan ' . now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-home')
                ->color($familySaldo >= 0 ? 'info' : 'warning'),
        ];
    }
}
