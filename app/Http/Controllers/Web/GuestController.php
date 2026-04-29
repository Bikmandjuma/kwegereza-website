<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;

class GuestController extends Controller
{
    // public function home(){

    //     $today = now()->toDateString();

    //     // increment visit
    //     $visit = Visit::where('date', $today)->first();

    //     if (!$visit) {
    //         Visit::create([
    //             'date' => $today,
    //             'count' => 1
    //         ]);
    //     } else {
    //         $visit->increment('count');
    //     }

    //     // today's visits
    //     $todayVisit = Visit::where('date', $today)->value('count') ?? 0;

    //     // total visits
    //     $totalVisit = Visit::sum('count');

    //     return view('Guest.ahabanza', [
    //         'todayVisit' => number_format($todayVisit),
    //         'totalVisit' => number_format($totalVisit),
    //     ]);
    // }

    public function home(){
        $today = now()->toDateString();

        // get or create guest id
        $guestId = request()->cookie('guest_visit_id');

        if (!$guestId) {
            $guestId = Str::uuid()->toString();

            // save cookie for 30 days
            Cookie::queue('guest_visit_id', $guestId, 60 * 24 * 30);
        }

        // unique key per guest per day
        $cacheKey = 'visit_' . $guestId . '_' . $today;

        // only count if 10 minutes passed
        if (!cache()->has($cacheKey)) {

            $visit = Visit::firstOrCreate(
                ['date' => $today],
                ['count' => 0]
            );

            $visit->increment('count');

            // block this guest for 10 minutes
            cache()->put($cacheKey, true, 600); // 600 sec = 10 min
        }

        // today's visits
        $todayVisit = Visit::where('date', $today)->value('count') ?? 0;

        // total visits
        $totalVisit = Visit::sum('count');

        return view('Guest.ahabanza', [
            'todayVisit' => number_format($todayVisit),
            'totalVisit' => number_format($totalVisit),
        ]);
    }

    public function books(){
        return view('Guest.ibitabo');
    }

    public function news(){
        return view('Guest.amatangazo');
    }

    public function inyandiko_zabamenyi(){
        return view('Guest.inyandiko-zabamenyi');
    }

    public function teachers(){
        return view('Guest.abasheikh');
    }

    public function search(){
        return view('Guest.search');
    }

    public function teacher_darsa(){
        return view('Guest.inyigisho_zabarimu');
    }

    // public function liveVisits()
    // {
    //     $today = now()->toDateString();

    //     $todayVisit = Visit::where('date', $today)->value('count') ?? 0;

    //     $totalVisit = Visit::sum('count');

    //     return response()->json([
    //         'today' => number_format($todayVisit, 0, '.', ','),
    //         'total' => number_format($totalVisit, 0, '.', ','),
    //     ]);
    // }

    // public function liveVisits()
    // {
    //     $today = now()->toDateString();

    //     $todayVisit = Visit::where('date', $today)->value('count') ?? 0;

    //     $totalVisit = Visit::sum('count')+1000;

    //     return response()->json([
    //         'today' => number_format($todayVisit, 0, '.', ',') . ' = ' . $this->shortNumber($todayVisit),
    //         'total' => number_format($totalVisit, 0, '.', ',') . ' = ' . $this->shortNumber($totalVisit),
    //     ]);
    // }

    // /* SHORT FORMAT FUNCTION */
    // private function shortNumber($num)
    // {
    //     if ($num >= 1000000000) {
    //         return round($num / 1000000000, 1) . 'B';
    //     }

    //     if ($num >= 1000000) {
    //         return round($num / 1000000, 1) . 'M';
    //     }

    //     if ($num >= 1000) {
    //         return round($num / 1000, 1) . 'K';
    //     }

    //     return $num;
    // }

    public function liveVisits()
    {
        $today = now()->toDateString();

        $todayVisit = Visit::where('date', $today)->value('count') ?? 0;

        $totalVisit = Visit::sum('count');

        return response()->json([
            'today' => $this->shortNumber($todayVisit),
            'total' => $this->shortNumber($totalVisit),
        ]);
    }

    /* SHORT FORMAT: K / M / B */
    private function shortNumber($num)
    {
        if ($num >= 1000000000) {
            return round($num / 1000000000, 1) . 'B';
        }

        if ($num >= 1000000) {
            return round($num / 1000000, 2) . 'M';
        }

        if ($num >= 1000) {
            return round($num / 1000, 1) . 'k';
        }

        return $num;
    }
    
}
