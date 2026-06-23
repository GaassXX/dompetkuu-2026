<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'avatar_url',
        'name',
        'email',
        'password',
        'role',
        'parent_id',
        'is_active',
        'is_independent',
        'telegram_chat_id',
        'telegram_pair_code',
        'telegram_disconnected_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'is_independent'             => 'boolean',
            'telegram_disconnected_at'   => 'datetime',
        ];
    }

    /**
     * Generate telegram pairing code otomatis saat user dibuat.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->telegram_pair_code)) {
                do {
                    $code = 'TG-' . Str::upper(Str::random(6));
                } while (
                    static::where('telegram_pair_code', $code)->exists()
                );

                $user->telegram_pair_code = $code;
            }
        });
    }

    /**
     * Avatar Filament.
     */
    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->avatar_url) {
            return asset('storage/' . $this->avatar_url);
        }

        $hash = md5(strtolower(trim($this->email)));

        return 'https://www.gravatar.com/avatar/' . $hash . '?d=mp&r=g&s=250';
    }

    /**
     * Hak akses panel Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin'  => $this->hasRole('super_admin') || $this->hasRole('admin'),
            'parent' => $this->hasRole('parent'),
            'child'  => $this->hasRole('child'),
            default  => false,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi
    |--------------------------------------------------------------------------
    */

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function savings(): HasMany
    {
        return $this->hasMany(Saving::class);
    }
}
