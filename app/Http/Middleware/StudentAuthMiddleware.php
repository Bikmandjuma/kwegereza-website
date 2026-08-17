<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class StudentAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('student')->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }

            // Confirmed bug while building the shareable Live Class
            // link: this previously redirected to login with no memory
            // of where the student was headed, and login always sent
            // them to the dashboard afterward — so a link shared to a
            // logged-out student (the common case) would silently
            // strand them at their dashboard instead of the class they
            // clicked through for. Laravel's redirect()->guest() stores
            // the original URL in the session as 'url.intended' for
            // exactly this purpose; the login controller below now
            // checks for it.
            return redirect()->guest(route('student.login'))->with('info', 'Nyamuneka injira kugira ngo ubone iyi paji.');
        }

        $user = Auth::guard('student')->user();

        if ($user->deactivated_at) {
            Auth::guard('student')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('student.login')->with('error', 'Konti yawe yahagaritswe.');
        }

        return $next($request);
    }
}
