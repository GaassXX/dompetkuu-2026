<?php

namespace App\Filament\Parent\Resources\DebtResource\Pages;

use App\Filament\Parent\Resources\DebtResource;
use Filament\Resources\Pages\ListRecords;

class ListDebts extends ListRecords
{
    protected static string $resource = DebtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make()
                ->label('Catat Utang/Piutang')
                ->icon('heroicon-o-plus'),
        ];
    }
}
