<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BudgetLimit extends Notification
{
    use Queueable;

    public function __construct(
        public string $categoryName,
        public float $totalSpent,
        public float $limitAmount,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $percentage = $this->limitAmount > 0
            ? round(($this->totalSpent / $this->limitAmount) * 100)
            : 0;

        return [
            'icon'    => 'heroicon-o-exclamation-triangle',
            'title'   => 'Budget Hampir Habis',
            'message' => "Budget {$this->categoryName} sudah terpakai {$percentage}%",
            'description' => "Rp " . number_format($this->totalSpent, 0, ',', '.') . " dari Rp " . number_format($this->limitAmount, 0, ',', '.'),
            'type'    => 'budget',
        ];
    }
}
