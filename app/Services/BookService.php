<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookDownload;
use App\Models\User;
use App\Notifications\NewBookNotification;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;

class BookService
{
    use HandlesFileUploads;

    public function paginate(int $perPage = 10, ?string $search = null, ?string $status = null)
    {
        return Book::when($search, function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            })
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): Book
    {
        return Book::findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $bookFile, ?UploadedFile $cover, int $ownerId): Book
    {
        $book = Book::create([
            'title'           => $data['title'],
            'author'          => $data['author'] ?? null,
            'category'        => $data['category'] ?? null,
            'description'     => $data['description'] ?? null,
            'book'            => $this->storeUploadedFile($bookFile, 'books'),
            'cover_image'     => $this->storeUploadedFile($cover, 'books/covers'),
            'status'          => $data['status'],
            'is_downloadable' => $data['is_downloadable'] ?? true,
            'downloads'       => 0,
            'created_by'      => $ownerId,
            'published_at'    => $data['status'] === 'published' ? now() : null,
        ]);

        // Was entirely missing — "new book" is one of the spec's explicit
        // notification types (§32), but BookService never notified
        // anyone at all, in any form (no database row, no push), unlike
        // its Darsat/Announcement siblings.
        if ($book->status === 'published') {
            User::whereNotNull('id')->chunk(200, function ($students) use ($book) {
                Notification::send($students, new NewBookNotification($book));
            });

            app(PushNotificationService::class)->sendToAll(
                'Igitabo Gishya',
                $book->title,
                route('guest.books')
            );
        }

        return $book;
    }

    public function update(Book $book, array $data, ?UploadedFile $bookFile, ?UploadedFile $cover, int $ownerId): Book
    {
        $bookName = $book->book;
        if ($bookFile) {
            $this->deleteUploadedFile($book->book, 'books');
            $bookName = $this->storeUploadedFile($bookFile, 'books');
        }

        $coverName = $book->cover_image;
        if ($cover) {
            $this->deleteUploadedFile($book->cover_image, 'books/covers');
            $coverName = $this->storeUploadedFile($cover, 'books/covers');
        }

        $book->update([
            'title'           => $data['title'],
            'author'          => $data['author'] ?? null,
            'category'        => $data['category'] ?? null,
            'description'     => $data['description'] ?? null,
            'book'            => $bookName,
            'cover_image'     => $coverName,
            'status'          => $data['status'],
            'is_downloadable' => $data['is_downloadable'] ?? true,
            'updated_by'      => $ownerId,
            'published_at'    => $data['status'] === 'published' ? ($book->published_at ?? now()) : null,
        ]);

        return $book;
    }

    public function delete(Book $book): void
    {
        $this->deleteUploadedFile($book->book, 'books');
        $this->deleteUploadedFile($book->cover_image, 'books/covers');
        $book->delete();
    }

    /**
     * Records a real, auditable download event (not just a counter bump) —
     * the gap flagged in the Phase 0 audit. Returns null if the book isn't
     * downloadable so the caller can refuse the request.
     */
    public function recordDownload(Book $book, ?int $userId, ?string $ip): ?Book
    {
        if (! $book->is_downloadable) {
            return null;
        }

        BookDownload::create(['book_id' => $book->id, 'user_id' => $userId, 'ip_address' => $ip]);
        $book->increment('downloads');

        return $book;
    }

    /**
     * The `views` column has existed since the earliest Books migration
     * but nothing ever incremented it (flagged in this same file's own
     * comment when download tracking was added, and never circled back
     * to). Deliberately much simpler than download tracking — no
     * per-view row, no ip/user linkage — since a "view" here just means
     * "opened in the reader", used for a lightweight popularity signal,
     * not an audit trail the way downloads are.
     */
    public function recordView(Book $book): int
    {
        $book->increment('views');

        return $book->fresh()->views;
    }

    public function downloadStats(int $bookId): array
    {
        return [
            'total' => BookDownload::where('book_id', $bookId)->count(),
            'unique_users' => BookDownload::where('book_id', $bookId)->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'last_7_days' => BookDownload::where('book_id', $bookId)->where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }
}
