<?php

namespace App\Providers\Filament;

use App\Filament\Resources\RfqDocumentResource;
use App\Filament\Resources\SupplierResource;
use App\Filament\Pages\SourcingWizard;
use App\Filament\Widgets\EsgScoreWidget;
use App\Filament\Widgets\RfqStatsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('app')
            ->login()
            ->colors([
                'primary' => Color::hex('#7FC200'),  // ZEROvia Green
                'gray'    => Color::hex('#5E656D'),  // ZEROvia Grey
            ])
            ->brandName('ZEROvia Sourcing')
            ->brandLogo(asset('img/zerovia-logo.svg'))
            ->favicon(asset('favicon.ico'))
            ->navigationGroups([
                NavigationGroup::make('Sourcing')->icon('heroicon-o-magnifying-glass'),
                NavigationGroup::make('Lieferanten')->icon('heroicon-o-building-office'),
                NavigationGroup::make('Einstellungen')->icon('heroicon-o-cog-6-tooth'),
            ])
            ->pages([
                Dashboard::class,
                SourcingWizard::class,
            ])
            ->resources([
                SupplierResource::class,
                RfqDocumentResource::class,
            ])
            ->widgets([
                RfqStatsWidget::class,
                EsgScoreWidget::class,
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
