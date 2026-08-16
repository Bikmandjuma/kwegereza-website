@extends('Users.admin.cover')

@section('content')

<div class="min-h-screen p-4 md:p-6">

    <!-- HEADER -->
    <div class="flex flex-col items-start justify-between gap-4 mb-6 md:flex-row md:items-center">

        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">
                Edit User
            </h1>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                Update user information and permissions.
            </p>
        </div>

        <!-- BACK BUTTON -->
        <a
            href="{{ route('users.index') }}"
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

    <!-- FORM CARD -->
    <div class="overflow-hidden bg-white shadow-lg rounded-2xl dark:bg-darker">

        <form
            action="{{ route('users.update', $user->id) }}"
            method="POST"
            enctype="multipart/form-data"
        >

            @csrf
            @method('PUT')

            <!-- TOP USER PROFILE -->
            <div class="p-6 border-b dark:border-gray-700">

                <div class="flex flex-col items-center gap-5 md:flex-row">

                    <!-- IMAGE -->
                    <div class="relative">

                        <img
                            src="{{ $user->imageUrl() }}"
                            alt=""
                            class="object-cover border-4 border-white rounded-full shadow-lg w-28 h-28 dark:border-dark"
                        >

                    </div>

                    <!-- INFO -->
                    <div>

                        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                            {{ $user->firstname }} {{ $user->lastname }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ $user->email }}
                        </p>

                        <div class="flex flex-wrap gap-2 mt-3">

                            <span class="px-4 py-1 text-xs font-semibold text-purple-700 bg-purple-100 rounded-full">
                                {{ $user->title }}
                            </span>

                            <span class="px-4 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">
                                {{ $user->role }}
                            </span>

                        </div>

                    </div>

                </div>

            </div>

            <!-- FORM -->
            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                <!-- FIRSTNAME -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Firstname
                    </label>

                    <input
                        type="text"
                        name="firstname"
                        value="{{ $user->firstname }}"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary text-gray-700 dark:text-gray-300"
                    >

                </div>

                <!-- LASTNAME -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Lastname
                    </label>

                    <input
                        type="text"
                        name="lastname"
                        value="{{ $user->lastname }}"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-gray-300"
                    >

                </div>

                <!-- GENDER -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Gender
                    </label>

                    <select
                        name="gender"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                    >

                        <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>
                            Male
                        </option>

                        <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>
                            Female
                        </option>

                    </select>

                </div>

                <!-- PHONE -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Phone
                    </label>

                    <input
                        type="text"
                        name="phone"
                        value="{{ $user->phone }}"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                    >

                </div>

                <!-- DOB -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Date Of Birth
                    </label>

                    <input
                        type="date"
                        name="dob"
                        value="{{ $user->dob }}"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                    >

                </div>

                <!-- EMAIL -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ $user->email }}"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                    >

                </div>

                <!-- ROLE -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Role
                    </label>

                    <select
                        name="role"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                    >

                        <option value="superAdmin" {{ $user->role == 'superAdmin' ? 'selected' : '' }}>
                            Super Admin
                        </option>

                        <option value="AssistantAdmin" {{ $user->role == 'AssistantAdmin' ? 'selected' : '' }}>
                            Assistant Admin
                        </option>

                        <option value="superSheikh" {{ $user->role == 'superSheikh' ? 'selected' : '' }}>
                            Super Sheikh
                        </option>

                    </select>

                </div>

                <!-- TITLE -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Title
                    </label>

                    <select
                        name="title"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                    >

                        <option value="Admin" {{ $user->title == 'Admin' ? 'selected' : '' }}>
                            Admin
                        </option>

                        <option value="Sheikh" {{ $user->title == 'Sheikh' ? 'selected' : '' }}>
                            Sheikh
                        </option>

                    </select>

                </div>

                <!-- IMAGE -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Change Image
                    </label>

                    <input
                        type="file"
                        name="image"
                        class="w-full px-4 py-3 border rounded-xl dark:bg-dark dark:border-gray-700 dark:text-white"
                    >

                </div>

                <!-- PASSWORD -->
                <div>

                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        New Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        placeholder="Leave blank to keep current password"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                    >

                </div>

            </div>

            <!-- FOOTER -->
            <div class="flex flex-wrap items-center justify-end gap-4 p-6 border-t dark:border-gray-700">

                <!-- DELETE -->
                <button
                    type="button"
                    onclick="confirmDelete()"
                    class="px-5 py-3 text-sm font-semibold text-white transition bg-red-600 rounded-xl hover:bg-red-700"
                >
                    Delete User
                </button>

                <!-- SAVE -->
                <button
                    type="submit"
                    class="px-6 py-3 text-sm font-semibold text-white transition rounded-xl bg-primary hover:bg-primary-dark"
                >
                    Update User
                </button>

            </div>

        </form>

        <!-- DELETE FORM -->
        <form
            id="delete-form"
            action="{{ route('users.destroy', $user->id) }}"
            method="POST"
            class="hidden"
        >

            @csrf
            @method('DELETE')

        </form>

    </div>

</div>

<!-- DELETE SCRIPT -->
<script>

function confirmDelete()
{
    if(confirm('Are you sure you want to delete this user?'))
    {
        document.getElementById('delete-form').submit();
    }
}

</script>

@endsection