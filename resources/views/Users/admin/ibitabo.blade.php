@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">

        <div class="flex items-center gap-4">

            <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
                <i class="fa-solid fa-book-quran"></i>
            </div>

            <div>
                <h1 class="text-2xl font-bold text-primary-dark dark:text-light">
                    Add New Book
                </h1>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Upload Islamic books.
                </p>
            </div>

        </div>

        <button
            onclick="window.location='{{ route('owner.viewBooks') }}'"
            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white shadow-md rounded-xl bg-primary"
        >
            <i class="fa-solid fa-book-open"></i>
            View Books
        </button>

    </div>

    <!-- CARD -->
    <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">

        <!-- HEADER -->
        <div class="flex items-center gap-3 px-6 py-5 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">

            <div class="flex items-center justify-center w-12 h-12 text-white rounded-2xl bg-primary">
                <i class="fa-solid fa-book"></i>
            </div>

            <div>
                <h2 class="text-lg font-bold text-primary-dark dark:text-light">
                    Book Information
                </h2>

                <p class="text-sm text-gray-500">
                    Fill all book details below.
                </p>
            </div>

        </div>

        <!-- FORM -->
        <form id="uploadForm"
              action="{{ route('owner.storeBook') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <div class="grid grid-cols-1 gap-6 p-6">

                <!-- BOOK TITLE -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Book Title
                    </label>

                    <div class="relative">

                        <span class="absolute text-gray-400 left-4 top-1/2 -translate-y-1/2">
                            <i class="fa-solid fa-heading"></i>
                        </span>

                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            placeholder="Example: Kitabu Tawhiid"
                            class="w-full py-3 pl-12 pr-4 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                        >

                    </div>

                    @error('title')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror

                </div>

                <!-- BOOK FILE -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Book File
                    </label>

                    <div class="relative">

                        <span class="absolute text-gray-400 left-4 top-1/2 -translate-y-1/2">
                            <i class="fa-solid fa-file-pdf"></i>
                        </span>

                        <input
                            type="file"
                            name="book"
                            accept=".pdf"
                            class="w-full py-3 pl-12 pr-4 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                        >

                    </div>

                    @error('book')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                    @enderror

                </div>

                <!-- AUTHOR -->
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Author</label>
                    <input type="text" name="author" value="{{ old('author') }}"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <!-- CATEGORY -->
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Category</label>
                    <input type="text" name="category" value="{{ old('category') }}" placeholder="e.g. Fiqh, Aqida, Hadith"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <!-- DESCRIPTION -->
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">{{ old('description') }}</textarea>
                </div>

                <!-- COVER IMAGE -->
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Cover Image (optional)</label>
                    <input type="file" name="cover_image" accept="image/*"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>

                <!-- STATUS -->
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" required class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="published">Publish now</option>
                        <option value="draft">Save as draft</option>
                    </select>
                </div>

                <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <input type="checkbox" name="is_downloadable" value="1" checked class="rounded text-primary">
                    Allow guests to download this book
                </label>

            </div>

            <!-- FOOTER -->
            <div class="flex justify-end gap-3 px-6 py-5 border-t bg-gray-50 dark:bg-dark">

                <button
                    type="reset"
                    class="px-5 py-3 font-semibold text-white bg-red-500 rounded-2xl"
                >
                    Reset
                </button>

                <button
                    id="uploadBtn"
                    type="submit"
                    class="flex items-center gap-2 px-6 py-3 font-semibold text-white rounded-2xl bg-primary"
                >

                    <span id="uploadIcon">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </span>

                    <svg
                        id="uploadSpinner"
                        class="hidden w-5 h-5 animate-spin"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24">

                        <circle
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                            class="opacity-25">
                        </circle>

                        <path
                            fill="currentColor"
                            class="opacity-75"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z">
                        </path>

                    </svg>

                    <span id="uploadText">Upload Book</span>

                </button>

            </div>

        </form>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function(){

    const form = document.getElementById('uploadForm');
    const btn = document.getElementById('uploadBtn');
    const icon = document.getElementById('uploadIcon');
    const spinner = document.getElementById('uploadSpinner');
    const text = document.getElementById('uploadText');

    form.addEventListener('submit', function(){

        btn.disabled = true;
        icon.classList.add('hidden');
        spinner.classList.remove('hidden');
        text.innerHTML = 'Uploading...';

    });

});
</script>

@endsection