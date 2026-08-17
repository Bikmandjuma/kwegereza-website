@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex items-center gap-4 mb-6">
        <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
            <i class="fa-solid fa-comments"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Ibitekerezo (Comment Moderation)</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Raporo zitegereje: <strong>{{ $reported->count() }}</strong></p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    @if($reported->count())
    <h2 class="mb-3 font-bold text-red-600">Ibyaratanzweho Raporo</h2>
    <div class="mb-8 space-y-3">
        @foreach($reported as $commentId => $reports)
            @php $comment = $reports->first()->comment; @endphp
            @if($comment)
            <div class="p-5 bg-white border-2 border-red-200 shadow rounded-2xl dark:bg-darker">
                <p class="mb-1 text-xs text-gray-400">{{ $comment->user->firstname ?? 'Umunyeshuri' }} · {{ class_basename($comment->commentable_type) }}</p>
                <p class="mb-3 text-sm">{{ $comment->content }}</p>
                <p class="mb-3 text-xs font-semibold text-red-600">{{ $reports->count() }} raporo: "{{ $reports->pluck('reason')->filter()->implode('", "') ?: 'nta mpamvu yatanzwe' }}"</p>

                <div class="flex gap-2">
                    <form action="{{ route('owner.comments.approve', $comment->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="px-4 py-2 text-xs font-bold text-white bg-green-600 rounded-xl">Emeza (Approve)</button>
                    </form>
                    <form action="{{ route('owner.comments.hide', $comment->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="px-4 py-2 text-xs font-bold text-white bg-gray-500 rounded-xl">Hisha (Hide)</button>
                    </form>
                    <form action="{{ route('owner.comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Gusiba burundu?')">
                        @csrf @method('DELETE')
                        <button class="px-4 py-2 text-xs font-bold text-white bg-red-600 rounded-xl">Siba Burundu</button>
                    </form>
                </div>
            </div>
            @endif
        @endforeach
    </div>
    @endif

    <h2 class="mb-3 font-bold text-primary-dark dark:text-light">Ibitekerezo Byose (Recent)</h2>
    <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-dark dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3">Umunyeshuri</th>
                    <th class="px-5 py-3">Igitekerezo</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Ibikorwa</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @foreach($recent as $comment)
                <tr>
                    <td class="px-5 py-3 font-semibold text-primary-dark dark:text-light">{{ $comment->user->firstname ?? '—' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ \Illuminate\Support\Str::limit($comment->content, 60) }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full {{ $comment->status === 'approved' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' }}">
                            {{ strtoupper($comment->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <form action="{{ route('owner.comments.destroy', $comment->id) }}" method="POST" class="inline" onsubmit="return confirm('Gusiba?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $recent->links() }}</div>

</div>

@endsection
