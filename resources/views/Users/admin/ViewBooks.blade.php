@extends('Users.admin.cover')

@section('content')

<div class="p-4 md:p-6">

    <!-- Header -->
    <div class="flex flex-col gap-4 mb-6 md:flex-row md:items-center md:justify-between">

        <div class="flex items-center gap-4">

            <div class="flex items-center justify-center w-14 h-14 text-white rounded-2xl bg-primary">
                <i class="fa-solid fa-book"></i>
            </div>

            <div>

                <h1 class="text-2xl font-bold text-primary-dark dark:text-white">
                    Uploaded Books
                </h1>

                <p class="text-sm text-gray-500">
                    Total Books :
                    <strong>{{ $books->total() }}</strong>
                </p>

            </div>

        </div>

        <a href="{{ route('owner.ibitabo') }}"
           class="flex items-center gap-2 px-5 py-3 font-semibold text-white rounded-xl bg-primary">

            <i class="fa-solid fa-plus"></i>

            Add Book

        </a>

    </div>

    @if(session('success'))

        <div class="p-4 mb-5 text-green-700 bg-green-100 rounded-xl">

            {{ session('success') }}

        </div>

    @endif

    @if($books->count())

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">

        @foreach($books as $book)

        <div class="overflow-hidden bg-white border shadow rounded-3xl">

            <!-- Cover -->

            <div class="flex items-center justify-center h-48 bg-red-50">

                <i class="text-7xl text-red-600 fa-solid fa-file-pdf"></i>

            </div>

            <!-- Body -->

            <div class="p-5">

                <h2 class="mb-3 text-lg font-bold">

                    {{ $book->title }}

                </h2>

                <p class="mb-5 text-sm text-gray-500">

                    Uploaded :
                    {{ $book->created_at->diffForHumans() }}

                </p>

                <div class="grid grid-cols-2 gap-2">

                    <!-- View -->

                    <a href="{{ asset('books/'.$book->book) }}"
                       target="_blank"
                       class="flex items-center justify-center gap-2 px-3 py-2 text-white bg-blue-600 rounded-xl">

                        <i class="fa-solid fa-eye"></i>

                        View

                    </a>

                    <!-- Download -->

                    <a href="{{ asset('books/'.$book->book) }}"
                       download
                       class="flex items-center justify-center gap-2 px-3 py-2 text-white bg-green-600 rounded-xl">

                        <i class="fa-solid fa-download"></i>

                        Download

                    </a>

                    <!-- Edit -->

                    <a href="{{ route('owner.editBook',$book->id) }}"
                       class="flex items-center justify-center gap-2 px-3 py-2 text-white bg-yellow-500 rounded-xl">

                        <i class="fa-solid fa-pen"></i>

                        Edit

                    </a>

                    <!-- Delete -->

                    <form action="{{ route('owner.deleteBook',$book->id) }}"
                          method="POST">

                        @csrf
                        @method('DELETE')

                        <button
                            onclick="return confirm('Delete this book?')"
                            class="flex items-center justify-center w-full gap-2 px-3 py-2 text-white bg-red-600 rounded-xl">

                            <i class="fa-solid fa-trash"></i>

                            Delete

                        </button>

                    </form>

                </div>

            </div>

        </div>

        @endforeach

    </div>

    <div class="mt-8">

        {{ $books->links() }}

    </div>

    @else

    <div class="py-20 text-center bg-white rounded-3xl shadow">

        <i class="mb-4 text-7xl text-gray-300 fa-solid fa-book-open"></i>

        <h2 class="mb-2 text-2xl font-bold">

            No Books Found

        </h2>

        <p class="mb-6 text-gray-500">

            Upload your first Islamic book.

        </p>

        <a href="{{ route('owner.ibitabo') }}"
           class="px-6 py-3 font-semibold text-white rounded-xl bg-primary">

            Upload Book

        </a>

    </div>

    @endif

</div>

@endsection