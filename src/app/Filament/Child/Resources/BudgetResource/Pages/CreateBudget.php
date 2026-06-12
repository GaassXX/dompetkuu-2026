<?php

namespace App\Filament\Child\Resources\BudgetResource\Pages;

use App\Filament\Child\Resources\BudgetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;
}
