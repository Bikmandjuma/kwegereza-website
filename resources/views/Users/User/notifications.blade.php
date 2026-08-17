@extends('Users.User.cover')
@section('title', 'Ibimenyetso')

@section('content')
<div class="p-4 md:p-6">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold" style="color:#0B3D2E">Ibimenyetso (Notifications)</h1>

        @if($notifications->where('read_at', null)->count())
        <form action="{{ route('student.notifications.readAll') }}" method="POST">
            @csrf
            <button class="px-4 py-2 text-sm font-semibold rounded-xl" style="background:#C9A227;color:#0B3D2E">
                Shyira byose nk'ibyasomwe
            </button>
        </form>
        @endif
    </div>

    <div class="space-y-3">
        @forelse($notifications as $n)
        <a href="{{ $n->data['url'] ?? '#' }}"
           onclick="fetch('{{ route('student.notifications.read', $n->id) }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})"
           class="flex items-start w-full gap-3 p-4 bg-white shadow cursor-pointer rounded-2xl {{ $n->read_at ? 'opacity-60' : '' }}">
            <span class="mt-1 w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: {{ $n->read_at ? '#ccc' : '#0B6D20' }}"></span>
            <div>
                <p class="font-semibold" style="color:#0B3D2E">{{ $n->data['title'] ?? 'Itangazo' }}</p>
                <p class="text-sm text-gray-500">{{ $n->data['message'] ?? '' }}</p>
                <p class="mt-1 text-xs text-gray-400">{{ $n->created_at->diffForHumans() }}</p>
            </div>
        </a>
        @empty
        <div class="p-10 text-center bg-white shadow-lg rounded-3xl text-gray-400">
            Nta bimenyetso urabona ubu.
        </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $notifications->links() }}</div>

</div>
@endsection
