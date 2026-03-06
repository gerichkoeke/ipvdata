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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin-panel')
            ->login(\App\Filament\Admin\Pages\Auth\Login::class)
            ->profile(\App\Filament\Admin\Pages\Auth\EditProfile::class, isSimple: false)
            ->colors(['primary' => Color::Amber])
            ->brandName('IPV ERP')
            ->brandLogo(fn () => view('filament.brand.admin-logo'))
            ->brandLogoHeight('2.5rem')
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Meu Perfil')
                    ->icon('heroicon-o-user-circle')
                    ->url('/admin-panel/profile'),
                'logout' => MenuItem::make()
                    ->label('Sair')
                    ->icon('heroicon-o-arrow-right-on-rectangle'),
            ])
            ->pages([
                \App\Filament\Admin\Pages\Dashboard::class,
                \App\Filament\Admin\Pages\ManageCompany::class,
            ])
            ->discoverResources(
                in: app_path('Filament/Admin/Resources'),
                for: 'App\\Filament\\Admin\\Resources'
            )
            ->resources([
                \App\Filament\Admin\Resources\Infrastructure\ResourcePricingResource::class,
                \App\Filament\Admin\Resources\Infrastructure\DiskTypeResource::class,
                \App\Filament\Admin\Resources\Infrastructure\NetworkTypeResource::class,
                \App\Filament\Admin\Resources\Infrastructure\EndpointSecurityResource::class,
                \App\Filament\Admin\Resources\Infrastructure\OsDistributionResource::class,
                \App\Filament\Admin\Resources\Infrastructure\BandwidthOptionResource::class,
                \App\Filament\Admin\Resources\Infrastructure\BackupSoftwareResource::class,
                \App\Filament\Admin\Resources\Infrastructure\BackupRetentionResource::class,
                \App\Filament\Admin\Resources\Infrastructure\FirewallOptionResource::class,
                \App\Filament\Admin\Resources\Infrastructure\RdsLicenseModeResource::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Admin/Widgets'),
                for: 'App\\Filament\\Admin\\Widgets'
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
        ->prefix('admin-panel')
        ->name('filament.admin.auth.')
        ->group(function () {
            \Illuminate\Support\Facades\Route::get(
                '/mfa-challenge',
                \App\Filament\Admin\Pages\Auth\MfaChallenge::class
            )->name('mfa-challenge');
        });
    }
}
