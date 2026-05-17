<?php

namespace App\Filament\Parent\Widgets;

use App\Services\BudgetAlertService;
use App\Models\User;
use Filament\Widgets\Widget;

class FamilyBudgetAlertWidget extends Widget
{
    protected static ?int $sort   = 1;
    protected static string $view = 'filament.parent.widgets.family-budget-alert-widget';

    public static function canView(): bool
    {
        $childIds = User::where('parent_id', auth()->id())->pluck('id')->toArray();
        foreach ($childIds as $childId) {
            if (count(BudgetAlertService::getAlerts($childId, 90)) > 0) return true;
        }
        return false;
    }

    public array $alerts = [];

    public function mount(): void
    {
        $childIds = User::where('parent_id', auth()->id())
            ->pluck('id', 'name')
            ->toArray();

        foreach ($childIds as $name => $id) {
            $childAlerts = BudgetAlertService::getAlerts($id, 90);
            foreach ($childAlerts as $alert) {
                $this->alerts[] = array_merge($alert, ['child_name' => $name]);
            }
        }
    }
}
