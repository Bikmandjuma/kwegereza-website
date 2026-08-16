<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;

/**
 * Supplies the teacher picker for the Darsat create/edit form — the same
 * ['sheikh','ustadh']-filtered list Web\AdminController@darsat already uses
 * for its own Blade dropdown, just exposed as JSON for React.
 */
class TeacherOptionsController extends Controller
{
    public function index(Request $request)
    {
        $teachers = Owner::whereIn('title', ['sheikh', 'ustadh'])
            ->orderBy('firstname')
            ->get(['id', 'firstname', 'lastname', 'title']);

        return response()->json([
            'success' => true,
            'message' => null,
            'data' => $teachers->map(fn ($o) => [
                'id' => $o->id,
                'name' => trim($o->firstname.' '.$o->lastname),
                'title' => $o->title,
            ]),
        ]);
    }
}
