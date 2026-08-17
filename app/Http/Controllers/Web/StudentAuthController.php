<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentAuthController extends Controller
{
    public function registerForm()
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }

        if (!\App\Models\FeatureFlag::enabled('registration')) {
            return redirect()->route('student.login')->with('error', 'Kwiyandikisha ntabwo bihari ubu. Ongera ugerageze vuba.');
        }

        return view('Users.User.auth.register');
    }

    public function submitRegister(Request $request)
    {
        if (!\App\Models\FeatureFlag::enabled('registration')) {
            return redirect()->route('student.login')->with('error', 'Kwiyandikisha ntabwo bihari ubu.');
        }

        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'email'     => 'nullable|email|unique:users,email',
            'phone'     => 'required|string|unique:users,phone',
            'password'  => 'required|string|min:6|confirmed',
        ], [
            'phone.unique' => 'Iyi numero ya telefone isanzwe ifite konti.',
            'email.unique' => 'Iyi email isanzwe ifite konti.',
        ]);

        $user = User::create([
            'user_code' => 'STD-' . Str::upper(Str::random(6)),
            'firstname' => $request->firstname,
            'lastname'  => $request->lastname,
            'email'     => $request->email,
            'phone'     => $request->phone,
            'password'  => Hash::make($request->password),
        ]);

        Auth::guard('student')->login($user);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard')->with('info', 'Ikaze kuri Kwegereza Islam Umuryango, ' . $user->firstname . '!');
    }

    public function loginForm()
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }

        return view('Users.User.auth.login');
    }

    public function submitLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Nyamuneka andika email cyangwa numero ya telefone.',
            'password.required' => 'Nyamuneka andika ijambo ry\'ibanga.',
        ]);

        $loginField = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        if (Auth::guard('student')->attempt([
            $loginField => $request->input('username'),
            'password'  => $request->input('password'),
        ], false)) {
            $user = Auth::guard('student')->user();

            if ($user->deactivated_at) {
                Auth::guard('student')->logout();

                return back()->with('error', 'Konti yawe yahagaritswe. Vugana n\'ubuyobozi niba ubona ari ikosa.');
            }

            if ($user->two_factor_enabled) {
                Auth::guard('student')->logout();
                $request->session()->put('2fa_user_id', $user->id);
                $request->session()->put('2fa_remember', $request->boolean('remember'));

                return redirect()->route('student.2fa.challenge');
            }

            if ($request->boolean('remember')) {
                Auth::guard('student')->login($user, true);
            }

            $request->session()->regenerate();

            return redirect()->route('student.dashboard')
                ->with('info', 'Ikaze ' . $user->firstname);
        }

        return back()->withInput($request->only('username'))->with([
            'error' => 'Amakuru wanditse si yo, ongera ugerageze.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('guest.home')->with('info', 'Wasohotse neza.');
    }
}
