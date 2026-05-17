<?php

namespace App\Filament\Parent\Resources\FamilyMemberResource\Pages;

use App\Filament\Parent\Resources\FamilyMemberResource;
use Filament\Resources\Pages\EditRecord;

class EditFamilyMember extends EditRecord
{
    protected static string $resource = FamilyMemberResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
