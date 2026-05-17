<?php

namespace App\Services;

use App\Models\Budget;
use Filament\Notifications\Notification;

class BudgetAlertService
{
    public static function checkAndNotify(int $userId, int $threshold = 90): void
    {
        $budgets = Budget::where('user_id', $userId)
            ->with('category')
            ->get();

        foreach ($budgets as $budget) {
            $percentage = $budget->getUsagePercentage();

            if ($percentage >= $threshold) {
                $spent  = number_format($budget->getSpentAmount(), 0, ',', '.');
                $limit  = number_format((float) $budget->limit_amount, 0, ',', '.');
                $pct    = number_format($percentage, 1);
                $period = $budget->period === 'monthly' ? 'bulan ini' : 'minggu ini';

                Notification::make()
                    ->title('⚠️ Budget Hampir Habis!')
                    ->body(
                        "Kategori **{$budget->category->name}** {$period}: " .
                        "Rp {$spent} / Rp {$limit} ({$pct}%)"
                    )
                    ->warning()
                    ->persistent()
                    ->sendToDatabase(auth()->user());
            }
        }
    }

    // ✅ Ambil list budget yang mendekati/melebihi limit
    public static function getAlerts(int $userId, int $threshold = 90): array
    {
        $budgets = Budget::where('user_id', $userId)
            ->with('category')
            ->get();

        $alerts = [];

        foreach ($budgets as $budget) {
            $percentage = $budget->getUsagePercentage();
            $spent      = $budget->getSpentAmount();

            if ($percentage >= $threshold) {
                $alerts[] = [
                    'category'   => $budget->category->name,
                    'period'     => $budget->period === 'monthly' ? 'Bulanan' : 'Mingguan',
                    'spent'      => $spent,
                    'limit'      => (float) $budget->limit_amount,
                    'percentage' => $percentage,
                    'is_over'    => $percentage >= 100,
                ];
            }
        }

        return $alerts;
    }
}
