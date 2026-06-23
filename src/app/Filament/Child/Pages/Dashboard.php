<?php

namespace App\Filament\Child\Pages;

use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Child\Widgets\ChildStatsOverview;
use App\Filament\Child\Widgets\ChildFinanceChart;
use App\Filament\Child\Widgets\ExpenseByCategory;
use App\Filament\Child\Widgets\LatestTransactions;
use App\Filament\Child\Widgets\BudgetOverview;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    public function getColumns(): int | string | array
    {
        return 12;
    }

    public function getWidgets(): array
    {
        return [
            ChildStatsOverview::class,
            ChildFinanceChart::class,
            ExpenseByCategory::class,
            LatestTransactions::class,
            BudgetOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('tambah_transaksi')
                ->label(' Transaksi Baru')
                ->color('warning')
                ->icon('heroicon-o-plus')
                ->url(route('filament.child.resources.incomes.create')),
        ];
    }

    public function getSubheading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return now()->translatedFormat('l, d F Y');
    }
}
