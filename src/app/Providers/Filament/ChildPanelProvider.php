<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ChildPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('child')
            ->path('child')
            ->login()
            ->passwordReset()
            ->profile(\App\Filament\Pages\Auth\EditProfile::class)
            ->databaseNotifications()
            ->databaseNotificationsPolling('15s')

            // ✅ Branding sidebar
            ->brandName('Dompetkuu')
            ->brandLogo(null)

            // ✅ Info tipe akun di sidebar bawah
            ->renderHook(
                'panels::sidebar.footer',
                fn() => view('filament.child.sidebar-footer')
            )

            // ✅ User menu lebih informatif
            ->userMenuItems([
                \Filament\Navigation\MenuItem::make('profile')
                    ->label('Edit Profil')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn() => \App\Filament\Pages\Auth\EditProfile::getUrl()),
            ])

            ->colors([
                'primary' => Color::Orange,
            ])
            ->viteTheme('resources/css/filament/child/theme.css')
            ->font('Montserrat')
            ->maxContentWidth(MaxWidth::SevenExtraLarge)
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()          // ← tambah ini
            ->databaseNotificationsPolling('30s')
            ->discoverResources(in: app_path('Filament/Child/Resources'), for: 'App\\Filament\\Child\\Resources')
            ->discoverPages(in: app_path('Filament/Child/Pages'), for: 'App\\Filament\\Child\\Pages')
            ->discoverWidgets(in: app_path('Filament/Child/Widgets'), for: 'App\\Filament\\Child\\Widgets')
            ->pages([
                \App\Filament\Child\Pages\Dashboard::class,
            ])
            ->widgets([
                \App\Filament\Child\Widgets\BudgetAlertWidget::class,
                \App\Filament\Child\Widgets\ChildStatsCards::class,
                \App\Filament\Child\Widgets\ChildFinanceChart::class,
                \App\Filament\Child\Widgets\ExpenseByCategory::class,
                \App\Filament\Child\Widgets\LatestTransactions::class,
                \App\Filament\Child\Widgets\BudgetOverview::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
