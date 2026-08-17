@extends('Users.User.cover')
@section('title', 'Umwirondoro')

@section('content')
<div class="p-4 md:p-6">

    <h1 class="mb-6 text-2xl font-bold" style="color:#094939">Umwirondoro</h1>

    <div class="max-w-xl p-6 bg-white shadow-lg rounded-3xl">

        @if($user->image)
            <img src="{{ \App\Support\FileUrl::resolve($user->image, 'students', 'uploads/students') ?? asset('Guest/images/logo.png') }}" class="object-cover w-20 h-20 mb-4 rounded-full">
        @else
            <div class="flex items-center justify-center w-20 h-20 mb-4 text-2xl font-bold text-white rounded-full" style="background:#058e48">
                {{ strtoupper(substr($user->firstname ?? 'U', 0, 1)) }}
            </div>
        @endif

        <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-4 mb-4 md:grid-cols-2">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700">Izina</label>
                    <input type="text" name="firstname" value="{{ old('firstname', $user->firstname) }}" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2" style="--tw-ring-color:#058e48">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700">Irindi zina</label>
                    <input type="text" name="lastname" value="{{ old('lastname', $user->lastname) }}" required
                        class="w-full px-4 py-3 border rounded-2xl">
                </div>
            </div>

            <div class="mb-4">
                <label class="block mb-1 text-sm font-semibold text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full px-4 py-3 border rounded-2xl">
                @error('email')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 text-sm font-semibold text-gray-700">Telefone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                    class="w-full px-4 py-3 border rounded-2xl">
                @error('phone')<p class="mt-1 text-sm text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 text-sm font-semibold text-gray-700">Igitsina</label>
                <select name="gender" class="w-full px-4 py-3 border rounded-2xl">
                    <option value="">-- Hitamo --</option>
                    <option value="male" {{ $user->gender === 'male' ? 'selected' : '' }}>Gabo</option>
                    <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}>Gore</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block mb-1 text-sm font-semibold text-gray-700">Ifoto y'Umwirondoro</label>
                <input type="file" name="image" accept="image/*" class="w-full px-4 py-3 border rounded-2xl">
            </div>

            <button type="submit" class="px-6 py-3 font-semibold text-white rounded-xl" style="background:#094939">
                Bika Impinduka
            </button>
        </form>

    </div>
</div>
@endsection
