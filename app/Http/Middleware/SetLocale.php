<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $validLocales = ['pt_BR', 'en', 'es', 'pt'];
            $locale       = null;

            // Prioridade 1: sessão — escolha explícita do usuário
            if ($sessionLocale = Session::get('locale')) {
                $locale = $sessionLocale;
                // Sync user DB if authenticated and differs
                if (auth()->check()) {
                    $user = auth()->user();
                    if ($user->locale !== $sessionLocale) {
                        $user->update(['locale' => $sessionLocale]);
                    }
                }
            } elseif (auth()->check()) {
                $user = auth()->user();

                // Prioridade 2: locale do próprio usuário
                $locale = $user->locale ?? null;

                // Prioridade 3: locale do parceiro
                if (!$locale && $user->partner_id) {
                    $locale = optional($user->partner)->locale;
                }

                // Prioridade 4: locale do distribuidor
                if (!$locale && $user->distributor_id) {
                    $locale = optional($user->distributor)->locale;
                }
            }

            // Validar e aplicar
            if ($locale && in_array($locale, $validLocales)) {
                App::setLocale($locale);
                Session::put('locale', $locale);
            } else {
                App::setLocale(config('app.locale', 'pt_BR'));
            }
        } catch (\Throwable $e) {
            App::setLocale(config('app.locale', 'pt_BR'));
        }

        return $next($request);
    }
}
