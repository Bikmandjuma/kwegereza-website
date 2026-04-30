<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Visit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class TrackVisit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next){
        $today = now()->toDateString();

        $guestId = $request->cookie('guest_visit_id');

        if (!$guestId) {
            $guestId = Str::uuid()->toString();
            Cookie::queue('guest_visit_id', $guestId, 60 * 24 * 30);
        }

        $key = "visit_{$guestId}_{$today}";

        if (!cache()->has($key)) {

            $visit = Visit::firstOrCreate(
                ['date' => $today],
                ['count' => 0]
            );

            $visit->increment('count');

            cache()->put($key, true, 600); // 10 minutes
        }

        return $next($request);
    }
}
