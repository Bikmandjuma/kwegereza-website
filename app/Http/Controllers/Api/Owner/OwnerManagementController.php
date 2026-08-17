<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\OwnerManagementResource;
use App\Models\Owner;
use App\Services\OwnerManagementService;
use Illuminate\Http\Request;

class OwnerManagementController extends Controller
{
    public function __construct(private OwnerManagementService $owners)
    {
    }

    public function index(Request $request)
    {
        $paginated = $this->owners->paginate(
            perPage: (int) $request->integer('per_page', 15),
            title: $request->string('title')->value() ?: null,
            search: $request->string('search')->value() ?: null,
        );

        return response()->json([
            'success' => true, 'message' => null,
            'data' => OwnerManagementResource::collection($paginated->items()),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    public function show(int $id)
    {
        return response()->json([
            'success' => true, 'message' => null,
            'data' => new OwnerManagementResource($this->owners->find($id)),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'gender'    => 'required|in:male,female',
            'phone'     => 'required|string|unique:owners,phone',
            'dob'       => 'required|date',
            'email'     => 'required|email|unique:owners,email',
            'role'      => 'required|string',
            'title'     => 'required|string',
            'password'  => 'required|string|min:8',
            'image'     => 'nullable|image|max:2048',
            'role_slugs'   => 'nullable|array',
            'role_slugs.*' => 'string|exists:roles,slug',
        ]);

        $owner = $this->owners->create($data, $request->file('image'));

        return response()->json([
            'success' => true, 'message' => 'Umukoresha yashyizweho.',
            'data' => new OwnerManagementResource($owner->load('roles')),
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $owner = Owner::findOrFail($id);

        $data = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'gender'    => 'required|in:male,female',
            'phone'     => 'required|string|unique:owners,phone,'.$owner->id,
            'dob'       => 'required|date',
            'email'     => 'required|email|unique:owners,email,'.$owner->id,
            'role'      => 'required|string',
            'title'     => 'required|string',
            'password'  => 'nullable|string|min:8',
            'image'     => 'nullable|image|max:2048',
            'role_slugs'   => 'nullable|array',
            'role_slugs.*' => 'string|exists:roles,slug',
        ]);

        $owner = $this->owners->update($owner, $data, $request->file('image'));

        return response()->json([
            'success' => true, 'message' => 'Umukoresha yahinduwe.',
            'data' => new OwnerManagementResource($owner->fresh('roles')),
        ]);
    }

    public function destroy(Request $request, int $id)
    {
        $owner = Owner::findOrFail($id);

        if ($owner->id === $request->user()->id) {
            abort(422, 'Ntushobora gusiba konti yawe bwite.');
        }

        $this->owners->delete($owner);

        return response()->json(['success' => true, 'message' => 'Umukoresha yasibwe.', 'data' => null]);
    }
}
