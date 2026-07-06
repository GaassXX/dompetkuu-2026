<?php

namespace App\Livewire\Mobile;

use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;

class Notifikasi extends Component
{
    public function markAsRead($id)
    {
        DatabaseNotification::where('id', $id)
            ->where('notifiable_id', auth()->id())
            ->update(['read_at' => now()]);
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
    }

    public function deleteAll()
    {
        auth()->user()->notifications()->delete();
    }

    public function render()
    {
        $notifications = auth()->user()->notifications()
            ->orderByDesc('created_at')
            ->paginate(20);

        $unreadCount = auth()->user()->unreadNotifications->count();

        return view('livewire.mobile.notifikasi', [
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ])->layout('layouts.mobile', ['title' => 'Notifikasi']);
    }
}
