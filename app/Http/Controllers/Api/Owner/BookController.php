<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Owner\StoreBookRequest;
use App\Http\Requests\Api\Owner\UpdateBookRequest;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookService;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct(private BookService $books)
    {
    }

    public function index(Request $request)
    {
        $paginated = $this->books->paginate(
            perPage: (int) $request->integer('per_page', 10),
            search: $request->string('search')->value() ?: null,
            status: $request->string('status')->value() ?: null,
        );

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => BookResource::collection($paginated->items()),
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
            ],
        ]);
    }

    public function show(int $id)
    {
        $book = $this->books->find($id);

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => (new BookResource($book))->toArray(request()) + [
                'download_stats' => $this->books->downloadStats($book->id),
            ],
        ]);
    }

    public function store(StoreBookRequest $request)
    {
        $book = $this->books->create(
            $request->validated(),
            $request->file('book'),
            $request->file('cover_image'),
            $request->user()->id,
        );

        return response()->json([
            'success' => true,
            'message' => 'Igitabo cyashyizweho.',
            'data' => new BookResource($book),
        ], 201);
    }

    public function update(UpdateBookRequest $request, int $id)
    {
        $book = Book::findOrFail($id);

        $this->books->update(
            $book,
            $request->validated(),
            $request->file('book'),
            $request->file('cover_image'),
            $request->user()->id,
        );

        return response()->json([
            'success' => true,
            'message' => 'Igitabo cyahinduwe.',
            'data' => new BookResource($book->fresh()),
        ]);
    }

    public function destroy(int $id)
    {
        $this->books->delete(Book::findOrFail($id));

        return response()->json(['success' => true, 'message' => 'Igitabo cyasibwe.', 'data' => null]);
    }

    /**
     * Public download-tracking endpoint — not owner-gated, since books are
     * publicly browsable to guests too (GuestController::books()). Logs a
     * real, auditable download event rather than only bumping a counter —
     * the gap the Phase 0 audit flagged.
     *
     * Honest limitation: this sits under routes/api.php's 'api' middleware
     * group, which doesn't start sessions, so the session-based 'student'
     * guard can't be resolved here and every download logs as anonymous
     * (ip_address only, user_id null) even if a student happens to be
     * logged in via the Blade side. Attributing downloads to a specific
     * student would need students to also authenticate via Sanctum tokens,
     * which don't exist yet — only owners do (Phase 2). Flagging rather
     * than pretending this already works.
     */
    public function download(Request $request, int $id)
    {
        $book = Book::published()->findOrFail($id);

        $updated = app(BookService::class)->recordDownload($book, null, $request->ip());

        if (! $updated) {
            return response()->json(['success' => false, 'message' => 'Iki gitabo ntigishobora kuboneka.'], 403);
        }

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => ['download_url' => $book->bookFileUrl(), 'downloads' => $updated->fresh()->downloads],
        ]);
    }

    /** Fired once per book-open in the reader (see ibitabo.blade.php's loadBook()) — a lightweight popularity counter, not an audit-grade log like downloads. */
    public function recordView(int $id)
    {
        $book = Book::published()->findOrFail($id);
        $views = app(BookService::class)->recordView($book);

        return response()->json(['success' => true, 'message' => null, 'data' => ['views' => $views]]);
    }
}
