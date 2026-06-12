<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class TransactionStatusNotification extends Notification
{
    public function __construct(
        public string $type,
        public string $category,
        public float  $amount,
        public string $status,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        $amount = 'Rp ' . number_format($this->amount, 0, ',', '.');

        $title = match ($this->status) {
            'approved' => " {$this->type} Disetujui",
            'rejected' => " {$this->type} Ditolak",
            default    => " {$this->type} Diperbarui",
        };

        $body = match ($this->status) {
            'approved' => "{$this->category} sebesar {$amount} telah disetujui.",
            'rejected' => "{$this->category} sebesar {$amount} ditolak.",
            default    => "{$this->category} sebesar {$amount} diperbarui.",
        };

        // Format yang dikenali Filament DatabaseNotifications
        return [
            'title'    => $title,
            'body'     => $body,
            'icon'     => match ($this->status) {
                'approved' => 'heroicon-o-check-circle',
                'rejected' => 'heroicon-o-x-circle',
                default    => 'heroicon-o-bell',
            },
            'iconColor' => match ($this->status) {
                'approved' => 'success',
                'rejected' => 'danger',
                default    => 'warning',
            },
            'status'   => match ($this->status) {
                'approved' => 'success',
                'rejected' => 'danger',
                default    => 'warning',
            },
            'color'    => match ($this->status) {
                'approved' => 'success',
                'rejected' => 'danger',
                default    => 'warning',
            },
            'duration' => 'persistent',
            'actions'  => [],
            'format'   => 'filament',
        ];
    }
}
