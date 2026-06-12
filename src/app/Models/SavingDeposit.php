<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavingDeposit extends Model
{
    protected $fillable = [
        'saving_id',
        'user_id',
        'amount',
        'date',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date'   => 'date',
        ];
    }

    public function savingGoal(): BelongsTo
    {
        return $this->belongsTo(Saving::class, 'saving_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
