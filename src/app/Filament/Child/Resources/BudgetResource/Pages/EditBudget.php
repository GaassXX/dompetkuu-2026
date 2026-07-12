<?php

namespace App\Filament\Child\Resources\BudgetResource\Pages;

use App\Filament\Child\Resources\BudgetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBudget extends EditRecord
{
    protected static string $resource = BudgetResource::class;

    protected function getRedirectUrl(): string
    {
        return \App\Filament\Child\Pages\BudgetView::getUrl();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['limit_amount'])) {
            $data['limit_amount'] = (int) str_replace(['.', ','], '', $data['limit_amount']);
        }
        return $data;
    }
}
