<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class LivewireServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Corrigir APP_URL ANTES de qualquer boot()
        // Isso garante que Filament e Livewire usem o host correto
        $host   = $_SERVER['HTTP_HOST']  ?? null;
        $https  = $_SERVER['HTTPS']      ?? null;
        $scheme = ($https && $https !== 'off') ? 'https' : 'http';

        if ($host) {
            $appUrl = $scheme . '://' . $host;
            config(['app.url' => $appUrl]);
        }
    }

    public function boot(): void
    {
        $request = $this->app->make('request');

        if ($request && $request->getHost()) {
            $scheme = $request->getScheme();
            $host   = $request->getHost();
            $appUrl = $scheme . '://' . $host;

            config(['app.url' => $appUrl]);
            URL::forceRootUrl($appUrl);

            if ($scheme === 'https') {
                URL::forceScheme('https');
            }
        }
    }
}
