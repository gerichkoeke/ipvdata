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
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PartnerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('partner')
            ->path('partner-panel')
            ->login(\App\Filament\Partner\Pages\Auth\Login::class)
            ->profile(\App\Filament\Partner\Pages\Auth\EditProfile::class, isSimple: false)
            ->colors(['primary' => Color::Indigo])
            ->brandName('Portal do Parceiro')
            ->brandLogo(fn () => view('filament.brand.partner-logo'))
            ->brandLogoHeight('2.5rem')
            ->topNavigation()
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Meu Perfil')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn (): string => request()->getSchemeAndHttpHost() . '/partner-panel/profile'),
            ])
            ->discoverResources(
                in: app_path('Filament/Partner/Resources'),
                for: 'App\\Filament\\Partner\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Partner/Pages'),
                for: 'App\\Filament\\Partner\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/Partner/Widgets'),
                for: 'App\\Filament\\Partner\\Widgets'
            )
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
                \App\Http\Middleware\SetLocale::class,
            ])
            ->authMiddleware([Authenticate::class]);
    }

    public function boot(): void
    {
        \Illuminate\Support\Facades\Route::middleware([
            'web',
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
        ->prefix('partner-panel')
        ->name('filament.partner.auth.')
        ->group(function () {
            \Illuminate\Support\Facades\Route::get(
                '/mfa-challenge',
                \App\Filament\Partner\Pages\Auth\MfaChallenge::class
            )->name('mfa-challenge');
        });
    }
}
