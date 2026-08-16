@extends('Users.User.cover')
@section('title', 'Igenamiterere')

@section('content')
<div class="p-4 md:p-6">

    <h1 class="mb-6 text-2xl font-bold" style="color:#0B3D2E">Igenamiterere</h1>

    <div class="max-w-xl p-6 bg-white shadow-lg rounded-3xl">
        <h2 class="mb-4 font-bold" style="color:#0B3D2E">Hindura Ijambo ry'Ibanga</h2>

        <form action="{{ route('student.settings.password') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block mb-1 text-sm font-semibold text-gray-700">Ijambo ry'ibanga rya none</label>
                <input type="password" name="current_password" required class="w-full px-4 py-3 border rounded-2xl">
            </div>

            <div class="mb-4">
                <label class="block mb-1 text-sm font-semibold text-gray-700">Ijambo ry'ibanga rishya</label>
                <input type="password" name="password" required minlength="6" class="w-full px-4 py-3 border rounded-2xl">
            </div>

            <div class="mb-6">
                <label class="block mb-1 text-sm font-semibold text-gray-700">Emeza Ijambo ry'ibanga rishya</label>
                <input type="password" name="password_confirmation" required minlength="6" class="w-full px-4 py-3 border rounded-2xl">
            </div>

            <button type="submit" class="px-6 py-3 font-semibold text-white rounded-xl" style="background:#0B3D2E">
                Hindura Ijambo ry'Ibanga
            </button>
        </form>
    </div>

    <div class="max-w-xl p-6 mt-6 bg-white shadow-lg rounded-3xl">
        <h2 class="mb-2 font-bold" style="color:#0B3D2E">Notification Preferences</h2>
        <p class="text-sm text-gray-400">
            Iyi nzira ntiyakozwe ubu — igihe sisitemu y'imenyesha (notifications) izaba yashyizweho, uzashobora
            guhitamo hano uburyo wifuza kumenyeshwa.
        </p>
    </div>
</div>
@endsection
