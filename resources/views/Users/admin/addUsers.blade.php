@extends('Users.admin.cover')

@section('content')

<div class="min-h-screen p-4 md:p-6">

    <!-- HEADER -->
    <div class="flex flex-col items-start justify-between gap-4 mb-6 md:flex-row md:items-center">

        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-300">
                Add New User
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Create a new account for sheikh or student.
            </p>
        </div>

        <!-- USE EMAIL BUTTON -->
        <button
            type="button"
            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white transition rounded-lg bg-primary hover:bg-primary-dark"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M16 12H8m4-4v8m8 4H4a2 2 0 01-2-2V6a2 2 0 012-2h8l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2z"/>
            </svg>

            Use Email Instead
        </button>
    </div>

    <!-- FORM CARD -->
    <div class="bg-white shadow-md rounded-2xl dark:bg-darker">

        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                <!-- FIRSTNAME -->
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Firstname
                    </label>

                    <input
                        type="text"
                        name="firstname"
                        placeholder="Enter firstname"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
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
                        placeholder="Enter lastname"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
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
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
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
                        placeholder="+2507..."
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                    >
                </div>

                <!-- DOB -->
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Date of Birth
                    </label>

                    <input
                        type="date"
                        name="dob"
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
                        placeholder="example@gmail.com"
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
                        <option value="">Select role</option>
                        <option value="superAdmin">Super Admin</option>
                        <option value="AssistantAdmin">Assistant Admin</option>
                        <option value="superSheikh">Super Sheikh</option>
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
                        <option value="">Select title</option>
                        <option value="Admin">Admin</option>
                        <option value="Sheikh">Sheikh</option>
                    </select>
                </div>

                <!-- IMAGE -->
                <div>
                    <label class="block mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        Profile Image
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
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        placeholder="********"
                        class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                    >
                </div>

            </div>

            <!-- FOOTER -->
            <div class="flex items-center justify-end gap-4 p-6 border-t dark:border-gray-700">

                <button
                    type="reset"
                    class="px-5 py-3 text-sm font-semibold text-gray-700 transition bg-gray-100 rounded-xl hover:bg-gray-200 dark:bg-danger dark:text-white"
                >
                    Reset
                </button>

                <button
                    type="submit"
                    class="px-6 py-3 text-sm font-semibold text-white transition rounded-xl bg-primary hover:bg-primary-dark"
                >
                    Save User
                </button>

            </div>

        </form>

    </div>

</div>

@endsection