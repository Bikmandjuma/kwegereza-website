<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Amatangazo;
use App\Models\Book;
use App\Models\DarsatTable;
use App\Models\Inyandiko;
use App\Models\Owner;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = trim((string) $request->input('q', ''));
        $type = $request->input('type', 'all'); // all | darsat | books | inyandiko | amatangazo | teachers

        $results = [
            'darsat'     => collect(),
            'books'      => collect(),
            'inyandiko'  => collect(),
            'amatangazo' => collect(),
            'teachers'   => collect(),
        ];

        if ($query !== '') {

            if (in_array($type, ['all', 'darsat'])) {
                $results['darsat'] = DarsatTable::published()
                    ->with('teacher')
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('type', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%");
                    })
                    ->latest()
                    ->limit(20)
                    ->get();
            }

            if (in_array($type, ['all', 'books'])) {
                $results['books'] = Book::published()
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('author', 'like', "%{$query}%")
                          ->orWhere('category', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%");
                    })
                    ->latest()
                    ->limit(20)
                    ->get();
            }

            if (in_array($type, ['all', 'inyandiko'])) {
                $results['inyandiko'] = Inyandiko::published()
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('author', 'like', "%{$query}%")
                          ->orWhere('category', 'like', "%{$query}%")
                          ->orWhere('summary', 'like', "%{$query}%");
                    })
                    ->latest()
                    ->limit(20)
                    ->get();
            }

            if (in_array($type, ['all', 'amatangazo'])) {
                $results['amatangazo'] = Amatangazo::published()
                    ->where(function ($q) use ($query) {
                        $q->where('title', 'like', "%{$query}%")
                          ->orWhere('presenter', 'like', "%{$query}%")
                          ->orWhere('description', 'like', "%{$query}%");
                    })
                    ->latest('published_at')
                    ->limit(20)
                    ->get();
            }

            if (in_array($type, ['all', 'teachers'])) {
                $results['teachers'] = Owner::whereIn('title', ['sheikh', 'ustadh'])
                    ->where(function ($q) use ($query) {
                        $q->where('firstname', 'like', "%{$query}%")
                          ->orWhere('lastname', 'like', "%{$query}%");
                    })
                    ->withCount(['darsat' => fn($q) => $q->published()])
                    ->limit(20)
                    ->get();
            }
        }

        $totalCount = $results['darsat']->count()
            + $results['books']->count()
            + $results['inyandiko']->count()
            + $results['amatangazo']->count()
            + $results['teachers']->count();

        return view('Guest.search', array_merge($results, [
            'query'      => $query,
            'type'       => $type,
            'totalCount' => $totalCount,
        ]));
    }

    /**
     * Lightweight JSON endpoint for instant search-as-you-type suggestions.
     */
    public function suggest(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = collect();

        DarsatTable::published()->where('title', 'like', "%{$query}%")->limit(5)->get()
            ->each(fn($d) => $suggestions->push(['type' => 'Darsat', 'title' => $d->title, 'url' => route('guest.teacher-darsa', $d->teachers)]));

        Book::published()->where('title', 'like', "%{$query}%")->limit(5)->get()
            ->each(fn($b) => $suggestions->push(['type' => 'Igitabo', 'title' => $b->title, 'url' => route('guest.books')]));

        Inyandiko::published()->where('title', 'like', "%{$query}%")->limit(5)->get()
            ->each(fn($i) => $suggestions->push(['type' => 'Inyandiko', 'title' => $i->title, 'url' => route('guest.inyandiko.show', $i->slug)]));

        Amatangazo::published()->where('title', 'like', "%{$query}%")->limit(5)->get()
            ->each(fn($a) => $suggestions->push(['type' => 'Itangazo', 'title' => $a->title, 'url' => route('guest.news')]));

        return response()->json($suggestions->take(10)->values());
    }
}
