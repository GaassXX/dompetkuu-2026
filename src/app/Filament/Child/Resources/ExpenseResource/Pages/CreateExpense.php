<?php

namespace App\Filament\Child\Resources\ExpenseResource\Pages;

use App\Filament\Child\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;
}
