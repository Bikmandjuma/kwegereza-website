@extends('Users.admin.cover')

@section('content')

<div class="max-w-2xl p-4 mx-auto md:p-6">

    <div class="p-6 mb-4 bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <p class="mb-1 text-xs font-mono text-gray-400">{{ $ticket->ticket_number }}</p>
        <h1 class="mb-2 text-xl font-bold text-primary-dark dark:text-light">{{ $ticket->subject }}</h1>
        <p class="mb-3 text-xs text-gray-400">
            {{ $ticket->user->firstname ?? '—' }} {{ $ticket->user->lastname ?? '' }} ·
            {{ ucfirst($ticket->category) }} ·
            {{ $ticket->created_at->diffForHumans() }}
        </p>

        <form action="{{ route('owner.support.updateStatus', $ticket->id) }}" method="POST" class="flex items-center gap-2">
            @csrf @method('PATCH')
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 text-sm border rounded-xl dark:bg-dark dark:border-gray-700 dark:text-white">
                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </form>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="mb-4 space-y-3">
        @foreach($ticket->replies as $reply)
        <div class="p-4 rounded-2xl {{ $reply->sender_type === 'owner' ? 'text-white' : 'bg-white shadow dark:bg-darker' }}"
             style="{{ $reply->sender_type === 'owner' ? 'background:#058e48;' : '' }}">
            <p class="mb-1 text-xs font-bold {{ $reply->sender_type === 'owner' ? 'opacity-80' : 'text-gray-400' }}">
                {{ $reply->senderName() }} · {{ $reply->created_at->diffForHumans() }}
            </p>
            <p class="text-sm">{{ $reply->message }}</p>
        </div>
        @endforeach
    </div>

    <form action="{{ route('owner.support.reply', $ticket->id) }}" method="POST" class="p-5 bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        @csrf
        <textarea name="message" required rows="3" placeholder="Andika igisubizo..." class="w-full px-4 py-3 mb-3 border rounded-2xl focus:ring-2 dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
        <button type="submit" class="px-6 py-2.5 font-semibold text-white rounded-xl bg-primary">Ohereza Igisubizo</button>
    </form>

</div>

@endsection
