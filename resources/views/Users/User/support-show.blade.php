@extends('Users.User.cover')
@section('title', $ticket->ticket_number)

@section('content')
<div class="max-w-2xl p-4 mx-auto md:p-6">

    <div class="p-6 mb-4 bg-white shadow-lg rounded-3xl">
        <p class="mb-1 text-xs font-bold text-gray-400">{{ $ticket->ticket_number }}</p>
        <h1 class="mb-2 text-xl font-bold" style="color:#0B3D2E">{{ $ticket->subject }}</h1>
        <span class="px-3 py-1 text-xs font-bold rounded-full
            {{ $ticket->status === 'open' ? 'bg-yellow-100 text-yellow-700' :
               ($ticket->status === 'in_progress' ? 'bg-blue-100 text-blue-700' :
               ($ticket->status === 'resolved' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500')) }}">
            {{ strtoupper(str_replace('_',' ', $ticket->status)) }}
        </span>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="mb-4 space-y-3">
        @foreach($ticket->replies as $reply)
        <div class="p-4 rounded-2xl {{ $reply->sender_type === 'owner' ? 'bg-white shadow' : 'text-white' }}"
             style="{{ $reply->sender_type === 'owner' ? '' : 'background:#0B6D20;' }}">
            <p class="mb-1 text-xs font-bold {{ $reply->sender_type === 'owner' ? 'text-gray-400' : 'opacity-80' }}">
                {{ $reply->senderName() }} · {{ $reply->created_at->diffForHumans() }}
            </p>
            <p class="text-sm">{{ $reply->message }}</p>
        </div>
        @endforeach
    </div>

    <form action="{{ route('student.support.reply', $ticket->id) }}" method="POST" class="p-5 bg-white shadow-lg rounded-3xl">
        @csrf
        <textarea name="message" required rows="3" placeholder="Andika ubutumwa..." class="w-full px-4 py-3 mb-3 border rounded-2xl focus:ring-2"></textarea>
        <button type="submit" class="px-6 py-2.5 font-semibold text-white rounded-xl" style="background:#0B6D20;">Ohereza</button>
    </form>

</div>
@endsection
