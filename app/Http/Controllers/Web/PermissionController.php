<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:permissions.view')->only(['index']);
        $this->middleware('permission:permissions.create')->only(['store']);
        $this->middleware('permission:permissions.update')->only(['update']);
        $this->middleware('permission:permissions.delete')->only(['destroy']);
    }

    public function index()
    {
        $permissions = Permission::withCount('roles')
            ->orderBy('group')
            ->orderBy('name')
            ->paginate(20);

        return view('Users.admin.permissions', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'slug'  => 'required|string|max:255|unique:permissions,slug|regex:/^[a-z0-9_]+\.[a-z0-9_]+$/',
            'group' => 'nullable|string|max:100',
        ], [
            'slug.regex' => 'Slug igomba kuba mu buryo bwa: ikintu.igikorwa (urugero: darsat.create).',
        ]);

        Permission::create([
            'name'  => $request->name,
            'slug'  => $request->slug,
            'group' => $request->group ?: Str::before($request->slug, '.'),
        ]);

        return redirect()->route('owner.permissions')->with('success', 'Uburenganzira bushya bwashyizweho.');
    }

    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'slug'  => 'required|string|max:255|unique:permissions,slug,' . $permission->id . '|regex:/^[a-z0-9_]+\.[a-z0-9_]+$/',
            'group' => 'nullable|string|max:100',
        ]);

        $permission->update([
            'name'  => $request->name,
            'slug'  => $request->slug,
            'group' => $request->group ?: Str::before($request->slug, '.'),
        ]);

        return redirect()->route('owner.permissions')->with('success', 'Uburenganzira bwahinduwe.');
    }

    public function destroy($id)
    {
        Permission::findOrFail($id)->delete();

        return redirect()->route('owner.permissions')->with('success', 'Uburenganzira bwasibwe.');
    }
}
