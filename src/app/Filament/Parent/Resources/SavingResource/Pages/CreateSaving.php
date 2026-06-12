<?php

namespace App\Filament\Parent\Resources\SavingResource\Pages;

use App\Filament\Parent\Resources\SavingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSaving extends CreateRecord
{
    protected static string $resource = SavingResource::class;

    public function getTitle(): string
    {
        return 'Tambah Tabungan Baru';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['status']  = 'active';
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
