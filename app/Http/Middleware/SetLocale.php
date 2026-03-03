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
            if (auth()->check()) {
                $user   = auth()->user();
                $locale = null;

                // Prioridade 1: locale do parceiro
                if ($user->partner_id) {
                    $locale = optional($user->partner)->locale;
                }

                // Prioridade 2: locale do distribuidor
                if (!$locale && $user->distributor_id) {
                    $locale = optional($user->distributor)->locale;
                }

                // Prioridade 3: locale do próprio usuário
                if (!$locale) {
                    $locale = $user->locale ?? null;
                }

                // Prioridade 4: sessão
                if (!$locale) {
                    $locale = Session::get('locale');
                }

                // Validar e aplicar
                $validLocales = ['pt_BR', 'en_US', 'en', 'es', 'pt'];
                if ($locale && in_array($locale, $validLocales)) {
                    App::setLocale($locale);
                } else {
                    App::setLocale(config('app.locale', 'pt_BR'));
                }
            } else {
                // Não autenticado — usar sessão ou padrão
                $locale = Session::get('locale', config('app.locale', 'pt_BR'));
                App::setLocale($locale);
            }
        } catch (\Throwable $e) {
            App::setLocale(config('app.locale', 'pt_BR'));
        }

        return $next($request);
    }
}
