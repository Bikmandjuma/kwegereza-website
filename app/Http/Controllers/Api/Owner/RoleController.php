<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(private RoleService $roles)
    {
    }

    public function index()
    {
        return response()->json([
            'success' => true, 'message' => null,
            'data' => RoleResource::collection($this->roles->all()),
        ]);
    }

    public function show(int $id)
    {
        return response()->json([
            'success' => true, 'message' => null,
            'data' => new RoleResource($this->roles->find($id)),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name',
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = $this->roles->create($data['name'], $data['description'] ?? null, $data['permissions'] ?? []);

        return response()->json([
            'success' => true, 'message' => 'Uruhare rushya rwashyizweho neza.',
            'data' => new RoleResource($role->load('permissions')),
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $role = Role::findOrFail($id);

        $data = $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name,'.$role->id,
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = $this->roles->update($role, $data['name'], $data['description'] ?? null, $data['permissions'] ?? []);

        return response()->json([
            'success' => true, 'message' => 'Uruhare rwahinduwe neza.',
            'data' => new RoleResource($role->fresh('permissions')),
        ]);
    }

    public function destroy(int $id)
    {
        $this->roles->delete(Role::findOrFail($id));

        return response()->json(['success' => true, 'message' => 'Uruhare rwasibwe.', 'data' => null]);
    }

    public function assign(Request $request, int $ownerId)
    {
        $request->validate(['roles' => 'nullable|array', 'roles.*' => 'exists:roles,id']);

        $owner = \App\Models\Owner::findOrFail($ownerId);
        $owner = $this->roles->assignToOwner($owner, $request->input('roles', []));

        return response()->json([
            'success' => true, 'message' => "Uruhare rw'ukoresha rwahinduwe.",
            'data' => ['roles' => $owner->roles->pluck('slug')],
        ]);
    }
}
