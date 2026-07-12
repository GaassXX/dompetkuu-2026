<?php

namespace App\Filament\Child\Resources\ExpenseResource\Pages;

use App\Filament\Child\Resources\ExpenseResource;
use App\Notifications\TransactionPending;
use App\Services\BudgetAlertService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['amount'] = (int) str_replace(['.', ','], '', $data['amount'] ?? '0');
        $data['status'] = auth()->user()->parent_id ? 'pending' : 'approved';
        return $data;
    }

    protected function afterCreate(): void
    {
        $expense = $this->record;
        if ($expense->status === 'pending' && auth()->user()->parent_id) {
            auth()->user()->parent?->notify(new TransactionPending(
                childName: auth()->user()->name,
                type: 'Pengeluaran',
                amount: $expense->amount,
                description: $expense->description ?? '',
            ));
        }

        BudgetAlertService::checkAndNotify(auth()->id(), 90, true);
    }
}
