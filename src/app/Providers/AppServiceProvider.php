<?php

namespace App\Providers;

use App\Models\Income;
use App\Models\Expense;
use App\Observers\IncomeObserver;
use App\Observers\ExpenseObserver;
use App\Http\Responses\RegistrationResponse;
use App\Policies\ActivityPolicy;
use Filament\Actions\MountableAction;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse as RegistrationResponseContract;
use Filament\Notifications\Livewire\Notifications;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ✅ Logout redirect ke /login
        $this->app->bind(LogoutResponseContract::class, function () {
            return new class implements LogoutResponseContract {
                public function toResponse($request)
                {
                    return redirect('/login');
                }
            };
        });

        // ✅ Register redirect sesuai role
        $this->app->bind(RegistrationResponseContract::class, RegistrationResponse::class);
    }

    public function boot(): void
    {
        \Carbon\Carbon::setLocale('id');
        Gate::policy(Activity::class, ActivityPolicy::class);
        Page::formActionsAlignment(Alignment::Right);
        Notifications::alignment(Alignment::End);
        Notifications::verticalAlignment(VerticalAlignment::End);
        Income::observe(IncomeObserver::class);
        Expense::observe(ExpenseObserver::class);
        Page::$reportValidationErrorUsing = function (ValidationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        };
        MountableAction::configureUsing(function (MountableAction $action) {
            $action->modalFooterActionsAlignment(Alignment::Right);
        });
    }
}
