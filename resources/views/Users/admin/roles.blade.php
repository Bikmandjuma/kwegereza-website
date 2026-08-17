@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">

        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Roles</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: <strong>{{ $roles->count() }}</strong></p>
            </div>
        </div>

        <button onclick="document.getElementById('createRoleModal').classList.remove('hidden')"
            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white shadow-md rounded-xl bg-primary">
            <i class="fa-solid fa-plus"></i> Ongeraho Uruhare
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="p-4 mb-5 font-medium text-red-700 bg-red-100 rounded-xl">{{ session('error') }}</div>
    @endif

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">

        @foreach($roles as $role)
        <div class="p-5 bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">

            <div class="flex items-start justify-between mb-3">
                <div>
                    <h2 class="flex items-center gap-2 text-lg font-bold text-primary-dark dark:text-light">
                        {{ $role->name }}
                        @if($role->is_super)
                            <span class="px-2 py-0.5 text-[10px] font-bold text-white rounded-full bg-amber-500">SUPER</span>
                        @endif
                    </h2>
                    <p class="text-sm text-gray-500">{{ $role->description }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4 mb-4 text-sm text-gray-500">
                <span><i class="fa-solid fa-key"></i> {{ $role->permissions_count }} permissions</span>
                <span><i class="fa-solid fa-users"></i> {{ $role->owners_count }} users</span>
            </div>

            @if(!$role->is_super)
            <div class="flex items-center gap-2 pt-3 border-t dark:border-gray-700">
                <button onclick="openEditRoleModal({{ $role->id }})"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl bg-primary/10 text-primary-dark">
                    <i class="fa-solid fa-pen"></i> Hindura
                </button>

                <form action="{{ route('owner.roles.destroy', $role->id) }}" method="POST"
                      onsubmit="return confirm('Uremeza gusiba uru ruhare?')">
                    @csrf @method('DELETE')
                    <button class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-red-50 rounded-xl">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </form>
            </div>

            <div id="role-data-{{ $role->id }}" class="hidden"
                 data-name="{{ $role->name }}"
                 data-description="{{ $role->description }}"
                 data-action="{{ route('owner.roles.update', $role->id) }}"
                 data-permissions="{{ $role->permissions->pluck('id')->implode(',') }}"></div>
            @endif

        </div>
        @endforeach

    </div>

</div>

@php
    $permCheckboxes = function($groups, $prefix, $checkedIds = []) {
        foreach ($groups as $group => $perms) {
            echo '<div class="mb-3"><p class="mb-1 text-xs font-bold tracking-wide text-gray-400 uppercase">'.$group.'</p><div class="flex flex-wrap gap-2">';
            foreach ($perms as $perm) {
                $checked = in_array($perm->id, $checkedIds) ? 'checked' : '';
                echo '<label class="flex items-center gap-2 px-3 py-1.5 text-xs font-medium bg-gray-100 rounded-lg cursor-pointer dark:bg-dark '.$prefix.'-perm-label">
                        <input type="checkbox" name="permissions[]" value="'.$perm->id.'" class="rounded text-primary '.$prefix.'-perm-checkbox" '.$checked.'>
                        '.$perm->name.'
                      </label>';
            }
            echo '</div></div>';
        }
    };
@endphp

<!-- CREATE ROLE MODAL -->
<div id="createRoleModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-2xl overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Uruhare Rushya</h3>
            <button onclick="document.getElementById('createRoleModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('owner.roles.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto max-h-[65vh]">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Izina</label>
                    <input type="text" name="name" required placeholder="e.g. Content Manager"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ibisobanuro</label>
                    <input type="text" name="description"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Uburenganzira</label>
                    {!! $permCheckboxes($permissions, 'create') !!}
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('createRoleModal').classList.add('hidden')"
                    class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl dark:bg-gray-700 dark:text-gray-200">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT ROLE MODAL -->
<div id="editRoleModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-2xl overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Hindura Uruhare</h3>
            <button onclick="document.getElementById('editRoleModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editRoleForm" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto max-h-[65vh]">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Izina</label>
                    <input type="text" name="name" id="edit_role_name" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Ibisobanuro</label>
                    <input type="text" name="description" id="edit_role_description"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Uburenganzira</label>
                    {!! $permCheckboxes($permissions, 'edit') !!}
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('editRoleModal').classList.add('hidden')"
                    class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl dark:bg-gray-700 dark:text-gray-200">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika Impinduka</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditRoleModal(id) {
    const box = document.getElementById('role-data-' + id);
    document.getElementById('edit_role_name').value = box.dataset.name;
    document.getElementById('edit_role_description').value = box.dataset.description;
    document.getElementById('editRoleForm').action = box.dataset.action;

    const checkedIds = box.dataset.permissions ? box.dataset.permissions.split(',') : [];
    document.querySelectorAll('.edit-perm-checkbox').forEach(cb => {
        cb.checked = checkedIds.includes(cb.value);
    });

    document.getElementById('editRoleModal').classList.remove('hidden');
}
</script>

@endsection
