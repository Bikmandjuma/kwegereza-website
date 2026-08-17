<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\OwnerProfileResource;
use App\Services\OwnerProfileService;
use Illuminate\Http\Request;

/**
 * Deliberately no permission gate beyond auth:sanctum: every owner
 * manages only their own profile here, the same reasoning as the
 * Notifications phase's inbox endpoints — there's no "view someone
 * else's profile" concern for a granular permission slug to guard
 * against.
 */
class ProfileController extends Controller
{
    public function __construct(private OwnerProfileService $profile)
    {
    }

    public function show(Request $request)
    {
        return response()->json([
            'success' => true, 'message' => null,
            'data' => new OwnerProfileResource($request->user()->load('roles')),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'phone'     => 'required|string|max:20',
            'bio'       => 'nullable|string|max:2000',
        ]);

        $owner = $this->profile->updateProfile($request->user(), $data);

        return response()->json([
            'success' => true, 'message' => 'Umwirondoro wahinduwe.',
            'data' => new OwnerProfileResource($owner),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $this->profile->updatePassword($request->user(), $request->current_password, $request->new_password);

        return response()->json(['success' => true, 'message' => 'Ijambo banga ryahinduwe.', 'data' => null]);
    }

    public function updateAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|max:2048']);

        $owner = $this->profile->updateAvatar($request->user(), $request->file('avatar'));

        return response()->json([
            'success' => true, 'message' => 'Ifoto yahinduwe.',
            'data' => new OwnerProfileResource($owner),
        ]);
    }
}
