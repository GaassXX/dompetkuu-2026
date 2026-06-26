<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalUsers     = User::count();
        $totalParents   = User::where('role', 'parent')->count();
        $totalChildren  = User::where('role', 'child')->count();
        $totalPersonal  = User::where('role', 'personal')->count();
        $activeUsers    = User::where('is_active', true)->count();
        $inactiveUsers  = User::where('is_active', false)->count();

        return [
            Stat::make('Total User', $totalUsers)
                ->description('Parent: ' . $totalParents . ' · Child: ' . $totalChildren . ' · Personal: ' . $totalPersonal)
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Akun Aktif', $activeUsers)
                ->description('Nonaktif: ' . $inactiveUsers)
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
