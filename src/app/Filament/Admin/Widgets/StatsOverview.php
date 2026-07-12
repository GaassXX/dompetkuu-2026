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
        $totalUsers     = User::whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['super_admin', 'admin']))->count();
        $totalParents   = User::whereHas('roles', fn($q) => $q->where('name', 'parent'))->count();
        $totalChildren  = User::whereHas('roles', fn($q) => $q->where('name', 'child'))->where('is_independent', false)->count();
        $totalPersonal  = User::where('is_independent', true)->count();
        $activeUsers    = User::where('is_active', true)->whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['super_admin', 'admin']))->count();
        $inactiveUsers  = User::where('is_active', false)->whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['super_admin', 'admin']))->count();

        return [
            Stat::make('Total User', $totalUsers)
                ->description('Orang Tua: ' . $totalParents . ' · Anak: ' . $totalChildren . ' · Personal: ' . $totalPersonal)
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Akun Aktif', $activeUsers)
                ->description('Nonaktif: ' . $inactiveUsers)
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
