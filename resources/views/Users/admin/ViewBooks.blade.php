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

    <form method="GET" action="{{ route('owner.viewBooks') }}" class="mb-5">
        <div class="relative max-w-md">
            <span class="absolute text-gray-400 left-4 top-1/2 -translate-y-1/2">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Shakisha igitabo, umwanditsi cyangwa icyiciro..."
                class="w-full py-3 pl-12 pr-4 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
        </div>
    </form>

    @if($books->count())

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">

        @foreach($books as $book)

        <div class="overflow-hidden bg-white border shadow rounded-3xl">

            <!-- Cover -->

            <div class="relative flex items-center justify-center h-48 bg-red-50">

                @if($book->coverUrl())
                    <img src="{{ $book->coverUrl() }}" class="object-cover w-full h-full" alt="{{ $book->title }}">
                @else
                    <i class="text-7xl text-red-600 fa-solid fa-file-pdf"></i>
                @endif

                <span class="absolute px-2 py-0.5 text-[11px] font-bold text-white rounded-full top-3 left-3 {{ $book->status === 'published' ? 'bg-primary' : 'bg-gray-500' }}">
                    {{ strtoupper($book->status ?? 'published') }}
                </span>

            </div>

            <!-- Body -->

            <div class="p-5">

                <h2 class="mb-1 text-lg font-bold">

                    {{ $book->title }}

                </h2>

                @if($book->author)
                    <p class="mb-1 text-sm text-gray-500">{{ $book->author }}</p>
                @endif

                @if($book->category)
                    <span class="inline-block px-2 py-0.5 mb-3 text-[11px] font-semibold rounded-full bg-gray-100 dark:bg-dark">{{ $book->category }}</span>
                @endif

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

                    <button type="button"
                       onclick="openEditBookModal({{ $book->id }})"
                       class="flex items-center justify-center gap-2 px-3 py-2 text-white bg-yellow-500 rounded-xl">

                        <i class="fa-solid fa-pen"></i>

                        Edit

                    </button>

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

            <div id="book-data-{{ $book->id }}" class="hidden"
                 data-title="{{ $book->title }}"
                 data-author="{{ $book->author }}"
                 data-category="{{ $book->category }}"
                 data-description="{{ $book->description }}"
                 data-status="{{ $book->status }}"
                 data-action="{{ route('owner.updateBook', $book->id) }}"></div>

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

<!-- EDIT BOOK MODAL -->
<div id="editBookModal" class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black/50">
    <div class="w-full max-w-lg overflow-hidden bg-white shadow-2xl rounded-3xl dark:bg-darker">
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 dark:bg-dark dark:border-gray-700">
            <h3 class="text-lg font-bold text-primary-dark dark:text-light">Hindura Igitabo</h3>
            <button type="button" onclick="document.getElementById('editBookModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="text-xl fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="editBookForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-4 p-6 overflow-y-auto max-h-[65vh]">
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Title</label>
                    <input type="text" name="title" id="edit_book_title" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Author</label>
                    <input type="text" name="author" id="edit_book_author"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Category</label>
                    <input type="text" name="category" id="edit_book_category"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" id="edit_book_description" rows="3"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Replace PDF File (optional)</label>
                    <input type="file" name="book" accept=".pdf"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Replace Cover Image (optional)</label>
                    <input type="file" name="cover_image" accept="image/*"
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="edit_book_status" required
                        class="w-full px-4 py-3 border rounded-2xl focus:ring-2 focus:ring-primary dark:bg-dark dark:border-gray-700 dark:text-white">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50 dark:bg-dark dark:border-gray-700">
                <button type="button" onclick="document.getElementById('editBookModal').classList.add('hidden')"
                    class="px-5 py-2.5 font-semibold text-gray-600 bg-gray-200 rounded-xl">Cancel</button>
                <button type="submit" class="px-5 py-2.5 font-semibold text-white rounded-xl bg-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditBookModal(id) {
    const box = document.getElementById('book-data-' + id);
    document.getElementById('edit_book_title').value = box.dataset.title;
    document.getElementById('edit_book_author').value = box.dataset.author;
    document.getElementById('edit_book_category').value = box.dataset.category;
    document.getElementById('edit_book_description').value = box.dataset.description;
    document.getElementById('edit_book_status').value = box.dataset.status;
    document.getElementById('editBookForm').action = box.dataset.action;
    document.getElementById('editBookModal').classList.remove('hidden');
}
</script>

@endsection