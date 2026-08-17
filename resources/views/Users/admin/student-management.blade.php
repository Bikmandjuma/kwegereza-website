@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex items-center gap-4 mb-6">
        <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
            <i class="fa-solid fa-user-graduate"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Abanyeshuri (Students)</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Total: <strong>{{ $students->total() }}</strong></p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <form method="GET" class="mb-5">
        <input type="text" name="search" value="{{ $search }}" placeholder="Shakisha izina, email, cyangwa telefone..."
            class="w-full max-w-md px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
    </form>

    <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-dark dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3">Izina</th>
                    <th class="px-5 py-3">Email / Telefone</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Yiyandikishije</th>
                    <th class="px-5 py-3 text-right">Ibikorwa</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @forelse($students as $student)
                <tr>
                    <td class="px-5 py-3 font-semibold text-primary-dark dark:text-light">
                        <a href="{{ route('owner.students.show', $student->id) }}" class="hover:underline">
                            {{ $student->firstname }} {{ $student->lastname }}
                        </a>
                    </td>
                    <td class="px-5 py-3 text-gray-500">{{ $student->email ?: $student->phone }}</td>
                    <td class="px-5 py-3">
                        @if($student->deactivated_at)
                            <span class="px-2 py-0.5 text-xs font-bold text-red-700 bg-red-100 rounded-full">BLOCKED</span>
                        @else
                            <span class="px-2 py-0.5 text-xs font-bold text-green-700 bg-green-100 rounded-full">ACTIVE</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-400">{{ $student->created_at?->format('M j, Y') }}</td>
                    <td class="px-5 py-3 text-right">
                        <a href="{{ route('owner.students.show', $student->id) }}" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-primary/10 text-primary-dark">
                            Reba
                        </a>
                        @if($student->deactivated_at)
                            <form action="{{ route('owner.students.unblock', $student->id) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button class="px-3 py-1.5 text-xs font-bold text-white bg-green-600 rounded-lg">Kuraho Block</button>
                            </form>
                        @else
                            <form action="{{ route('owner.students.block', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Emeza guhagarika iyi konti?')">
                                @csrf @method('PATCH')
                                <button class="px-3 py-1.5 text-xs font-bold text-white bg-red-600 rounded-lg">Block</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-10 text-center text-gray-400">Nta munyeshuri uboneka.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $students->links() }}</div>

</div>

@endsection
