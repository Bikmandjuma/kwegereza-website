<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

/**
 * "Continue with Google" for students. Net-new — no OAuth of any kind
 * existed anywhere in this app before this. Requires real credentials
 * from Google Cloud Console (GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET in
 * .env) that this project has no way to provision itself; without them
 * this controller will fail with a clear Socialite configuration
 * exception rather than doing anything silently wrong.
 */
class StudentGoogleAuthController extends Controller
{
    public function redirect(Request $request)
    {
        $next = $request->query('next');
        if ($next && str_starts_with($next, '/') && ! str_starts_with($next, '//')) {
            $request->session()->put('url.intended', $next);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('student.login')
                ->with('error', 'Ntibyakunze kwinjira na Google. Ongera ugerageze cyangwa ukoreshe email/ijambo banga.');
        }

        // Match on google_id first (returning user), then fall back to
        // email (a student who registered normally before ever trying
        // Google — link the accounts rather than creating a duplicate).
        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $googleUser->getEmail())->first();

        if ($user) {
            if (! $user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        } else {
            $user = User::create([
                'user_code' => 'STD-'.Str::upper(Str::random(6)),
                'firstname' => $googleUser->user['given_name'] ?? explode(' ', $googleUser->getName())[0] ?? 'Umunyeshuri',
                'lastname'  => $googleUser->user['family_name'] ?? '',
                'email'     => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password'  => null,
            ]);
        }

        if ($user->deactivated_at) {
            return redirect()->route('student.login')
                ->with('error', 'Konti yawe yahagaritswe. Vugana n\'ubuyobozi niba ubona ari ikosa.');
        }

        Auth::guard('student')->login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('student.dashboard'))
            ->with('info', 'Ikaze ' . $user->firstname);
    }
}
