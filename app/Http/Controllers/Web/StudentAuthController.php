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
    /**
     * Shared by loginForm/registerForm here and
     * StudentGoogleAuthController::redirect() — a guest arriving from
     * the "Ni mukanya" live-class popup carries a ?next= query param
     * (the class join URL) rather than having been bounced here by
     * StudentAuthMiddleware (which stores the intended URL itself via
     * redirect()->guest()). This stores that same 'url.intended'
     * session key manually, so all three auth entry points — normal
     * login, normal registration, and Google — end up honoring the
     * exact same redirect()->intended() call already in place after a
     * successful login, without three separate ad-hoc mechanisms.
     * Only accepts a local, same-app path — never an absolute/external
     * URL — so this can't be turned into an open-redirect vector.
     */
    private function rememberIntendedFromQuery(Request $request): void
    {
        $next = $request->query('next');

        if ($next && str_starts_with($next, '/') && ! str_starts_with($next, '//')) {
            $request->session()->put('url.intended', $next);
        }
    }

    public function registerForm(Request $request)
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }

        $this->rememberIntendedFromQuery($request);

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

        return redirect()->intended(route('student.dashboard'))->with('info', 'Ikaze kuri Kwegereza Islam Umuryango, ' . $user->firstname . '!');
    }

    public function loginForm(Request $request)
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }

        $this->rememberIntendedFromQuery($request);

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

            return redirect()->intended(route('student.dashboard'))
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
