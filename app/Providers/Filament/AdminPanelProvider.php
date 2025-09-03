<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Register;
use App\Filament\User\Resources\DaftarRisikoResource;
use App\Filament\User\Widgets\AnalisisrisikoBarChartklinis;
use App\Filament\User\Widgets\AnalisisrisikoPieChartklinis;
use App\Filament\User\Widgets\AnalisisrisikoPieChartNonKlinis;
use App\Filament\User\Widgets\AnalisisrisikoBarChartNonKlinis;
use App\Filament\User\Widgets\WelcomeWidget;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Middleware\RedirectIfNotUserRole;
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
use App\Filament\User\Resources\RiskResource;
use App\Filament\User\Pages\AnalisisRisiko;
use App\Filament\User\Pages\LaporanRisiko;
use App\Filament\User\Widgets\AnalisisrisikoPieChart;
use App\Filament\User\Widgets\AnalisisrisikoBarChart;
use App\Http\Middleware\RedirectIfNotSuperadmin;
use App\Filament\User\Widgets\AdvancedStatsOverviewWidget;
use App\Filament\Widgets\RiskMatrixWidget;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->homeUrl('/admin')
            ->login(Login::class)
            ->brandName('')
            ->passwordreset()
            ->profile()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                LaporanRisiko::class,
                AnalisisRisiko::class,
                 
            ])

            ->resources([
                DaftarRisikoResource::class,
                RiskResource::class,
])
       
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AdvancedStatsOverviewWidget::class,
                RiskMatrixWidget::class,
                AnalisisrisikoPieChartklinis::class,
                AnalisisrisikoBarChartklinis::class,
                AnalisisrisikoPieChartNonKlinis::class,
                AnalisisrisikoBarChartNonKlinis::class,
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
                RedirectIfNotSuperadmin::class,
                

                
            ]);
            
    }
}
