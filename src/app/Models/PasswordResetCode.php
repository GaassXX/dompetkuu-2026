<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetCode extends Model
{
    protected $fillable = [
        'email',
        'code',
        'is_used',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'is_used'    => 'boolean',
            'expired_at' => 'datetime',
        ];
    }
}
