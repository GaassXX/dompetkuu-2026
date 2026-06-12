<?php

namespace App\Filament\Parent\Resources\SavingResource\Widgets;

use App\Models\Saving;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SavingStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $parentId = auth()->id();
        $childIds = User::where('parent_id', $parentId)->pluck('id')->toArray();
        $allIds   = array_merge([$parentId], $childIds);

        $savings       = Saving::whereIn('user_id', $allIds)->where('status', 'active')->get();
        $totalSaved    = $savings->sum('current_amount');
        $totalTarget   = $savings->sum('target_amount');
        $completed     = Saving::whereIn('user_id', $allIds)->where('status', 'completed')->count();
        $remaining     = max(0, $totalTarget - $totalSaved);

        return [
            Stat::make('Total Tabungan', 'Rp ' . number_format($totalSaved, 0, ',', '.'))
                ->description(count($savings) . ' tabungan aktif')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Target Tercapai', $completed . ' Target')
                ->description('Tabungan selesai')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),

            Stat::make('Sisa Target', 'Rp ' . number_format($remaining, 0, ',', '.'))
                ->description(Saving::whereIn('user_id', $allIds)->where('status', 'active')->count() . ' target tersisa')
                ->descriptionIcon('heroicon-m-flag')
                ->color('warning'),
        ];
    }
}
