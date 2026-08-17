<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Kwegereza owner/leader/admin API authentication.
 *
 * Deliberately separate from the legacy Api\ApiAuthController (job-seeker
 * product, JWT/'users' guard) and from the existing session-based owner
 * login (Web\WebAuthController) — issues Sanctum personal access tokens for
 * the React app instead of a session cookie or JWT.
 */
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $owner = Owner::where('email', $credentials['email'])->first();

        if (! $owner || ! Hash::check($credentials['password'], $owner->password)) {
            throw ValidationException::withMessages([
                'email' => ['Amakuru wanditse ntabwo ahuye.'],
            ]);
        }

        $token = $owner->createToken('kwegereza-react')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Winjiye neza.',
            'data' => [
                'token' => $token,
                'user'  => $this->present($owner),
            ],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => null,
            'data' => $this->present($request->user()),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['success' => true, 'message' => 'Wasohotse.']);
    }

    /**
     * The user + roles + permissions shape the React sidebar gate consumes
     * (spec section 5): a flat permission-slug array, not nested role trees,
     * so the frontend can do a single hasPermission('darsat.create') lookup.
     */
    private function present(Owner $owner): array
    {
        $owner->loadMissing('roles.permissions');

        return [
            'id'         => $owner->id,
            'firstname'  => $owner->firstname,
            'lastname'   => $owner->lastname,
            'email'      => $owner->email,
            'image_url'  => $owner->imageUrl(),
            'roles'      => $owner->roles->pluck('slug')->values(),
            'permissions' => $owner->isSuperAdmin()
                ? \App\Models\Permission::pluck('slug')->values()
                : $owner->roles->flatMap->permissions->pluck('slug')->unique()->values(),
        ];
    }
}
