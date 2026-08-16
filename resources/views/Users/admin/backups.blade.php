@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
                <i class="fa-solid fa-database"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Backup & Recovery</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: <strong>{{ count($backups) }}</strong> backup(s)</p>
            </div>
        </div>

        <form action="{{ route('owner.backups.store') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white shadow-md rounded-xl bg-primary" onclick="this.disabled=true; this.innerHTML='<i class=\'fa-solid fa-spinner fa-spin\'></i> Kurema Backup...'; this.form.submit();">
                <i class="fa-solid fa-plus"></i> Rema Backup Nshya
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 mb-5 font-medium text-red-700 bg-red-100 rounded-xl">{{ session('error') }}</div>
    @endif

    <div class="p-4 mb-5 text-sm text-yellow-800 bg-yellow-50 rounded-2xl">
        <i class="fa-solid fa-triangle-exclamation"></i>
        Iyi backup ni dosiye ya SQL ikubiyemo amakuru yose y'urubuga, harimo n'amabanga ya konti (password hashes).
        Kuraho no kubika neza — ntukagishyire ahagaragarira rubanda cyangwa uyohereze binyuze muri imeyili itarinzwe.
        Kugarura (restore) ntibikorwa mu buryo bwikora hano — bisaba ko uyishyira mu bubiko bw'amakuru ubwawe hifashishijwe umuyoboro
        ufite ubushobozi (Database client), ni ikintu cy'ingenzi cyane ku buryo kitagomba gukorwa ako kanya n'urwego rw'ikoranabuhanga rimwe.
    </div>

    <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-dark dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3">Dosiye</th>
                    <th class="px-5 py-3">Ingano</th>
                    <th class="px-5 py-3">Itariki</th>
                    <th class="px-5 py-3 text-right">Ibikorwa</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @forelse($backups as $backup)
                <tr>
                    <td class="px-5 py-3 font-mono text-xs text-primary-dark dark:text-light">{{ $backup['filename'] }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ number_format($backup['size'] / 1048576, 2) }} MB</td>
                    <td class="px-5 py-3 text-gray-400">{{ $backup['created_at']->format('M j, Y g:i A') }}</td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('owner.backups.download', $backup['filename']) }}" class="px-3 py-1.5 text-xs font-bold text-white rounded-lg bg-primary">
                            <i class="fa-solid fa-download"></i> Kuraho
                        </a>
                        <form action="{{ route('owner.backups.destroy', $backup['filename']) }}" method="POST" class="inline" onsubmit="return confirm('Gusiba iyi backup burundu?')">
                            @csrf @method('DELETE')
                            <button class="px-3 py-1.5 text-xs font-bold text-red-600 bg-red-50 rounded-lg">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="p-10 text-center text-gray-400">Nta backup irahaboneka. Kanda "Rema Backup Nshya" hejuru.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
