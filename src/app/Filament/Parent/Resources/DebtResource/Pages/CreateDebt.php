<?php

namespace App\Filament\Parent\Resources\DebtResource\Pages;

use App\Filament\Parent\Resources\DebtResource;
use App\Models\Debt;
use Filament\Resources\Pages\CreateRecord;

class CreateDebt extends CreateRecord
{
    protected static string $resource = DebtResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['amount'] = (int) str_replace(['.', ','], '', $data['amount'] ?? '0');
        $data['status'] = Debt::STATUS_UNPAID;
        $data['paid_at'] = null;

        return $data;
    }
}
