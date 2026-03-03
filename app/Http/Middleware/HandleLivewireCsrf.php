<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleLivewireCsrf
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Renovar o XSRF-TOKEN cookie a cada request
        // para evitar expiração em páginas abertas por muito tempo
        if (method_exists($response, 'cookie')) {
            $response->cookie(
                'XSRF-TOKEN',
                csrf_token(),
                config('session.lifetime'),  // 480 min
                '/',
                config('session.domain'),    // .ipvdata.com.br
                config('session.secure'),    // true
                false,                       // httpOnly = false (JS precisa ler)
                false,
                config('session.same_site') ?? 'lax'
            );
        }

        return $response;
    }
}
