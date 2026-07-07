<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
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

class ParentPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('parent')
            ->default()
            ->path('parent')
            ->profile(\App\Filament\Pages\Auth\EditProfile::class)

            ->brandName('Dompetkuu')
            ->brandLogo(fn () => view('components.filament.brand-logo'))
            ->brandLogoHeight('2.5rem')

            ->renderHook(
                'panels::sidebar.footer',
                fn() => view('filament.parent.sidebar-footer')
            )

            ->renderHook('panels::body.end', fn () => \Illuminate\Support\Facades\Blade::render('
                <script>
                    document.addEventListener(\'DOMContentLoaded\', function() {
                        var s = document.querySelector(\'aside.fi-sidebar\');
                        if (s) {
                            s.style.setProperty(\'background\', \'linear-gradient(180deg, #FFFBEB 0%, #FEF3C7 100%)\', \'important\');
                            s.style.setProperty(\'background-color\', \'#FEF3C7\', \'important\');
                        }
                    });
                </script>
            '))

            ->userMenuItems([
                    \Filament\Navigation\MenuItem::make()
                        ->label(fn() => auth()->user()?->name . ' · Orang Tua')
                        ->icon('heroicon-o-user-circle')
                        ->url('#'),
                ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->font('Plus Jakarta Sans')
            ->viteTheme('resources/css/filament/parent/theme.css')
            ->maxContentWidth(MaxWidth::SevenExtraLarge)
            //->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->databaseNotificationsPolling('5s')
            ->discoverResources(in: app_path('Filament/Parent/Resources'), for: 'App\\Filament\\Parent\\Resources')
            // ->resources([
            //     \App\Filament\Parent\Resources\FamilyMemberResource::class, // ✅ tambah ini
            //     \App\Filament\Parent\Resources\SavingResource::class, // ✅ tambah ini
            // ])
            ->discoverPages(in: app_path('Filament/Parent/Pages'), for: 'App\\Filament\\Parent\\Pages')
            ->discoverWidgets(in: app_path('Filament/Parent/Widgets'), for: 'App\\Filament\\Parent\\Widgets')
            ->pages([
                \App\Filament\Parent\Pages\Dashboard::class,
            ])
            ->widgets([
                \App\Filament\Parent\Widgets\ParentStatsOverview::class,
                \App\Filament\Parent\Widgets\FamilyFinanceChart::class,
                \App\Filament\Parent\Widgets\FamilyExpenseByCategory::class,
                \App\Filament\Parent\Widgets\FamilyBudgetAlertWidget::class,
                \App\Filament\Parent\Widgets\PendingApprovalWidget::class,
                \App\Filament\Parent\Widgets\ParentLatestTransactions::class,
                \App\Filament\Parent\Widgets\ChildSummaryWidget::class,

            ])
            ->plugins([
    \Joaopaulolndev\FilamentEditProfile\FilamentEditProfilePlugin::make()
        ->slug('my-profile')
        ->shouldRegisterNavigation(false)
        ->shouldShowDeleteAccountForm(false)
        ->shouldShowSanctumTokens(false)
        ->shouldShowBrowserSessionsForm()
        ->shouldShowAvatarForm(),
            ])
            ->middleware([
                \App\Http\Middleware\DetectDevice::class,
                \App\Http\Middleware\RedirectIfMobile::class,
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
            ->authGuard('web')
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
