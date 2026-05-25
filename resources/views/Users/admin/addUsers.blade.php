@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <!-- PAGE HEADER -->
    <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">

        <!-- LEFT -->
        <div class="flex items-center gap-4">

            <div class="flex items-center justify-center w-14 h-14 text-white shadow-lg rounded-2xl bg-primary">
                <i class="text-2xl fa-solid fa-user-plus"></i>
            </div>

            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                    Add New User
                </h1>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Create new accounts for sheikhs and students.
                </p>
            </div>

        </div>

        <!-- RIGHT BUTTONS -->
        <div class="flex flex-wrap gap-3">

            <button
                type="button"
                class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white transition shadow-md rounded-xl bg-primary hover:scale-[1.02]"
            >
                <i class="fa-solid fa-users"></i>
                View Users
            </button>

            <button
                type="button"
                class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white transition bg-green-600 shadow-md rounded-xl hover:bg-green-700 hover:scale-[1.02]"
            >
                <i class="fa-solid fa-envelope"></i>
                New Emails
            </button>

        </div>

    </div>

    <!-- MAIN CARD -->
    <div class="overflow-hidden bg-white border border-gray-100 shadow-lg rounded-3xl dark:bg-darker dark:border-gray-700">

        <!-- CARD TOP -->
        <div class="flex items-center gap-3 px-6 py-5 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">

            <div class="flex items-center justify-center w-12 h-12 text-white rounded-2xl bg-primary">
                <i class="text-lg fa-solid fa-id-card"></i>
            </div>

            <div>
                <h2 class="text-lg font-bold text-gray-800 dark:text-white">
                    User Information
                </h2>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Fill in the information below correctly.
                </p>
            </div>

        </div>

        <!-- FORM -->
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                <!-- EMAIL -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Email Address
                    </label>

                    <div class="relative">

                        <span class="absolute text-gray-400 -translate-y-1/2 left-4 top-1/2" style="margin-top: -10px;">
                            <i class="fa-solid fa-envelope"></i>
                        </span>

                        <input
                            type="email"
                            name="email"
                            placeholder="example@gmail.com"
                            class="w-full py-3.5 pl-12 pr-4 text-sm border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                        >

                    </div>

                </div>

                <!-- ROLE -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        User Role
                    </label>

                    <div class="relative">

                        <span class="absolute text-gray-400 -translate-y-1/2 left-4 top-1/2" style="margin-top: -10px;">
                            <i class="fa-solid fa-user-shield"></i>
                        </span>

                        <select
                            name="role"
                            class="w-full py-3.5 pl-12 pr-4 text-sm border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                        >
                            <option value="">Select role</option>
                            <option value="Sheikh">Sheikh</option>
                            <option value="User">User</option>
                        </select>

                    </div>

                </div>

                <!-- TITLE -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        User Title
                    </label>

                    <div class="relative">

                        <span class="absolute text-gray-400 -translate-y-1/2 left-4 top-1/2" style="margin-top: -10px;">
                            <i class="fa-solid fa-user-tag"></i>
                        </span>

                        <select
                            name="title"
                            class="w-full py-3.5 pl-12 pr-4 text-sm border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                        >
                            <option value="">Select title</option>
                            <option value="Sheikh">Sheikh</option>
                            <option value="User">User</option>
                        </select>

                    </div>

                </div>

                <!-- IMAGE -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Profile Image
                    </label>

                    <div class="relative">

                        <span class="absolute text-gray-400 -translate-y-1/2 left-4 top-1/2" style="margin-top: -10px;">
                            <i class="fa-solid fa-camera"></i>
                        </span>

                        <input
                            type="file"
                            name="image"
                            class="w-full py-3 pl-12 pr-4 text-sm border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                        >

                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="flex flex-col-reverse gap-3 px-6 py-5 border-t bg-gray-50 md:flex-row md:justify-end dark:bg-dark dark:border-gray-700">

                <!-- RESET -->
                <button
                    type="reset"
                    class="flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-white transition bg-red-500 rounded-2xl hover:bg-red-600"
                >
                    <i class="fa-solid fa-rotate-left"></i>
                    Reset
                </button>

                <!-- SAVE -->
                <button
                    type="submit"
                    class="flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white transition shadow-md rounded-2xl bg-primary hover:scale-[1.02]"
                >
                    <i class="fa-solid fa-floppy-disk"></i>
                    Save User
                </button>

            </div>

        </form>

    </div>

</div>

@endsection