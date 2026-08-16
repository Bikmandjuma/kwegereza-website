<?php

namespace App\Http\Middleware;

use App\Models\Owner;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * API counterpart to EnsurePermission (which is web/session-guard only).
 *
 * Usage on a route:
 *   ->middleware('permission.api:darsat.create')
 *
 * Sanctum's auth:sanctum guard resolves $request->user() to whichever
 * HasApiTokens model the token belongs to (Owner today; could be a student
 * User in a future phase). This middleware only knows how to check
 * permissions for Owner — kept separate from EnsurePermission so nothing
 * about the already-verified web RBAC pipeline is touched.
 */
class EnsureApiPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user instanceof Owner || ! $user->hasPermission($permission)) {
            return response()->json([
                'success' => false,
                'message' => 'Ntabwo ufite uburenganzira bwo gukora iki gikorwa.',
            ], 403);
        }

        return $next($request);
    }
}
