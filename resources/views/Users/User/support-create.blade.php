@extends('Users.User.cover')
@section('title', 'Tanga Ikibazo')

@section('content')
<div class="max-w-lg p-4 mx-auto md:p-6">

    <div class="p-6 bg-white shadow-lg rounded-3xl">
        <h1 class="mb-4 text-xl font-bold" style="color:#0B3D2E">Tanga Ikibazo Gishya</h1>

        <form action="{{ route('student.support.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-1 text-sm font-semibold text-gray-700">Umutwe w'Ikibazo</label>
                <input type="text" name="subject" required maxlength="255" placeholder="Andika muri make ikibazo cyawe"
                    class="w-full px-4 py-3 border rounded-2xl focus:ring-2">
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-sm font-semibold text-gray-700">Ubwoko bw'Ikibazo</label>
                <select name="category" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2">
                    <option value="technical">Ikoranabuhanga (Technical)</option>
                    <option value="content">Ibirimo (Content)</option>
                    <option value="account">Konti Yanjye (Account)</option>
                    <option value="other">Ikindi</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-sm font-semibold text-gray-700">Sobanura Ikibazo</label>
                <textarea name="message" required rows="5" maxlength="3000"
                    class="w-full px-4 py-3 border rounded-2xl focus:ring-2"></textarea>
            </div>
            <button type="submit" class="w-full py-3 font-semibold text-white rounded-2xl" style="background:#0B6D20;">
                Ohereza Ikibazo
            </button>
        </form>
    </div>

</div>
@endsection
