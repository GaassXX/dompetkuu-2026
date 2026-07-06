<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransactionPending extends Notification
{
    use Queueable;

    public function __construct(
        public string $childName,
        public string $type,
        public float $amount,
        public string $description,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icon'        => 'heroicon-o-clock',
            'title'       => 'Transaksi Menunggu',
            'message'     => "{$this->childName} mengajukan {$this->type} sebesar Rp " . number_format($this->amount, 0, ',', '.'),
            'description' => $this->description,
            'type'        => 'pending',
        ];
    }
}
