<?php

namespace App\Filament\Child\Resources\BudgetResource\Pages;

use App\Filament\Child\Resources\BudgetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;

    protected function getRedirectUrl(): string
    {
        return \App\Filament\Child\Pages\BudgetView::getUrl();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['limit_amount'] = (int) str_replace(['.', ','], '', $data['limit_amount'] ?? '0');
        return $data;
    }
}
