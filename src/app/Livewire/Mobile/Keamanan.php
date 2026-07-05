<?php

namespace App\Livewire\Mobile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Keamanan extends Component
{
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    public function updatePassword()
    {
        $this->validate([
            'current_password'          => 'required|current_password',
            'new_password'              => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        Auth::user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        session()->flash('success', 'Password berhasil diubah.');
    }

    public function render()
    {
        $user = Auth::user();

        return view('livewire.mobile.keamanan', [
            'user' => $user,
        ])->layout('layouts.mobile', ['title' => 'Keamanan']);
    }
}
