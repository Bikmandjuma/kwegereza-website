<?php

namespace App\Http\Middleware;

use App\Events\StudentOnline;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Fixed the real gap the Phase 0 audit flagged: this middleware previously
 * stamped last_active_at on EVERY single request with no concept of a
 * transition at all — spec section 17 explicitly requires "a user should
 * generate a 'new user online' event only when transitioning
 * OFFLINE -> ONLINE, not on every request." There was no event of any
 * kind before this phase, transition-aware or not.
 *
 * The "was this genuinely offline before now" check happens BEFORE the
 * timestamp update, using the OLD value still on the model — updating
 * first would make every subsequent check see a fresh timestamp and never
 * detect a transition again. The same check also naturally handles
 * multiple open tabs for one student: whichever tab's request lands first
 * updates the shared last_active_at column, so a second tab's request a
 * moment later reads the now-recent timestamp and correctly does not
 * re-fire the broadcast.
 */
class UpdateLastActive
{
    public function handle(Request $request, Closure $next , $guard = null): Response
    {
        if (Auth::guard('student')->check()) {
            $student = Auth::guard('student')->user();

            $wasOffline = ! $student->last_active_at
                || $student->last_active_at->lt(now()->subMinutes(\App\Services\StudentService::ONLINE_THRESHOLD_MINUTES));

            $student->update(['last_active_at' => now()]);

            if ($wasOffline) {
                try {
                    broadcast(new StudentOnline($student));
                } catch (\Throwable $e) {
                    Log::warning('StudentOnline broadcast failed (last_active_at was still updated).', [
                        'student_id' => $student->id,
                        'exception' => $e->getMessage(),
                    ]);
                }
            }
        }

        return $next($request);
    }
}
