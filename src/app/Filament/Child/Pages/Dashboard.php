<?php

namespace App\Filament\Child\Pages;

use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('tambah_pemasukan')
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

    // 6 kolom di layar besar biar bisa dibagi 2/3+1/3 dan 1/2+1/2
    public function getColumns(): int|string|array
    {
        return [
            'default' => 1,
            'md'      => 2,
            'lg'      => 6,
        ];
    }
}
