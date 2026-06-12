<?php

namespace App\Filament\Child\Resources\SavingResource\Pages;

use App\Filament\Child\Resources\SavingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSaving extends CreateRecord
{
    protected static string $resource = SavingResource::class;

    // ✅ Inject user_id sebelum save
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return SavingResource::getUrl('index');
    }
}
