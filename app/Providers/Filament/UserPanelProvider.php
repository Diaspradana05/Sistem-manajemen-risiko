<?php

namespace App\Providers\Filament;

use App\Filament\User\Pages\AnalisisRisiko;
use App\Filament\User\Pages\LaporanRisiko;
use App\Filament\User\Widgets\AdvancedStatsOverviewWidget;
use App\Filament\User\Widgets\AnalisisrisikoPieChartklinis;
use App\Filament\User\Widgets\AnalisisrisikoBarChartklinis;
use App\Filament\User\Widgets\AnalisisrisikoPieChartNonKlinis;
use App\Filament\User\Widgets\AnalisisrisikoBarChartNonKlinis;
use App\Filament\User\Widgets\WelcomeWidget;
use App\Filament\Widgets\RiskMatrixWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Hasnayeen\Themes\Http\Middleware\SetTheme;
use Hasnayeen\Themes\ThemesPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\Auth\Login;
use App\Filament\User\Widgets\RingkasanRisiko;
use App\Http\Middleware\RedirectIfNotUserRole;

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('user')
            ->path('user')
            ->homeUrl('/user')
            ->login(Login::class)
            ->passwordreset()
            ->profile()
            ->brandName('')
            ->colors([
                 'primary' => [
                    50 => '238, 242, 255',
                    100 => '224, 231, 255',
                    200 => '199, 210, 254',
                    300 => '165, 180, 252',
                    400 => '129, 140, 248',
                    500 => '99, 102, 241',
                    600 => '79, 70, 229',
                    700 => '67, 56, 202',
                    800 => '55, 48, 163',
                    900 => '49, 46, 129',
                    950 => '30, 27, 75',
                ],
                ])
            ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
            ->pages([
                Pages\Dashboard::class,
                LaporanRisiko::class,
                AnalisisRisiko::class,
            ])
            ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\\Filament\\User\\Widgets')
            ->widgets([
                AdvancedStatsOverviewWidget::class,
                AnalisisrisikoPieChartklinis::class,
                AnalisisrisikoBarChartklinis::class,
                AnalisisrisikoPieChartNonKlinis::class,
                AnalisisrisikoBarChartNonKlinis::class,
                RiskMatrixWidget::class,
                WelcomeWidget::class,
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
                SetTheme::class,
            ])

            ->plugin(
                ThemesPlugin::make()
            )
            ->authMiddleware([
                Authenticate::class,
                RedirectIfNotUserRole::class,

            
            ]);
        }
    }
