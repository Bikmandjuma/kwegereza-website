<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Inyandiko;
use App\Models\Owner;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        $xml = Cache::remember('sitemap.xml', 3600, function () {
            $urls = collect([
                ['loc' => route('guest.home'), 'priority' => '1.0'],
                ['loc' => route('guest.books'), 'priority' => '0.8'],
                ['loc' => route('guest.teachers'), 'priority' => '0.8'],
                ['loc' => route('guest.inyandiko_zabamenyi'), 'priority' => '0.8'],
                ['loc' => route('guest.news'), 'priority' => '0.7'],
                ['loc' => route('guest.courses'), 'priority' => '0.8'],
                ['loc' => route('guest.twandikire'), 'priority' => '0.5'],
            ]);

            $urls = $urls->merge(
                Owner::whereIn('title', ['sheikh', 'ustadh'])->get()->map(fn($teacher) => [
                    'loc'      => route('guest.teacher-darsa', $teacher->id),
                    'priority' => '0.6',
                ])
            );

            $urls = $urls->merge(
                Inyandiko::published()->get()->map(fn($item) => [
                    'loc'      => route('guest.inyandiko.show', $item->slug),
                    'lastmod'  => $item->updated_at->toAtomString(),
                    'priority' => '0.6',
                ])
            );

            $urls = $urls->merge(
                Course::published()->get()->map(fn($course) => [
                    'loc'      => route('guest.course.show', $course->slug),
                    'lastmod'  => $course->updated_at->toAtomString(),
                    'priority' => '0.7',
                ])
            );

            return view('sitemap', ['urls' => $urls])->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
