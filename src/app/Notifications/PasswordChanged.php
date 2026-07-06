<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasswordChanged extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icon'    => 'heroicon-o-shield-check',
            'title'   => 'Password Diubah',
            'message' => 'Password akun Anda berhasil diubah pada ' . now()->translatedFormat('d M Y H:i'),
            'type'    => 'security',
        ];
    }
}
