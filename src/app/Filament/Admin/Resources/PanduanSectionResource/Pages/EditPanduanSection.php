<?php

namespace App\Filament\Admin\Resources\PanduanSectionResource\Pages;

use App\Filament\Admin\Resources\PanduanSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPanduanSection extends EditRecord
{
    protected static string $resource = PanduanSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
