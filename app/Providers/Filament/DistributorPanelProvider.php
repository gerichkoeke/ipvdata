<?php

namespace App\Providers\Filament;

use App\Http\Middleware\FilamentAuthenticate as Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class DistributorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('distributor')
            ->path('distributor')
            ->login(\App\Filament\Distributor\Pages\Auth\Login::class)
            ->profile(\Filament\Pages\Auth\EditProfile::class, isSimple: false)
            ->colors(['primary' => Color::Blue])
            ->brandName('Portal do Distribuidor')
            ->topNavigation()
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label(__('app.profile.title'))
                    ->icon('heroicon-o-user-circle')
                    ->url(fn (): string => request()->getSchemeAndHttpHost() . '/distributor/profile'),
                'logout' => MenuItem::make()
                    ->label(__('app.auth.logout'))
                    ->icon('heroicon-o-arrow-right-on-rectangle'),
            ])
            ->pages([
                \App\Filament\Distributor\Pages\Dashboard::class,
            ])
            ->discoverResources(
                in: app_path('Filament/Distributor/Resources'),
                for: 'App\\Filament\\Distributor\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Distributor/Pages'),
                for: 'App\\Filament\\Distributor\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/Distributor/Widgets'),
                for: 'App\\Filament\\Distributor\\Widgets'
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                \App\Http\Middleware\SetLocale::class,
            ])
            ->authMiddleware([Authenticate::class]);
    }
}
