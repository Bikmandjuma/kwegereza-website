<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Amatangazo;
use App\Models\Inyandiko;
use App\Traits\HandlesFileUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentDashboardController extends Controller
{
    use HandlesFileUploads;
    public function dashboard()
    {
        $user = Auth::guard('student')->user();

        $recentAmatangazo = Amatangazo::published()->latest('published_at')->limit(3)->get();
        $recentInyandiko = Inyandiko::published()->latest('published_at')->limit(3)->get();

        return view('Users.User.dashboard', compact('user', 'recentAmatangazo', 'recentInyandiko'));
    }

    public function profile()
    {
        $user = Auth::guard('student')->user();

        return view('Users.User.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('student')->user();

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'email'     => 'nullable|email|unique:users,email,' . $user->id,
            'phone'     => 'required|string|unique:users,phone,' . $user->id,
            'gender'    => 'nullable|string|max:20',
            'image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $imageName = $user->image;

        if ($request->hasFile('image')) {
            $this->deleteUploadedFile($user->image, 'students');
            $imageName = $this->storeUploadedFile($request->file('image'), 'students');
        }

        $user->update([
            'firstname' => $request->firstname,
            'lastname'  => $request->lastname,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'gender'    => $request->gender,
            'image'     => $imageName,
        ]);

        return redirect()->route('student.profile')->with('success', 'Umwirondoro wawe wahinduwe neza.');
    }

    public function settings()
    {
        return view('Users.User.settings');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::guard('student')->user();

        $request->validate([
            'current_password' => 'required|string',
            'password'          => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Ijambo ry\'ibanga rya none si ryo.');
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Ijambo ry\'ibanga ryahinduwe neza.');
    }
}
