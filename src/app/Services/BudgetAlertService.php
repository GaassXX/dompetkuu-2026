<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\DismissedBudgetAlert;
use App\Models\User;
use App\Notifications\BudgetAlertNotification;
use Illuminate\Support\Carbon;

class BudgetAlertService
{
    public static function checkAndNotify(int $userId, int $threshold = 90, bool $notifyParent = false): void
    {
        $budgets = Budget::where('user_id', $userId)
            ->with('category')
            ->get();

        $owner = User::find($userId);
        if (!$owner) return;

        foreach ($budgets as $budget) {
            $percentage = $budget->getUsagePercentage();

            if ($percentage >= $threshold) {
                $rawSpent = $budget->getSpentAmount();
                $rawLimit = (float) $budget->limit_amount;
                $pct      = self::formatPercentage($percentage);
                $period   = $budget->period === 'monthly' ? 'bulan ini' : 'minggu ini';
                $title    = self::getAlertTitle($percentage);
                $body     = "Kategori **{$budget->category->name}** {$period}: " .
                    "Rp " . number_format($rawSpent, 0, ',', '.') .
                    " / Rp " . number_format($rawLimit, 0, ',', '.') .
                    " ({$pct}%)";

                $owner->notify(new BudgetAlertNotification(
                    title: $title,
                    body: $body,
                    categoryName: $budget->category->name,
                    spent: $rawSpent,
                    limit: $rawLimit,
                    percentage: $pct,
                ));

                if ($notifyParent && $owner->parent_id) {
                    $parent = User::find($owner->parent_id);
                    if ($parent) {
                        $parentBody = "{$owner->name} — {$body}";
                        $parent->notify(new BudgetAlertNotification(
                            title: $title,
                            body: $parentBody,
                            categoryName: $budget->category->name,
                            spent: $rawSpent,
                            limit: $rawLimit,
                            percentage: $pct,
                        ));
                    }
                }
            }
        }
    }

    private static function getAlertTitle(float $percentage): string
    {
        if ($percentage > 100) {
            return '🚨 Budget Melebihi Limit!';
        }
        if ($percentage == 100) {
            return 'Budget Habis!';
        }
        return '⚠️ Budget Hampir Habis!';
    }

    public static function getAlerts(int $userId, int $threshold = 90): array
    {
        $budgets = Budget::where('user_id', $userId)
            ->with('category')
            ->get();

        $dismissedIds = DismissedBudgetAlert::where('user_id', $userId)
            ->get()
            ->keyBy(fn($d) => $d->budget_id . '_' . $d->period);

        $alerts = [];

        foreach ($budgets as $budget) {
            $percentage = $budget->getUsagePercentage();
            $spent      = $budget->getSpentAmount();

            if ($percentage >= $threshold) {
                $key = $budget->id . '_' . $budget->period;
                $dismissed = $dismissedIds->get($key);

                if ($dismissed && self::isDismissedInCurrentPeriod($dismissed, $budget->period)) {
                    continue;
                }

                $alerts[] = [
                    'budget_id'  => $budget->id,
                    'category'   => $budget->category->name,
                    'period'     => $budget->period === 'monthly' ? 'Bulanan' : 'Mingguan',
                    'period_raw' => $budget->period,
                    'spent'      => $spent,
                    'limit'      => (float) $budget->limit_amount,
                    'percentage' => $percentage,
                    'is_over'    => $percentage > 100,
                ];
            }
        }

        return $alerts;
    }

    public static function dismissAlert(int $userId, int $budgetId, string $period): void
    {
        DismissedBudgetAlert::updateOrCreate(
            [
                'user_id'   => $userId,
                'budget_id' => $budgetId,
                'period'    => $period,
            ],
            [
                'updated_at' => now(),
            ]
        );
    }

    private static function isDismissedInCurrentPeriod(DismissedBudgetAlert $dismissed, string $period): bool
    {
        $now = Carbon::now();

        if ($period === 'monthly') {
            return $dismissed->created_at->format('Y-m') === $now->format('Y-m');
        }

        return $dismissed->created_at->format('o-W') === $now->format('o-W');
    }

    public static function formatPercentage(float $value): string
    {
        return $value == (int)$value
            ? number_format($value)
            : number_format($value, 1);
    }
}
