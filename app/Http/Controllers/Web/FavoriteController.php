<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Amatangazo;
use App\Models\Book;
use App\Models\DarsatTable;
use App\Models\Favorite;
use App\Models\Inyandiko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    private const TYPE_MAP = [
        'darsat'     => DarsatTable::class,
        'book'       => Book::class,
        'inyandiko'  => Inyandiko::class,
        'amatangazo' => Amatangazo::class,
    ];

    public function toggle(Request $request, string $type, int $id)
    {
        if (!array_key_exists($type, self::TYPE_MAP)) {
            abort(404);
        }

        $modelClass = self::TYPE_MAP[$type];
        $item = $modelClass::findOrFail($id);
        $user = Auth::guard('student')->user();

        $existing = $item->favorites()->where('user_id', $user->id)->first();

        if ($existing) {
            $existing->delete();
            $favorited = false;
        } else {
            Favorite::create([
                'user_id'          => $user->id,
                'favoritable_id'   => $item->id,
                'favoritable_type' => $modelClass,
            ]);
            $favorited = true;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['favorited' => $favorited]);
        }

        return back()->with('success', $favorited ? 'Byongewe ku bikunzwe.' : 'Byakuwe ku bikunzwe.');
    }

    public function myFavorites()
    {
        $user = Auth::guard('student')->user();

        $favorites = $user->favorites()->with('favoritable')->latest()->get();

        $grouped = [
            'darsat'     => $favorites->filter(fn($f) => $f->favoritable_type === DarsatTable::class)->pluck('favoritable')->filter(),
            'books'      => $favorites->filter(fn($f) => $f->favoritable_type === Book::class)->pluck('favoritable')->filter(),
            'inyandiko'  => $favorites->filter(fn($f) => $f->favoritable_type === Inyandiko::class)->pluck('favoritable')->filter(),
            'amatangazo' => $favorites->filter(fn($f) => $f->favoritable_type === Amatangazo::class)->pluck('favoritable')->filter(),
        ];

        return view('Users.User.favorites', compact('grouped'));
    }
}
