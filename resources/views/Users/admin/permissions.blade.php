@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
                <i class="fa-solid fa-key"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Permissions</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: <strong>{{ $permissions->total() }}</strong></p>
            </div>
        </div>

        <button onclick="document.getElementById('createPermModal').classList.remove('hidden')"
            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white shadow-md rounded-xl bg-primary">
            <i class="fa-solid fa-plus"></i> Ongeraho Uburenganzira
        </button>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-dark dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Slug</th>
                    <th class="px-5 py-3">Group</th>
                    <th class="px-5 py-3">Roles using it</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @forelse($permissions as $perm)
                <tr data-name="{{ $perm->name }}" data-slug="{{ $perm->slug }}" data-group="{{ $perm->group }}"
                    data-action="{{ route('owner.permissions.update', $perm->id) }}" id="perm-row-{{ $perm->id }}">
                    <td class="px-5 py-3 font-semibold text-primary-dark dark:text-light">{{ $perm->name }}</td>
                    <td class="px-5 py-3"><code class="px-2 py-1 text-xs bg-gray-100 rounded dark:bg-dark">{{ $perm->slug }}</code></td>
                    <td class="px-5 py-3 text-gray-500">{{ $perm->group }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $perm->roles_count }}</td>
                    <td class="px-5 py-3 text-right">
                        <button onclick="openEditPermModal({{ $perm->id }})" class="mr-2 text-primary-dark"><i class="fa-solid fa-pen"></i></button>
                        <form action="{{ route('owner.permissions.destroy', $perm->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Uremeza gusiba ubu burenganzira?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-10 text-center text-gray-400">Nta buburenganzira burahaboneka.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $permissions->links() }}</div>

</div>

<!-- CREATE -->
<div id="createPermModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-md overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Uburenganzira Bushya</h3>
            <button onclick="document.getElementById('createPermModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="{{ route('owner.permissions.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4 p-6">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" name="name" required placeholder="e.g. Create Darsat"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Slug (module.action)</label>
                    <input type="text" name="slug" required placeholder="e.g. darsat.create"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                    @error('slug')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Group (optional)</label>
                    <input type="text" name="group" placeholder="e.g. darsat"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('createPermModal').classList.add('hidden')"
                    class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT -->
<div id="editPermModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-md overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Hindura Uburenganzira</h3>
            <button onclick="document.getElementById('editPermModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>
        <form id="editPermForm" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-4 p-6">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" name="name" id="edit_perm_name" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Slug</label>
                    <input type="text" name="slug" id="edit_perm_slug" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Group</label>
                    <input type="text" name="group" id="edit_perm_group"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('editPermModal').classList.add('hidden')"
                    class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Hagarika</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Bika Impinduka</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditPermModal(id) {
    const row = document.getElementById('perm-row-' + id);
    document.getElementById('edit_perm_name').value = row.dataset.name;
    document.getElementById('edit_perm_slug').value = row.dataset.slug;
    document.getElementById('edit_perm_group').value = row.dataset.group;
    document.getElementById('editPermForm').action = row.dataset.action;
    document.getElementById('editPermModal').classList.remove('hidden');
}
</script>

@endsection
