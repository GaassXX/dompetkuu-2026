<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransactionProcessed extends Notification
{
    use Queueable;

    public function __construct(
        public string $status,
        public string $type,
        public float $amount,
        public ?string $reason = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $label = $this->status === 'approved' ? 'Disetujui' : 'Ditolak';
        $icon  = $this->status === 'approved' ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle';

        return [
            'icon'        => $icon,
            'title'       => "Transaksi {$label}",
            'message'     => "{$this->type} sebesar Rp " . number_format($this->amount, 0, ',', '.') . " {$label} oleh Orang Tua",
            'description' => $this->reason ?? '',
            'type'        => 'processed',
        ];
    }
}
