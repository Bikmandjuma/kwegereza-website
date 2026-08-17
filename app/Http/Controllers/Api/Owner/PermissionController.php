<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(private PermissionService $permissions)
    {
    }

    public function index(Request $request)
    {
        $paginated = $this->permissions->paginate((int) $request->integer('per_page', 20));

        return response()->json([
            'success' => true, 'message' => null,
            'data' => PermissionResource::collection($paginated->items()),
            'meta' => ['current_page' => $paginated->currentPage(), 'last_page' => $paginated->lastPage(), 'total' => $paginated->total()],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'slug'  => 'required|string|max:255|unique:permissions,slug|regex:/^[a-z0-9_]+\.[a-z0-9_]+$/',
            'group' => 'nullable|string|max:100',
        ], [
            'slug.regex' => 'Slug igomba kuba mu buryo bwa: ikintu.igikorwa (urugero: darsat.create).',
        ]);

        $permission = $this->permissions->create($data['name'], $data['slug'], $data['group'] ?? null);

        return response()->json([
            'success' => true, 'message' => 'Uburenganzira bushya bwashyizweho.',
            'data' => new PermissionResource($permission),
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $permission = Permission::findOrFail($id);

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'slug'  => 'required|string|max:255|unique:permissions,slug,'.$permission->id.'|regex:/^[a-z0-9_]+\.[a-z0-9_]+$/',
            'group' => 'nullable|string|max:100',
        ]);

        $permission = $this->permissions->update($permission, $data['name'], $data['slug'], $data['group'] ?? null);

        return response()->json([
            'success' => true, 'message' => 'Uburenganzira bwahinduwe.',
            'data' => new PermissionResource($permission),
        ]);
    }

    public function destroy(int $id)
    {
        $this->permissions->delete(Permission::findOrFail($id));

        return response()->json(['success' => true, 'message' => 'Uburenganzira bwasibwe.', 'data' => null]);
    }
}
