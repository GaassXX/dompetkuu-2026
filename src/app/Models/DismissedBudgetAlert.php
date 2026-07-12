<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DismissedBudgetAlert extends Model
{
    protected $fillable = [
        'user_id',
        'budget_id',
        'period',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }
}
