<?php

namespace App\Filament\Admin\Resources\PanduanSectionResource\Pages;

use App\Filament\Admin\Resources\PanduanSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPanduanSections extends ListRecords
{
    protected static string $resource = PanduanSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
