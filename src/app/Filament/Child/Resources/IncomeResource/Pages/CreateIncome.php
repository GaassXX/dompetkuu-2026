<?php

namespace App\Filament\Child\Resources\IncomeResource\Pages;

use App\Filament\Child\Resources\IncomeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIncome extends CreateRecord
{
    protected static string $resource = IncomeResource::class;
}
