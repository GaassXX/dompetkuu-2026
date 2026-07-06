<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransactionSuccess extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public float $amount,
        public string $category,
        public string $description,
        public string $source = 'Mobile',
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $label = $this->type === 'Pemasukan' ? 'Pemasukan' : 'Pengeluaran';
        $icon  = $this->type === 'Pemasukan' ? 'heroicon-o-arrow-trending-down' : 'heroicon-o-arrow-trending-up';

        return [
            'icon'        => $icon,
            'title'       => "{$label} Berhasil",
            'message'     => "Rp " . number_format($this->amount, 0, ',', '.') . " via {$this->source}",
            'description' => "{$this->category} · {$this->description}",
            'type'        => 'transaction',
        ];
    }
}
