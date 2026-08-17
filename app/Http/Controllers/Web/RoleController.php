<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:roles.view')->only(['index']);
        $this->middleware('permission:roles.create')->only(['store']);
        $this->middleware('permission:roles.update')->only(['update', 'assign']);
        $this->middleware('permission:roles.delete')->only(['destroy']);
    }

    public function index()
    {
        $roles = Role::withCount(['permissions', 'owners'])->latest()->get();
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');

        return view('Users.admin.roles', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name',
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
            'is_super'    => false,
        ]);

        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('owner.roles')->with('success', 'Uruhare rushya rwashyizweho neza.');
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        if ($role->is_super) {
            return redirect()->route('owner.roles')->with('error', 'Ntushobora guhindura uruhare rwa Super Admin.');
        }

        $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'description' => $request->description,
        ]);

        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('owner.roles')->with('success', 'Uruhare rwahinduwe neza.');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->is_super) {
            return redirect()->route('owner.roles')->with('error', 'Ntushobora gusiba uruhare rwa Super Admin.');
        }

        $role->delete();

        return redirect()->route('owner.roles')->with('success', 'Uruhare rwasibwe.');
    }

    /**
     * Assign one or more roles to an owner (staff/teacher/leader/admin account).
     */
    public function assign(Request $request, $ownerId)
    {
        $request->validate([
            'roles'   => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $owner = Owner::findOrFail($ownerId);
        $owner->roles()->sync($request->input('roles', []));

        return redirect()->back()->with('success', 'Uruhare rw\'ukoresha rwahinduwe.');
    }
}
