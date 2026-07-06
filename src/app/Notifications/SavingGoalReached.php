<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SavingGoalReached extends Notification
{
    use Queueable;

    public function __construct(
        public string $savingName,
        public float $targetAmount,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'icon'    => 'heroicon-o-trophy',
            'title'   => 'Target Tabungan Tercapai! 🎉',
            'message' => "Target \"{$this->savingName}\" sebesar Rp " . number_format($this->targetAmount, 0, ',', '.') . " berhasil tercapai!",
            'type'    => 'saving',
        ];
    }
}
