<?php

namespace App\Observers;

use App\Models\Expense;
use App\Notifications\TransactionStatusNotification;

class ExpenseObserver
{
    public function updated(Expense $expense): void
    {
        if (!$expense->wasChanged('status')) return;

        $user = $expense->user;
        if (is_null($user->parent_id)) return;

        $user->notify(new TransactionStatusNotification(
            type: 'Pengeluaran',
            category: $expense->category->name ?? '-',
            amount: $expense->amount,
            status: $expense->status,
        ));
    }
}
