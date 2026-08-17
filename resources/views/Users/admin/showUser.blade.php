@extends('Users.admin.cover')

@section('content')

<div class="min-h-screen p-4 md:p-6">

    <!-- HEADER -->
    <div class="flex flex-col items-start justify-between gap-4 mb-6 md:flex-row md:items-center">

        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
                User Profile
            </h1>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Full information about selected user.
            </p>
        </div>

        <!-- BACK -->
        <a
            href="{{ route('owner.viewUser') }}"
            class="flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white transition rounded-xl bg-primary hover:bg-primary-dark"
        >

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M15 19l-7-7 7-7"/>

            </svg>

            Back To Users

        </a>

    </div>

    <!-- PROFILE CARD -->
    <div class="overflow-hidden bg-white shadow-xl rounded-3xl dark:bg-darker">

        <!-- TOP -->
        <div class="relative p-8 overflow-hidden bg-gradient-to-r from-primary to-purple-700">

            <div class="absolute inset-0 opacity-10">
                <div class="w-full h-full bg-white"></div>
            </div>

            <div class="relative flex flex-col items-center gap-6 md:flex-row">

                <!-- IMAGE -->
                <img
                    src="{{ $user->imageUrl() }}"
                    alt=""
                    class="object-cover border-4 border-white shadow-2xl w-36 h-36 rounded-3xl"
                >

                <!-- INFO -->
                <div class="text-center md:text-left">

                    <h2 class="text-4xl font-bold " style="color:teal;">
                        {{ $user->firstname }} {{ $user->lastname }}
                    </h2>

                    <p class="mt-2 text-lg text-gray-300">
                        {{ $user->email }}
                    </p>

                    <div class="flex flex-wrap justify-center gap-3 mt-5 md:justify-start">
<!-- 
                        <span class="px-5 py-2 text-sm font-semibold text-purple-700 bg-white rounded-full">
                            {{ $user->title }}
                        </span> -->

                        <span class="px-5 py-2 text-sm font-semibold text-green-700 bg-green-100 rounded-full">
                            {{ $user->role }}
                        </span>

                        <span class="px-5 py-2 text-sm font-semibold text-blue-700 bg-blue-100 rounded-full">
                            {{ ucfirst($user->gender) }}
                        </span>

                    </div>

                </div>

            </div>

        </div>

        <!-- DETAILS -->
        <div class="grid grid-cols-1 gap-6 p-8 md:grid-cols-2">

            <!-- FIRSTNAME -->
            <div class="p-5 border rounded-2xl dark:border-gray-700">

                <h4 class="mb-2 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
                    Firstname
                </h4>

                <p class="text-lg font-bold text-gray-800 dark:text-white">
                    {{ $user->firstname }}
                </p>

            </div>

            <!-- LASTNAME -->
            <div class="p-5 border rounded-2xl dark:border-gray-700">

                <h4 class="mb-2 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
                    Lastname
                </h4>

                <p class="text-lg font-bold text-gray-800 dark:text-white">
                    {{ $user->lastname }}
                </p>

            </div>

            <!-- PHONE -->
            <div class="p-5 border rounded-2xl dark:border-gray-700">

                <h4 class="mb-2 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
                    Phone Number
                </h4>

                <p class="text-lg font-bold text-gray-800 dark:text-white">
                    {{ $user->phone }}
                </p>

            </div>

            <!-- EMAIL -->
            <div class="p-5 border rounded-2xl dark:border-gray-700">

                <h4 class="mb-2 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
                    Email Address
                </h4>

                <p class="text-lg font-bold text-gray-800 dark:text-white">
                    {{ $user->email }}
                </p>

            </div>

            <!-- DOB -->
            <div class="p-5 border rounded-2xl dark:border-gray-700">

                <h4 class="mb-2 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
                    Date Of Birth
                </h4>

                <p class="text-lg font-bold text-gray-800 dark:text-white">
                    {{ $user->dob }}
                </p>

            </div>

            <!-- CREATED -->
            <div class="p-5 border rounded-2xl dark:border-gray-700">

                <h4 class="mb-2 text-sm font-semibold text-gray-500 uppercase dark:text-gray-400">
                    Account Created
                </h4>

                <p class="text-lg font-bold text-gray-800 dark:text-white">
                    {{ $user->created_at->format('d M Y') }}
                </p>

            </div>

        </div>

        <!-- ACTIONS -->
        <div class="flex flex-wrap items-center justify-end gap-4 p-8 border-t dark:border-gray-700">

            <!-- EDIT -->
            <a
                href="{{ url('owner/users/'.$user->id.'/edit') }}"
                class="px-6 py-3 text-sm font-semibold text-white transition bg-blue-600 rounded-xl hover:bg-blue-700"
            >
                Edit User
            </a>

            <!-- DELETE -->
            <form
                action="{{ url('owner/users/'.$user->id) }}"
                method="POST"
                onsubmit="return confirm('Delete this user?')"
            >

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="px-6 py-3 text-sm font-semibold text-white transition bg-red-600 rounded-xl hover:bg-red-700"
                >
                    Delete User
                </button>

            </form>

        </div>

    </div>

</div>

@endsection