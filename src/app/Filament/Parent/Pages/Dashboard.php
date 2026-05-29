<?php

namespace App\Filament\Parent\Pages;

use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('tambah_transaksi')
                ->label(' Transaksi Baru')
                ->color('warning')
                ->icon('heroicon-o-plus')
                ->url(route('filament.parent.resources.incomes.create')),
        ];
    }

    public function getSubheading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return now()->translatedFormat('l, d F Y');
    }
}
