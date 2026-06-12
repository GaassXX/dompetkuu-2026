<?php

namespace App\Observers;

use App\Models\Income;
use App\Notifications\TransactionStatusNotification;

class IncomeObserver
{
    public function updated(Income $income): void
    {
        if (!$income->wasChanged('status')) return;

        $user = $income->user;
        if (is_null($user->parent_id)) return;

        $user->notify(new TransactionStatusNotification(
            type: 'Pemasukan',
            category: $income->category->name ?? '-',
            amount: $income->amount,
            status: $income->status,
        ));
    }
}
