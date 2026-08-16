@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <div class="flex items-center gap-4 mb-6">
        <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
            <i class="fa-solid fa-toggle-on"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-primary-dark dark:text-light">Feature Flags</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Shyiraho cyangwa uhagarike ibice bimwe by'urubuga nta kode uhinduye.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 mb-5 font-medium text-green-700 bg-green-100 rounded-xl">{{ session('success') }}</div>
    @endif

    <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-dark dark:text-gray-400">
                <tr>
                    <th class="px-5 py-3">Feature</th>
                    <th class="px-5 py-3">Ibisobanuro</th>
                    <th class="px-5 py-3 text-right">Uko rihagaze</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @foreach($flags as $flag)
                <tr>
                    <td class="px-5 py-3 font-semibold text-primary-dark dark:text-light">{{ $flag->label }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $flag->description }}</td>
                    <td class="px-5 py-3 text-right">
                        <form action="{{ route('owner.featureFlags.toggle', $flag->id) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button class="px-4 py-2 text-xs font-bold rounded-full {{ $flag->is_enabled ? 'bg-primary text-white' : 'bg-gray-200 text-gray-500' }}">
                                {{ $flag->is_enabled ? 'ON' : 'OFF' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection
