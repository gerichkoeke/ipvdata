<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireMfa
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        // Se MFA está habilitado e ainda não verificado nesta sessão
        if ($user->mfa_enabled && !session('mfa_verified')) {
            // Salvar URL pretendida
            session(['url.intended' => $request->url()]);
            return redirect()->route('filament.admin.auth.mfa-challenge');
        }

        return $next($request);
    }
}
