<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\View\PanelsRenderHook;
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
            ->login()
            ->profile(\App\Filament\Pages\Auth\EditProfile::class, isSimple: false)
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
                    900 => '#036635',
                    950 => '#036635',
                ],
            ])
            ->darkMode(false)
            ->renderHook(
                PanelsRenderHook::STYLES_AFTER,
                fn (): string => <<<'HTML'
                    <style>
                        .fi-sidebar-item.fi-active > .fi-sidebar-item-btn {
                            border-left: 3px solid var(--primary-500);
                            background-color: white;
                        }

                        .fi-section-header {
                            border-left: 3px solid var(--primary-500);
                            padding-left: 0.75rem;
                            border-top-left-radius: 0.5rem;
                        }

                        .fi-sidebar-item-badge-ctn .fi-badge {
                            background-color: var(--primary-100);
                            color: var(--primary-700);
                            --tw-ring-color: var(--primary-600);
                        }

                        .fi-ta {
                            border-left: 3px solid var(--primary-500);
                            border-top-left-radius: 0.8rem;
                            border-bottom-left-radius: 0.8rem;
                        }

                        .fi-ta,
                        .fi-badge.fi-color {
                            --primary-50: color-mix(in oklab, #036635 8%, white);
                            --primary-100: color-mix(in oklab, #036635 16%, white);
                            --primary-200: color-mix(in oklab, #036635 28%, white);
                            --primary-300: color-mix(in oklab, #036635 42%, white);
                            --primary-400: color-mix(in oklab, #036635 58%, white);
                            --primary-500: #036635;
                            --primary-600: color-mix(in oklab, #036635 85%, black);
                            --primary-700: color-mix(in oklab, #036635 72%, black);
                            --primary-800: color-mix(in oklab, #036635 60%, black);
                            --primary-900: color-mix(in oklab, #036635 48%, black);
                            --primary-950: color-mix(in oklab, #036635 35%, black);
                        }

                        .fi-badge.fi-color {
                            --color-50: var(--primary-50);
                            --color-100: var(--primary-100);
                            --color-200: var(--primary-200);
                            --color-300: var(--primary-300);
                            --color-400: var(--primary-400);
                            --color-500: var(--primary-500);
                            --color-600: var(--primary-600);
                            --color-700: var(--primary-700);
                            --color-800: var(--primary-800);
                            --color-900: var(--primary-900);
                            --color-950: var(--primary-950);
                        }
                    </style>
                    HTML
            )
            ->brandLogo(asset('ranking-sys.png'))
            ->brandLogoHeight('2.3rem')
            ->breadcrumbs()
            ->navigationGroups([
                'User Management',
                'Council Management',
                'Award Management',
            ])
            ->collapsibleNavigationGroups()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\AccountWidget::class,
                \App\Filament\Widgets\StatsOverview::class,
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
