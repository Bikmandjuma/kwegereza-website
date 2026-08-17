<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public const SUPPORTED = ['rw', 'en', 'fr', 'ar'];
    public const RTL = ['ar'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('lang');

        if ($locale && in_array($locale, self::SUPPORTED, true)) {
            session(['locale' => $locale]);
        } else {
            $locale = session('locale', config('app.locale', 'rw'));
        }

        if (!in_array($locale, self::SUPPORTED, true)) {
            $locale = 'rw';
        }

        App::setLocale($locale);

        // Available to every view without passing it explicitly everywhere.
        view()->share('currentLocale', $locale);
        view()->share('isRtl', in_array($locale, self::RTL, true));

        return $next($request);
    }
}
