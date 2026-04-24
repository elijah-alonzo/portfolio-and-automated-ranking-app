<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Widgets\AccountWidget;
use App\Filament\Widgets\CouncilSubmissionProgressWidget;
use App\Filament\Widgets\MySubmissionProgressWidget;
use App\Filament\Widgets\StatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class RankingPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('ranking')
            ->path('ranking')
            ->viteTheme('resources/css/filament/ranking/theme.css')
            ->login()
            ->profile(EditProfile::class, isSimple: false)
            ->font('Figtree')
            ->colors([
                'primary' => [
                    50 => '#036635',
                    100 => '#036635',
                    200 => '#036635',
                    300 => '#036635',
                    400 => '#036635',
                    500 => '#036635',
                    600 => '#036635',
                    700 => '#036635',
                    800 => '#036635',
                    900 => '#ffffff',
                    950 => '#036635',
                ],
            ])
            ->darkMode(false)
            ->sidebarCollapsibleOnDesktop()
            ->databaseNotifications()
            ->brandLogo(asset('sys-logo.png'))
            ->topbar(false)
            ->brandLogoHeight('2.5rem')
            ->breadcrumbs()
            ->navigationGroups([
                'User Management',
                'Portfolio Management',
                'Department Management',
                'Council Management',
                'Award Management',
                'Help',
            ])
            ->collapsibleNavigationGroups()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                StatsOverview::class,
                CouncilSubmissionProgressWidget::class,
                MySubmissionProgressWidget::class,
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
