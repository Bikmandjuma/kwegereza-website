@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">

        <div class="flex items-center gap-4">

            <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
                <i class="text-2xl fa-solid fa-microphone-lines"></i>
            </div>

            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white text-primary-dark dark:text-light">
                    Add New Lesson
                </h1>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Upload Islamic audio lessons.
                </p>
            </div>

        </div>

        <button
                onclick="window.location='{{ route("owner.viewDarsat") }}'"
                type="button"
                class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white transition shadow-md rounded-xl bg-primary hover:scale-[1.02]"
            >
                <i class="fa-solid fa-list-alt"></i>
                View darsat
            </button>

    </div>

    <!-- CARD -->
    <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">

        <!-- CARD HEADER -->
        <div class="flex items-center gap-3 px-6 py-5 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">

            <div class="flex items-center justify-center w-12 h-12 text-white rounded-2xl bg-primary">
                <i class="fa-solid fa-book-open"></i>
            </div>

            <div>
                <h2 class="text-lg font-bold text-gray-800 text-primary-dark dark:text-light">
                    Lesson Information
                </h2>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Fill all lesson details below.
                </p>
            </div>

        </div>

        <!-- FORM -->
        <!-- <form action="{{ route('owner.storeDarsat') }}" method="POST" enctype="multipart/form-data"> -->
        <form id="uploadForm" action="{{ route('owner.storeDarsat') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                <!-- LESSON TITLE -->
                <div class="md:col-span-2">

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Lesson Title
                    </label>

                    <div class="relative">

                        <span class="absolute text-gray-400 left-4 top-1/4 -translate-y-1/2">
                            <i class="fa-solid fa-heading"></i>
                        </span>

                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="Ex : Ibisingizo bya mbere yo kuryama"
                            class="w-full py-3.5 pl-12 pr-4 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:outline-none dark:bg-dark dark:border-gray-700 dark:text-white"
                        >

                    </div>

                    @error('title')
                        <p class="mt-2 text-sm text-red-500 dark:text-light">{{ $message }}</p>
                    @enderror

                </div>

                <!-- LESSON TYPE -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Lesson Type
                    </label>

                    <div class="relative">

                        <span class="absolute text-gray-400 left-4 top-1/4 -translate-y-1/2">
                            <i class="fa-solid fa-layer-group"></i>
                        </span>

                        <select
                            name="type"
                            class="w-full py-3.5 pl-12 pr-4 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:outline-none dark:bg-dark dark:border-gray-700 dark:text-white"
                        >

                            <option value="">Select Lesson Type</option>

                            <option value="Tawhid">Tawhid</option>
                            <option value="Fiqh">Fiqh</option>
                            <option value="Hadith">Hadith</option>
                            <option value="Tafsir">Tafsir</option>
                            <option value="Aqidah">Aqidah</option>
                            <option value="Seerah">Seerah</option>
                            <option value="Akhlaq">Akhlaq</option>

                        </select>

                    </div>

                    @error('type')
                        <p class="mt-2 text-sm text-red-500 dark:text-light">{{ $message }}</p>
                    @enderror

                </div>

                <!-- AUDIO FILE -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Lesson Audio
                    </label>

                    <div class="relative">

                        <span class="absolute text-gray-400 left-4 top-1/4 -translate-y-1/2">
                            <i class="fa-solid fa-file-audio"></i>
                        </span>

                        <input
                            type="file"
                            name="audio"
                            accept=".mp3,.wav,.ogg,.m4a"
                            class="w-full py-3 pl-12 pr-4 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:outline-none dark:bg-dark dark:border-gray-700 dark:text-white"
                        >

                    </div>

                    @error('audio')
                        <p class="mt-2 text-sm text-red-500 dark:text-light">{{ $message }}</p>
                    @enderror

                </div>

                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        List of Teachers
                    </label>

                    <div class="relative">

                        <span class="absolute text-gray-400 left-4 top-1/4 -translate-y-1/2">
                            <i class="fa-solid fa-list-alt"></i>
                        </span>

                        <select
                            name="teachers"
                            class="w-full py-3.5 pl-12 pr-4 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-primary focus:outline-none dark:bg-dark dark:border-gray-700 dark:text-white"
                        >

                            <option value="">Select teacher</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id}}">{{ $user->title}} {{ $user->firstname}} {{$user->lastname}}</option>
                            @endforeach
                        </select>

                    </div>

                    @error('teachers')
                        <p class="mt-2 text-sm text-red-500 dark:text-light">{{ $message }}</p>
                    @enderror

                </div>

                <!-- AUDIO FILE -->
                <div>

                    <div class="flex flex-col-reverse gap-3 px-6 py-5 border-t bg-gray-50 md:flex-row md:justify-end dark:bg-dark dark:border-gray-700">

                <button
                    type="reset"
                    class="flex items-center justify-center gap-2 px-5 py-3 font-semibold text-white bg-red-500 rounded-2xl hover:bg-red-600"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                    Reset
                </button>

                <!-- <button
                    type="submit"
                    class="flex items-center justify-center gap-2 px-6 py-3 font-semibold text-white shadow-md rounded-2xl bg-primary hover:scale-[1.02]"
                >
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    Upload Lesson
                </button> -->
                <button
                    id="uploadBtn"
                    type="submit"
                    class="flex items-center justify-center gap-2 px-6 py-3 font-semibold text-white transition shadow-md rounded-2xl bg-primary hover:scale-[1.02] disabled:opacity-70 disabled:cursor-not-allowed"
                >
                    <span id="uploadIcon">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </span>

                    <svg
                        id="uploadSpinner"
                        class="hidden w-5 h-5 animate-spin"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4">
                        </circle>

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                        </path>
                    </svg>

                    <span id="uploadText">
                        Upload Lesson
                    </span>
                </button>

            </div>

                </div>

            </div>

        </form>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('uploadForm');
    const btn = document.getElementById('uploadBtn');
    const text = document.getElementById('uploadText');
    const spinner = document.getElementById('uploadSpinner');
    const icon = document.getElementById('uploadIcon');

    form.addEventListener('submit', function () {

        btn.disabled = true;

        icon.classList.add('hidden');
        spinner.classList.remove('hidden');

        text.textContent = 'Uploading...';

    });

});
</script>

@endsection