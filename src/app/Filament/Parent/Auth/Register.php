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

        $user = User::create([
            'name'           => $data['name'],
            'email'          => $data['email'],
            'password'       => Hash::make($data['password']),
            'role'           => $isParent ? 'parent' : 'child',
            'is_independent' => $isIndependent,
            'parent_id'      => null,
            'is_active'      => true,
        ]);

        $user->assignRole($isParent ? 'parent' : 'child');

        event(new Registered($user));

        $this->auth->login($user);

        return app(RegistrationResponse::class);
    }
}
