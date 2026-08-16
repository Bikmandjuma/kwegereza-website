@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex items-center gap-4 mb-6">
        <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
            <i class="fa-solid fa-clipboard-list"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Audit Logs</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: <strong>{{ $logs->total() }}</strong></p>
        </div>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <select name="entity_type" onchange="this.form.submit()"
            class="px-4 py-2 border rounded-xl dark:bg-dark dark:border-gray-700 dark:text-white">
            <option value="">All content types</option>
            @foreach($entityTypes as $type)
                <option value="{{ $type }}" {{ $entityType === $type ? 'selected' : '' }}>{{ class_basename($type) }}</option>
            @endforeach
        </select>

        <select name="action" onchange="this.form.submit()"
            class="px-4 py-2 border rounded-xl dark:bg-dark dark:border-gray-700 dark:text-white">
            <option value="">All actions</option>
            <option value="created" {{ $action === 'created' ? 'selected' : '' }}>Created</option>
            <option value="updated" {{ $action === 'updated' ? 'selected' : '' }}>Updated</option>
            <option value="deleted" {{ $action === 'deleted' ? 'selected' : '' }}>Deleted</option>
        </select>
    </form>

    <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-dark dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3">When</th>
                    <th class="px-5 py-3">Who</th>
                    <th class="px-5 py-3">Action</th>
                    <th class="px-5 py-3">Content</th>
                    <th class="px-5 py-3">Item</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @forelse($logs as $log)
                <tr>
                    <td class="px-5 py-3 text-gray-400">{{ $log->created_at->diffForHumans() }}</td>
                    <td class="px-5 py-3">{{ $log->owner ? $log->owner->firstname.' '.$log->owner->lastname : 'System' }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full
                            {{ $log->action === 'created' ? 'bg-green-100 text-green-700' : ($log->action === 'deleted' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ strtoupper($log->action) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $log->entityTypeLabel() }}</td>
                    <td class="px-5 py-3 font-semibold text-primary-dark dark:text-light">{{ $log->entity_label ?? ('#'.$log->entity_id) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-10 text-center text-gray-400">No audit activity recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $logs->links() }}</div>

</div>

@endsection
