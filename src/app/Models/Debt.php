<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Debt extends Model
{
    public const TYPE_DEBT = 'debt';           // Utang saya ke orang lain
    public const TYPE_RECEIVABLE = 'receivable'; // Piutang (orang lain utang ke saya)

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PAID = 'paid';

    protected $fillable = [
        'user_id',
        'type',
        'person_name',
        'amount',
        'date',
        'due_date',
        'description',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date' => 'date',
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isOverdue(): bool
    {
        return ! $this->isPaid()
            && $this->due_date !== null
            && $this->due_date->isPast();
    }

    /**
     * Lunasi utang/piutang.
     *
     * Mencatat arus kas otomatis:
     * - type `debt` (bayar utang) -> Expense
     * - type `receivable` (terima piutang) -> Income
     *
     * Mengikuti aturan approval yang berlaku: Expense milik anak
     * yang punya parent berstatus `pending`, selain itu `approved`.
     *
     * @throws ValidationException jika sudah lunas.
     */
    public function markAsPaid(): self
    {
        if ($this->isPaid()) {
            throw ValidationException::withMessages([
                'status' => 'Utang/piutang ini sudah lunas.',
            ]);
        }

        DB::transaction(function () {
            $user = $this->user;

            if ($this->type === self::TYPE_DEBT) {
                $category = Category::where('type', 'expense')
                    ->where('name', 'Lainnya')
                    ->first()
                    ?? Category::where('type', 'expense')->firstOrFail();

                Expense::create([
                    'user_id' => $this->user_id,
                    'category_id' => $category->id,
                    'debt_id' => $this->id,
                    'amount' => $this->amount,
                    'description' => "Bayar utang ke {$this->person_name}"
                        . ($this->description ? " — {$this->description}" : ''),
                    'date' => now(),
                    'status' => $user->parent_id ? 'pending' : 'approved',
                ]);
            } else {
                $category = Category::where('type', 'income')
                    ->where('name', 'Lainnya')
                    ->first()
                    ?? Category::where('type', 'income')->firstOrFail();

                Income::create([
                    'user_id' => $this->user_id,
                    'category_id' => $category->id,
                    'debt_id' => $this->id,
                    'amount' => $this->amount,
                    'description' => "Terima piutang dari {$this->person_name}"
                        . ($this->description ? " — {$this->description}" : ''),
                    'date' => now(),
                    'status' => 'approved',
                ]);
            }

            $this->update([
                'status' => self::STATUS_PAID,
                'paid_at' => now(),
            ]);
        });

        return $this->refresh();
    }
}
