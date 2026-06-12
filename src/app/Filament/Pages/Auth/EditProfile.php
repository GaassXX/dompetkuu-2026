<?php

namespace App\Filament\Pages\Auth;


use App\Models\Expense;
use App\Models\Income;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        $user        = auth()->user();
        $accountType = match(true) {
            $user->hasRole('parent')  => '👨‍👩‍👧 Orang Tua',
            $user->is_independent     => '👤 Akun Pribadi',
            $user->parent_id !== null => '🧒 Anggota Keluarga',
            default                   => '👤 Child',
        };

        $totalTx = Income::where('user_id', $user->id)->whereMonth('date', now()->month)->count()
                 + Expense::where('user_id', $user->id)->whereMonth('date', now()->month)->count();

        $totalIncome = Income::where('user_id', $user->id)
            ->whereMonth('date', now()->month)->whereYear('date', now()->year)
            ->where('status', 'approved')->sum('amount');

        return $form
            ->schema([

                // ===== FOTO: 3 kolom =====
                Forms\Components\Section::make('Foto Profil')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar_url')
                            ->label('')
                            ->directory('avatars')
                            ->image()
                            ->panelAspectRatio('1:1')
                            ->panelLayout('integrated')
                            ->optimize('webp')
                            ->helperText('JPG atau PNG. Maksimal 2MB.'),
                    ])
                    ->columnSpan(3),

                // ===== TENGAH: 6 kolom =====
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Informasi Dasar')
                            ->schema([
                                $this->getNameFormComponent()
                                    ->label('Nama Lengkap')
                                    ->inlineLabel(false),
                                $this->getEmailFormComponent()
                                    ->label('Email')
                                    ->inlineLabel(false),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Ganti Password')
                            ->description('Kosongkan jika tidak ingin mengubah password')
                            ->schema([
                                Forms\Components\TextInput::make('current_password')
                                    ->label('Password Lama')
                                    ->password()
                                    ->revealable()
                                    ->inlineLabel(false)
                                    ->columnSpanFull()
                                    ->required(false)
                                    ->requiredWith('password')
                                    ->rule('current_password')
                                    ->dehydrated(false),

                                $this->getPasswordFormComponent()
                                    ->label('Password Baru')
                                    ->inlineLabel(false)
                                    ->required(false)
                                    ->live(debounce: 500)
                                    ->dehydrated(fn ($state) => filled($state)),

                                $this->getPasswordConfirmationFormComponent()
                                    ->label('Konfirmasi Password')
                                    ->inlineLabel(false)
                                    ->required(false)
                                    ->requiredWith('password')
                                    ->dehydrated(false),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(6),

                // ===== KANAN: 3 kolom =====
                Forms\Components\Section::make('Detail Akun')
                    ->schema([
                        Forms\Components\Placeholder::make('account_type')
                            ->label('Tipe Akun')
                            ->content($accountType),

                        Forms\Components\Placeholder::make('joined_at')
                            ->label('Tanggal Bergabung')
                            ->content(fn() => auth()->user()->created_at?->translatedFormat('d M Y')),

                        Forms\Components\Placeholder::make('total_tx')
                            ->label('Total Pendapatan Bulan Ini')
                            ->content(new \Illuminate\Support\HtmlString("
                                <div style='display:flex;align-items:center;gap:8px;margin-top:4px;'>
                                    <div style='width:32px;height:32px;border-radius:50%;background:var(--color-primary-100);
                                                display:flex;align-items:center;justify-content:center;flex-shrink:0;'>
                                        <svg style='width:16px;height:16px;' fill='none' stroke='var(--color-primary-600)' viewBox='0 0 24 24'>
                                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'/>
                                        </svg>
                                    </div>
                                    <span style='font-size:18px;font-weight:700;color:var(--color-primary-600);'>
                                        Rp " . number_format($totalIncome, 0, ',', '.') . "
                                    </span>
                                </div>
                                <p style='font-size:12px;color:var(--color-text-secondary);margin-top:6px;'>{$totalTx} transaksi bulan ini</p>
                            ")),
                    ])
                    ->columnSpan(3),

            ])->columns(12);
    }

    public function getFormActionsAlignment(): string|Alignment
    {
        return Alignment::End;
    }
    public function getMaxWidth(): MaxWidth | string | null
    {
    return \Filament\Support\Enums\MaxWidth::FiveExtraLarge;
    }
}
