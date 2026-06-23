<?php
namespace App\Filament\Parent\Auth;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
class Register extends BaseRegister
{
    protected static string $view = 'filament-panels::pages.auth.register';
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(User::class),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->confirmed(),
                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->required(),
                Select::make('account_type')
                    ->label('Daftar Sebagai')
                    ->options([
                        'parent'      => '👨‍👩‍👧 Orang Tua',
                        'independent' => '👤 Diri Sendiri',
                    ])
                    ->required()
                    ->native(false)
                    ->helperText('Orang Tua dapat mengelola keuangan anggota keluarga. Diri Sendiri untuk keuangan pribadi.'),
            ]);
    }
    public function register(): ?RegistrationResponse
    {
        $data = $this->form->getState();
        $isParent      = $data['account_type'] === 'parent';
        $isIndependent = $data['account_type'] === 'independent';

        // Kolom 'role' di tabel users: untuk label/tampilan & query ringan.
        // Akun pribadi diberi label 'personal' agar tidak disebut "anak".
        $roleLabel = match(true) {
            $isParent      => 'parent',
            $isIndependent => 'personal',
            default        => 'child',
        };

        // Spatie Permission role: TETAP 'parent' atau 'child' saja.
        // Akun pribadi tetap diberi role Spatie 'child' supaya semua
        // hasRole('child') check (akses panel /child, policy resource, dll)
        // tetap berfungsi tanpa perlu diubah di puluhan tempat.
        $spatieRole = $isParent ? 'parent' : 'child';

        $user = User::create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password'       => Hash::make($data['password']),
            'role'           => $roleLabel,
            'is_independent' => $isIndependent,
            'parent_id'      => null,
            'is_active'      => true,
        ]);
        $user->assignRole($spatieRole);
        event(new Registered($user));
        $this->auth->login($user);
        return app(RegistrationResponse::class);
    }
}
