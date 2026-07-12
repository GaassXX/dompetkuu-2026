<?php

namespace App\Filament\Child\Resources\ExpenseResource\Pages;

use App\Filament\Child\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpense extends EditRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['amount'])) {
            $data['amount'] = (int) str_replace(['.', ','], '', $data['amount']);
        }
        return $data;
    }
}
