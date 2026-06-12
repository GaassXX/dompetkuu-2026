<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Saving extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'target_amount',
        'current_amount',
        'target_date',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'target_amount'  => 'decimal:2',
            'current_amount' => 'decimal:2',
            'target_date'    => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function deposits(): HasMany
    {
        return $this->hasMany(SavingDeposit::class);
    }

    public function getProgressPercentage(): float
    {
        if ($this->target_amount <= 0) return 0;
        return min(100, round(($this->current_amount / $this->target_amount) * 100, 1));
    }

    public function getRemainingAmount(): float
    {
        return max(0, (float)$this->target_amount - (float)$this->current_amount);
    }

    public function isCompleted(): bool
    {
        return (float)$this->current_amount >= (float)$this->target_amount;
    }
}
