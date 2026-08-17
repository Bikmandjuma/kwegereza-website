@extends('Users.User.cover')
@section('title', 'Ubufasha (Support)')

@section('content')
<div class="p-4 md:p-6">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold" style="color:#0B3D2E">Ubufasha (Support Tickets)</h1>
        <a href="{{ route('student.support.create') }}" class="px-5 py-2.5 text-sm font-semibold text-white rounded-xl" style="background:#0B6D20">
            <i class="fa-solid fa-plus"></i> Tanga Ikibazo Gishya
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    @if($tickets->isEmpty())
        <div class="p-10 text-center bg-white shadow-lg rounded-3xl text-gray-400">
            Ntabwo urarasaba ubufasha. Kanda "Tanga Ikibazo Gishya" hejuru niba ufite ikibazo.
        </div>
    @else
        <div class="space-y-3">
            @foreach($tickets as $ticket)
            <a href="{{ route('student.support.show', $ticket->id) }}" class="flex items-center justify-between p-5 bg-white shadow-lg rounded-3xl">
                <div>
                    <p class="mb-1 text-xs font-bold text-gray-400">{{ $ticket->ticket_number }}</p>
                    <h2 class="font-bold" style="color:#0B3D2E">{{ $ticket->subject }}</h2>
                    <p class="mt-1 text-xs text-gray-400">{{ $ticket->created_at->diffForHumans() }}</p>
                </div>
                <span class="px-3 py-1 text-xs font-bold rounded-full
                    {{ $ticket->status === 'open' ? 'bg-yellow-100 text-yellow-700' :
                       ($ticket->status === 'in_progress' ? 'bg-blue-100 text-blue-700' :
                       ($ticket->status === 'resolved' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500')) }}">
                    {{ strtoupper(str_replace('_',' ', $ticket->status)) }}
                </span>
            </a>
            @endforeach
        </div>
    @endif

</div>
@endsection
