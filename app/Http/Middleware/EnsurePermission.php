<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Usage on a route:
 *   ->middleware('permission:darsat.create')
 *
 * Must run AFTER 'ownerAuth' so a guest never reaches here.
 * Super Admin (role.is_super = true) always passes.
 */
class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $owner = Auth::guard('owner')->user();

        if (!$owner || !$owner->hasPermission($permission)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Ntabwo ufite uburenganzira bwo gukora iki gikorwa.'], 403);
            }

            abort(403, 'Ntabwo ufite uburenganzira bwo gukora iki gikorwa.');
        }

        return $next($request);
    }
}
