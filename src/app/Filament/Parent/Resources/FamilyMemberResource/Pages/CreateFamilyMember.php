<?php

namespace App\Filament\Parent\Resources\FamilyMemberResource\Pages;

use App\Filament\Parent\Resources\FamilyMemberResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFamilyMember extends CreateRecord
{
    protected static string $resource = FamilyMemberResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['parent_id']      = auth()->id();
        $data['role']           = 'child';
        $data['is_active']      = true;
        $data['is_independent'] = false;

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->assignRole('child');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
