<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
class SetLocale
{
    protected array $supported = ['pt_BR', 'en', 'es'];
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale($this->resolveLocale($request));
        return $next($request);
    }
    protected function resolveLocale(Request $request): string
    {
        if (Auth::check()) {
            $user = Auth::user();
            $locale = $user->active_locale ?? $user->locale ?? config('app.locale');
            return $this->normalize($locale);
        }
        if ($request->session()->has('locale')) {
            return $this->normalize($request->session()->get('locale'));
        }
        return config('app.locale', 'pt_BR');
    }
    protected function normalize(string $locale): string
    {
        if (in_array($locale, $this->supported, true)) return $locale;
        $prefix = strtok($locale, '_-');
        foreach ($this->supported as $supported) {
            if (str_starts_with($supported, $prefix)) return $supported;
        }
        return config('app.locale', 'pt_BR');
    }
}
