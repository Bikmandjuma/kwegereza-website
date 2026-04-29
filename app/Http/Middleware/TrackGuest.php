<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use App\Models\GuestVisit;

class TrackGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // get or create guest id
        $guestId = $request->cookie('guest_visit_id');

        if (!$guestId) {
            $guestId = Str::uuid()->toString();

            Cookie::queue('guest_visit_id', $guestId, 60 * 24 * 30);
        }

        // update or create record
        GuestVisit::updateOrCreate(
            ['guest_id' => $guestId],
            [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_visit_at' => now(),
            ]
        );

        return $next($request);
    }
}
