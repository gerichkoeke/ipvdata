<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Filament\Facades\Filament;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Prefix vazio no session store
        Cache::store('session')->getStore()->setPrefix('');

        // Injetar Alpine NO INÍCIO do HEAD - ANTES de qualquer script do Filament
        // O Livewire 3 detecta window.Alpine e chama Alpine.start()
        Filament::registerRenderHook(
            PanelsRenderHook::HEAD_START,
            fn (): HtmlString => new HtmlString(
                '<script src="/js/app/alpine.js?v=' . filemtime(public_path('js/app/alpine.js')) . '"></script>'
            ),
        );

        // Registrar componentes MFA
        Livewire::component(
            'app.filament.admin.pages.auth.mfa-challenge',
            \App\Filament\Admin\Pages\Auth\MfaChallenge::class
        );
        Livewire::component(
            'app.filament.partner.pages.auth.mfa-challenge',
            \App\Filament\Partner\Pages\Auth\MfaChallenge::class
        );

        // Script anti-expiração CSRF
        Filament::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn (): HtmlString => new HtmlString('
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener("livewire:init", function () {
        Livewire.hook("request", ({ fail }) => {
            fail(({ status, preventDefault }) => {
                if (status === 419 || status === 403) {
                    preventDefault();
                    console.warn("[CSRF] Token expirado, recarregando...");
                    setTimeout(() => window.location.reload(), 400);
                }
            });
        });
    });
});
</script>
            '),
        );
    }
}
