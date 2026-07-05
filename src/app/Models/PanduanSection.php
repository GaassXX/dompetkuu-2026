<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PanduanSection extends Model
{
    protected $fillable = [
        'title',
        'content',
        'icon',
        'order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
