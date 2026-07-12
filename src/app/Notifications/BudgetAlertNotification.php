<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BudgetAlertNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $body,
        public string $categoryName,
        public float  $spent,
        public float  $limit,
        public string $percentage,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $percentage = $this->limit > 0
            ? round(($this->spent / $this->limit) * 100)
            : 0;

        return [
            'title'       => $this->title,
            'body'        => $this->body,
            'icon'        => 'heroicon-o-exclamation-triangle',
            'iconColor'   => $percentage >= 100 ? 'danger' : 'warning',
            'status'      => $percentage >= 100 ? 'danger' : 'warning',
            'color'       => $percentage >= 100 ? 'danger' : 'warning',
            'duration'    => 'persistent',
            'format'      => 'filament',
            'message'     => $this->body,
            'description' => "Rp " . number_format($this->spent, 0, ',', '.') . " dari Rp " . number_format($this->limit, 0, ',', '.'),
            'type'        => 'budget',
        ];
    }
}
