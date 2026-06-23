<?php

namespace App\Filament\Pages\Auth;


use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Str;

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

                        Forms\Components\Section::make('Koneksi Telegram')
                            ->description('Hubungkan akun Anda dengan Bot Telegram untuk input transaksi cepat.')
                            ->schema([
                                Forms\Components\Placeholder::make('telegram_status')
                                    ->label('Status')
                                    ->content(function () use ($user) {
                                        if ($user->telegram_chat_id) {
                                            return new \Illuminate\Support\HtmlString(
                                                "<span style='display:inline-flex;align-items:center;gap:6px;color:#16A34A;font-weight:600;font-size:13px;'>
                                                    <span style='width:8px;height:8px;border-radius:50%;background:#16A34A;'></span>
                                                    Terhubung
                                                </span>"
                                            );
                                        }
                                        if ($user->telegram_disconnected_at) {
                                            return new \Illuminate\Support\HtmlString(
                                                "<span style='display:inline-flex;align-items:center;gap:6px;color:#D97706;font-weight:600;font-size:13px;'>
                                                    <span style='width:8px;height:8px;border-radius:50%;background:#D97706;'></span>
                                                    Tidak Aktif
                                                </span>"
                                            );
                                        }
                                        return new \Illuminate\Support\HtmlString(
                                            "<span style='display:inline-flex;align-items:center;gap:6px;color:#DC2626;font-weight:600;font-size:13px;'>
                                                <span style='width:8px;height:8px;border-radius:50%;background:#DC2626;'></span>
                                                Belum Terhubung
                                            </span>"
                                        );
                                    }),

                                Forms\Components\Placeholder::make('telegram_pair_code')
                                    ->label('Kode Pairing Anda')
                                    ->content(function () use ($user) {
                                        return new \Illuminate\Support\HtmlString(
                                            "<div style='display:flex;align-items:center;gap:10px;'>
                                                <code style='font-size:16px;font-weight:700;padding:6px 14px;border-radius:8px;background:var(--color-background-secondary,#f3f4f6);letter-spacing:1px;'>
                                                    {$user->telegram_pair_code}
                                                </code>
                                            </div>"
                                        );
                                    }),

                                Actions::make([
                                    Action::make('reset_telegram')
                                        ->label('Putuskan Koneksi Telegram')
                                        ->color('danger')
                                        ->icon('heroicon-o-link-slash')
                                        ->visible(fn () => auth()->user()->telegram_chat_id !== null)
                                        ->requiresConfirmation()
                                        ->modalHeading('Putuskan Koneksi Telegram?')
                                        ->modalDescription('Bot Telegram akan terputus. Anda bisa menghubungkan kembali dengan kode pairing yang baru.')
                                        ->action(function () {
                                            $user = auth()->user();
                                            do {
                                                $code = 'TG-' . Str::upper(Str::random(6));
                                            } while (User::where('telegram_pair_code', $code)->exists());

                                            $user->update([
                                                'telegram_chat_id' => null,
                                                'telegram_disconnected_at' => now(),
                                                'telegram_pair_code' => $code,
                                            ]);

                                            Notification::make()
                                                ->title('Koneksi Telegram diputuskan')
                                                ->body('Kode pairing baru telah dibuat. Gunakan kode baru untuk menghubungkan kembali.')
                                                ->success()
                                                ->send();

                                            $this->redirect(request()->url());
                                        }),
                                ])
                                    ->columnSpanFull(),

                                Forms\Components\Placeholder::make('telegram_instructions')
                                    ->label('Cara Menghubungkan')
                                    ->content(new \Illuminate\Support\HtmlString(
                                        "<ol style='font-size:13px;color:var(--color-text-secondary);line-height:1.8;margin:0;padding-left:18px;'>
                                            <li>Buka Telegram, cari bot <strong>@dompettkuu_bot</strong></li>
                                            <li>Tekan <strong>/Start</strong> lalu kirim pesan: <code>/connect KODE_ANDA</code></li>
                                            <li>Setelah berhasil, status akan berubah jadi <strong>Terhubung</strong></li>
                                            <li>Kirim transaksi contoh: <code>masuk 500000 gaji bulan mei</code></li>
                                        </ol>"
                                    ))
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
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
