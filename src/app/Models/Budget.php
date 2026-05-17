<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'limit_amount',
        'period',
    ];

    protected function casts(): array
    {
        return [
            'limit_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ✅ Hitung total pengeluaran sesuai periode
    public function getSpentAmount(): float
    {
        $query = Expense::where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->where('status', 'approved');

        if ($this->period === 'monthly') {
            $query->whereMonth('date', now()->month)
                  ->whereYear('date', now()->year);
        } elseif ($this->period === 'weekly') {
            $query->whereBetween('date', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ]);
        }

        return (float) $query->sum('amount');
    }

    // ✅ Hitung persentase penggunaan
    public function getUsagePercentage(): float
    {
        if ($this->limit_amount <= 0) return 0;
        return ($this->getSpentAmount() / (float) $this->limit_amount) * 100;
    }

    // ✅ Cek apakah sudah >= 90%
    public function isNearLimit(int $threshold = 90): bool
    {
        return $this->getUsagePercentage() >= $threshold;
    }
}
