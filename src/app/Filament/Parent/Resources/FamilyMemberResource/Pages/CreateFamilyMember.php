<?php

namespace App\Filament\Parent\Resources\FamilyMemberResource\Pages;

use App\Filament\Parent\Resources\FamilyMemberResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CreateFamilyMember extends Page
{
    protected static string $resource = FamilyMemberResource::class;

    protected static string $view = 'filament.parent.resources.family-member-resource.pages.create-family-member';

    public ?string $name = '';
    public ?string $email = '';
    public ?string $password = '';
    public bool $showPassword = false;

    public function getTitle(): string
    {
        return 'Create Anggota';
    }

    public function getHeading(): string
    {
        return '';
    }

    public function togglePassword(): void
    {
        $this->showPassword = !$this->showPassword;
    }

    public function save(): void
    {
        $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique(User::class, 'email')],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $member = User::create([
            'name'           => $this->name,
            'email'          => $this->email,
            'password'       => Hash::make($this->password),
            'parent_id'      => auth()->id(),
            'role'           => 'child',
            'is_active'      => true,
            'is_independent' => false,
        ]);

        $member->assignRole('child');

        Notification::make()
            ->title('Anggota berhasil ditambahkan')
            ->success()
            ->send();

        $this->redirect(FamilyMemberResource::getUrl('index'));
    }

    public function saveAndCreateAnother(): void
    {
        $this->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique(User::class, 'email')],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $member = User::create([
            'name'           => $this->name,
            'email'          => $this->email,
            'password'       => Hash::make($this->password),
            'parent_id'      => auth()->id(),
            'role'           => 'child',
            'is_active'      => true,
            'is_independent' => false,
        ]);

        $member->assignRole('child');

        Notification::make()
            ->title('Anggota berhasil ditambahkan')
            ->success()
            ->send();

        $this->name = '';
        $this->email = '';
        $this->password = '';
    }

    public function cancel(): void
    {
        $this->redirect(FamilyMemberResource::getUrl('index'));
    }
}
