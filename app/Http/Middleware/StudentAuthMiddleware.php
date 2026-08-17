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

            return redirect()->route('student.login')->with('info', 'Nyamuneka injira kugira ngo ubone iyi paji.');
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
