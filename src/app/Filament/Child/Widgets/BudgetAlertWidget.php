<?php

namespace App\Filament\Child\Widgets;

use App\Services\BudgetAlertService;
use Filament\Widgets\Widget;

class BudgetAlertWidget extends Widget
{
    protected static ?int $sort = 0;
    protected static ?string $maxHeight = '300px';
    protected static string $view = 'filament.child.widgets.budget-alert-widget';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $alerts = BudgetAlertService::getAlerts(auth()->id(), 90);
        return count($alerts) > 0;
    }

    public array $alerts = [];

    public function mount(): void
    {
        $this->alerts = BudgetAlertService::getAlerts(auth()->id(), 90);
    }

    public function dismissAlert(int $budgetId, string $period): void
    {
        BudgetAlertService::dismissAlert(auth()->id(), $budgetId, $period);
        $this->alerts = BudgetAlertService::getAlerts(auth()->id(), 90);
    }
}
