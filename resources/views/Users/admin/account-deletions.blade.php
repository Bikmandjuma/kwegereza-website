@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex items-center gap-4 mb-6">
        <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
            <i class="fa-solid fa-user-slash"></i>
        </div>
        <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Ibyifuzo byo Gusiba Konti</h1>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-dark dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3">Umunyeshuri</th>
                    <th class="px-5 py-3">Impamvu</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Itariki</th>
                    <th class="px-5 py-3 text-right">Ibikorwa</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @forelse($requests as $req)
                <tr>
                    <td class="px-5 py-3 font-semibold text-primary-dark dark:text-light">{{ $req->user->firstname ?? '—' }} {{ $req->user->lastname ?? '' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ \Illuminate\Support\Str::limit($req->reason, 50) ?: '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full
                            {{ $req->status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                               ($req->status === 'approved' ? 'bg-red-100 text-red-700' : 'bg-gray-200 text-gray-500') }}">
                            {{ strtoupper($req->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-400">{{ $req->created_at->diffForHumans() }}</td>
                    <td class="px-5 py-3 text-right">
                        @if($req->status === 'pending')
                            <form action="{{ route('owner.accountDeletions.approve', $req->id) }}" method="POST" class="inline" onsubmit="return confirm('Emeza guhagarika iyi konti?')">
                                @csrf @method('PATCH')
                                <button class="px-3 py-1.5 text-xs font-bold text-white bg-red-600 rounded-lg">Emeza</button>
                            </form>
                            <form action="{{ route('owner.accountDeletions.reject', $req->id) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button class="px-3 py-1.5 text-xs font-bold text-gray-600 bg-gray-200 rounded-lg">Anga</button>
                            </form>
                        @else
                            <span class="text-xs text-gray-400">{{ $req->processed_at?->diffForHumans() }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-10 text-center text-gray-400">Nta byifuzo bihari.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $requests->links() }}</div>

</div>

@endsection
