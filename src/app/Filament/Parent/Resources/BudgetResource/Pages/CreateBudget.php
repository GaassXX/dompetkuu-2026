<?php

namespace App\Filament\Parent\Resources\BudgetResource\Pages;

use App\Filament\Parent\Resources\BudgetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBudget extends CreateRecord
{
    protected static string $resource = BudgetResource::class;
}
