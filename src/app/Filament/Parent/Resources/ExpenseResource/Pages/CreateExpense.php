<?php

namespace App\Filament\Parent\Resources\ExpenseResource\Pages;

use App\Filament\Parent\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;
}
