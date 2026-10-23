<?php

namespace App\Filament\Parent\Resources\DebtResource\Pages;

use App\Filament\Parent\Resources\DebtResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDebt extends EditRecord
{
    protected static string $resource = DebtResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['amount'])) {
            $data['amount'] = (int) str_replace(['.', ','], '', $data['amount'] ?? '0');
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
