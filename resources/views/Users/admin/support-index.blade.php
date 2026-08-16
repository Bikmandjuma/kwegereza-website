@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex items-center gap-4 mb-6">
        <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
            <i class="fa-solid fa-headset"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Ubufasha (Support Tickets)</h1>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('owner.support') }}" class="px-4 py-2 text-xs font-bold rounded-full {{ !$status ? 'bg-primary text-white' : 'bg-gray-200 text-gray-600' }}">
            Byose
        </a>
        <a href="{{ route('owner.support', ['status' => 'open']) }}" class="px-4 py-2 text-xs font-bold rounded-full {{ $status === 'open' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-600' }}">
            Open ({{ $counts['open'] }})
        </a>
        <a href="{{ route('owner.support', ['status' => 'in_progress']) }}" class="px-4 py-2 text-xs font-bold rounded-full {{ $status === 'in_progress' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-600' }}">
            In Progress ({{ $counts['in_progress'] }})
        </a>
        <a href="{{ route('owner.support', ['status' => 'resolved']) }}" class="px-4 py-2 text-xs font-bold rounded-full {{ $status === 'resolved' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-600' }}">
            Resolved ({{ $counts['resolved'] }})
        </a>
        <a href="{{ route('owner.support', ['status' => 'closed']) }}" class="px-4 py-2 text-xs font-bold rounded-full {{ $status === 'closed' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-600' }}">
            Closed ({{ $counts['closed'] }})
        </a>
    </div>

    <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-dark dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3">Ticket</th>
                    <th class="px-5 py-3">Umunyeshuri</th>
                    <th class="px-5 py-3">Umutwe</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Itariki</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @forelse($tickets as $ticket)
                <tr class="cursor-pointer hover:bg-gray-50 dark:hover:bg-dark" onclick="window.location='{{ route('owner.support.show', $ticket->id) }}'">
                    <td class="px-5 py-3 font-mono text-xs">{{ $ticket->ticket_number }}</td>
                    <td class="px-5 py-3 font-semibold text-primary-dark dark:text-light">{{ $ticket->user->firstname ?? '—' }} {{ $ticket->user->lastname ?? '' }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ \Illuminate\Support\Str::limit($ticket->subject, 40) }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full
                            {{ $ticket->status === 'open' ? 'bg-yellow-100 text-yellow-700' :
                               ($ticket->status === 'in_progress' ? 'bg-blue-100 text-blue-700' :
                               ($ticket->status === 'resolved' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500')) }}">
                            {{ strtoupper(str_replace('_',' ', $ticket->status)) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-400">{{ $ticket->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-10 text-center text-gray-400">Nta ma ticket ahari.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $tickets->links() }}</div>

</div>

@endsection
