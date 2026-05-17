@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <!-- HEADER -->
    <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Users Management
            </h1>

            <p class="text-sm text-gray-500 dark:text-gray-400">
                View and manage all registered users.
            </p>
        </div>

        <!-- ADD USER BUTTON -->
        <a
            href="{{ route('owner.addUser') }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-white transition rounded-xl bg-primary hover:bg-primary-dark"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>

            Add User
        </a>

    </div>

    <!-- CARD -->
    <div class="overflow-hidden bg-white shadow-lg rounded-2xl dark:bg-darker">

        <!-- TOP SECTION -->
        <div class="p-4 border-b dark:border-gray-700">

            <!-- TABS -->
            <div class="flex flex-wrap items-center gap-3 mb-4">

                <!-- ALL -->
<!-- 
                <a href="{{ route('owner.viewUser') }}"
                   class="px-4 py-2 text-sm font-semibold text-white transition rounded-xl bg-primary hover:bg-primary-dark">
                    All
                </a> -->
                <a href="{{ route('owner.viewUser') }}"
                   class="px-4 py-2 text-sm font-semibold text-white transition rounded-xl bg-primary hover:bg-primary-dark">
                    All ({{ $users->count() }})
                </a>

                <!-- DYNAMIC TITLES -->
                @foreach($titles as $title)

                    <a
                        href="{{ route('owner.viewUser', ['title' => $title->title]) }}"
                        class="px-4 py-2 text-sm font-medium text-gray-700 transition bg-gray-100 rounded-xl hover:bg-primary hover:text-white dark:bg-dark dark:text-gray-300"
                    >
                        <!-- {{ $title->title }} -->
                        {{ $title->title }} ({{ $title->total }})
                    </a>

                @endforeach

            </div>

            <!-- SEARCH -->
            <div class="relative">

                <span class="absolute text-gray-400 transform -translate-y-1/2 left-4 top-1/2">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-5 h-5"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              stroke-width="2"
                              d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>

                <input
                    type="text"
                    id="searchInput"
                    placeholder="Search users..."
                    class="w-full py-3 pl-12 pr-4 border rounded-xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"
                    style="z-index: 0;"
                >

            </div>

        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto">

            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">

                <!-- HEAD -->
                <thead class="text-xs uppercase bg-gray-50 dark:bg-dark dark:text-gray-300">

                    <tr>

                        <th class="px-6 py-4">
                            User
                        </th>

                        <th class="px-6 py-4">
                            Phone,
                            Email,
                            Gender
                        </th>

                        <!-- <th class="px-6 py-4">
                            Role
                        </th> -->

                        <th class="px-6 py-4">
                            Title
                        </th>

                       <!--  <th class="px-6 py-4">
                            Joined
                        </th> -->

                        <th class="px-6 py-4 text-center">
                            Actions
                        </th>

                    </tr>

                </thead>

                <!-- BODY -->
                <tbody id="usersTable">

                    @foreach($users as $user)

                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-dark transition">

                        <!-- USER -->
                        <td class="px-6 py-4">

                            <div class="flex items-center gap-3">

                                <img
                                    src="{{ asset('users/images/'.$user->image) }}"
                                    alt=""
                                    class="object-cover w-12 h-12 rounded-full"
                                >

                                <div>

                                    <h4 class="font-semibold pr-2">
                                        {{ $user->firstname }}&nbsp;{{ $user->lastname }}
                                    </h4>

                                    <p class="text-xs text-gray-500">
                                        {{ $user->dob }}
                                    </p>

                                </div>

                            </div>

                        </td>

                        <!-- PHONE -->
                        <td class="px-6 py-4">
                            {{ $user->email }}<br>
                            {{ $user->phone }},

                            @if($user->gender == 'male')
                                Male
                            @else
                                Female
                            @endif

                        </td>

                        <!-- ROLE -->
                        <!-- <td class="px-6 py-4">

                            <span class="px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">
                                {{ $user->role }}
                            </span>

                        </td> -->

                        <!-- TITLE -->
                        <td class="px-6 py-4">

                            <span class="px-3 py-1 text-xs font-semibold text-purple-700 bg-purple-100 rounded-full">

                                @if($user->role == "Assistant-Admin")

                                    Assistant&nbsp;Admin

                                @else

                                    Super&nbsp;{{ $user->title }}

                                @endif

                            </span>

                        </td>

                        <!-- CREATED -->
                       <!--  <td class="px-6 py-4 flex">
                            {{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() }}
                        </td> -->

                        <!-- ACTIONS -->
                        <td class="px-6 py-4">

                            <div class="flex items-center justify-center gap-2">

                                <!-- VIEW -->
                                <a
                                    href="{{ route('owner.showUser', $user->id) }}"
                                    class="p-2 text-blue-600 transition bg-blue-100 rounded-lg hover:bg-blue-200"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="w-5 h-5"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                <!-- EDIT -->
                                <a
                                    href="{{ route('owner.EditUser', $user->id) }}"
                                    class="p-2 text-yellow-600 transition bg-yellow-100 rounded-lg hover:bg-yellow-200"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="w-5 h-5"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M11 5h2m-1-1v2m6.364 1.636l-9.9 9.9a2 2 0 01-.878.515l-3 1a1 1 0 01-1.264-1.264l1-3a2 2 0 01.515-.878l9.9-9.9a2 2 0 112.828 2.828z"/>
                                    </svg>
                                </a>

                                <!-- DELETE -->
                                <form action="{{ route('users.destroy', $user->id) }}"
                                      method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="p-2 text-red-600 transition bg-red-100 rounded-lg hover:bg-red-200"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="w-5 h-5"
                                             fill="none"
                                             viewBox="0 0 24 24"
                                             stroke="currentColor">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                        </svg>
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- SEARCH SCRIPT -->
<script>

document.getElementById('searchInput').addEventListener('keyup', function () {

    let value = this.value.toLowerCase();

    let rows = document.querySelectorAll('#usersTable tr');

    rows.forEach((row) => {

        row.style.display =
            row.innerText.toLowerCase().includes(value)
                ? ''
                : 'none';

    });

});

</script>

@endsection