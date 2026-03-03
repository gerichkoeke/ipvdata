<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectBySubdomain
{
    public function handle(Request $request, Closure $next)
    {
        // Só interceptar requisições GET na raiz /
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        $host = $request->getHost();
        $path = $request->path();

        // dis.ipvdata.com.br — redirecionar raiz para login distribuidor
        if (str_starts_with($host, 'dis.') || str_starts_with($host, 'distributor.')) {
            if (str_starts_with($path, 'distributor')) {
                return $next($request);
            }
            if ($path === '/') {
                return redirect('/distributor/login');
            }
            return $next($request);
        }

        // par.ipvdata.com.br — redirecionar raiz para login parceiro
        if (str_starts_with($host, 'par.') || str_starts_with($host, 'partner.')) {
            if (str_starts_with($path, 'partner-panel')) {
                return $next($request);
            }
            if ($path === '/') {
                return redirect('/partner-panel/login');
            }
            return $next($request);
        }

        // admin.ipvdata.com.br — redirecionar raiz para login admin
        if (str_starts_with($host, 'admin.')) {
            if (str_starts_with($path, 'admin-panel')) {
                return $next($request);
            }
            if ($path === '/') {
                return redirect('/admin-panel/login');
            }
            return $next($request);
        }

        return $next($request);
    }
}
