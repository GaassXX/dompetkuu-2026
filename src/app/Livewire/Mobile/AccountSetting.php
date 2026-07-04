<?php

namespace App\Livewire\Mobile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;

class AccountSetting extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $avatar;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updatedAvatar()
    {
        $this->validate([
            'avatar' => 'image|max:1024',
        ]);
    }

    public function save()
    {
        $user = Auth::user();

        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $data = ['name' => $this->name];

        if ($this->avatar) {
            $this->validate(['avatar' => 'image|max:1024']);
            $path = $this->avatar->store('avatars', 'public');
            $data['avatar_url'] = $path;
        }

        if ($this->current_password || $this->new_password) {
            $this->validate([
                'current_password' => 'required|current_password',
                'new_password' => 'required|string|min:8|confirmed',
            ]);
            $data['password'] = Hash::make($this->new_password);
        }

        $user->update($data);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation', 'avatar']);

        session()->flash('success', 'Pengaturan berhasil disimpan.');
    }

    public function render()
    {
        return view('livewire.mobile.account-setting')
            ->layout('layouts.mobile');
    }
}
