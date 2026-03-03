<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SetSessionCookieBySubdomain
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();

        if (str_contains($host, 'admin.')) {
            Config::set('session.cookie', 'ipvdata_admin_session');
        } elseif (str_contains($host, 'partner.') || str_contains($host, 'par.')) {
            Config::set('session.cookie', 'ipvdata_partner_session');
        } elseif (str_contains($host, 'distributor.') || str_contains($host, 'dis.')) {
            Config::set('session.cookie', 'ipvdata_distributor_session');
        }

        return $next($request);
    }
}
