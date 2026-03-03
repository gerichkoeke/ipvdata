<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RefreshCsrfToken
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Renovar o cookie CSRF a cada request para evitar expiração
        if (!$request->is('livewire/*') && !$request->ajax()) {
            \Cookie::queue(
                'XSRF-TOKEN',
                csrf_token(),
                config('session.lifetime'),
                '/',
                config('session.domain'),
                config('session.secure'),
                false,
                false,
                config('session.same_site') ?? 'lax'
            );
        }

        return $response;
    }
}
