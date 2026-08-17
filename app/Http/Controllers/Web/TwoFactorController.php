<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function __construct(private TwoFactorService $twoFactor)
    {
    }

    // --- Setup / management (student must already be logged in) ---

    public function setup()
    {
        $user = Auth::guard('student')->user();

        if ($user->two_factor_enabled) {
            return redirect()->route('student.2fa.manage');
        }

        // A fresh secret is generated each time this page loads and stashed
        // in session until confirmed — it only becomes permanent once the
        // user proves they can generate a valid code with it.
        $secret = session('2fa_setup_secret') ?: $this->twoFactor->generateSecretKey();
        session(['2fa_setup_secret' => $secret]);

        $otpAuthUri = $this->twoFactor->getOtpAuthUri($secret, $user->email ?: $user->phone);

        return view('Users.User.two-factor-setup', compact('secret', 'otpAuthUri'));
    }

    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $secret = session('2fa_setup_secret');

        if (!$secret || !$this->twoFactor->verifyCode($secret, $request->code)) {
            return back()->with('error', 'Kode ntabwo ari yo. Ongera ugerageze.');
        }

        $user = Auth::guard('student')->user();
        $recoveryCodes = $this->twoFactor->generateRecoveryCodes();

        $user->update([
            'two_factor_secret'          => $secret,
            'two_factor_enabled'         => true,
            'two_factor_recovery_codes'  => json_encode($this->twoFactor->hashRecoveryCodes($recoveryCodes)),
            'two_factor_confirmed_at'    => now(),
        ]);

        session()->forget('2fa_setup_secret');

        return view('Users.User.two-factor-recovery-codes', compact('recoveryCodes'));
    }

    public function manage()
    {
        $user = Auth::guard('student')->user();

        return view('Users.User.two-factor-manage', compact('user'));
    }

    public function disable(Request $request)
    {
        $request->validate(['password' => 'required|string']);

        $user = Auth::guard('student')->user();

        if (!\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Ijambo ry\'ibanga si ryo.');
        }

        $user->update([
            'two_factor_secret'         => null,
            'two_factor_enabled'        => false,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Two-Factor Authentication yahagaritswe.');
    }

    // --- Login challenge (NOT logged in yet — a 2fa_user_id must be pending in session) ---

    public function challengeForm(Request $request)
    {
        if (!session('2fa_user_id')) {
            return redirect()->route('student.login');
        }

        return view('Users.User.two-factor-challenge');
    }

    public function challengeSubmit(Request $request)
    {
        $userId = session('2fa_user_id');

        if (!$userId) {
            return redirect()->route('student.login');
        }

        $user = User::findOrFail($userId);
        $request->validate(['code' => 'required|string']);

        $verified = $this->twoFactor->verifyCode($user->two_factor_secret, $request->code);

        // Fall back to a recovery code if the 6-digit TOTP didn't match —
        // recovery codes are for "I lost my phone", not everyday login.
        if (!$verified && $user->two_factor_recovery_codes) {
            $hashedCodes = json_decode($user->two_factor_recovery_codes, true) ?: [];
            $index = $this->twoFactor->verifyRecoveryCode($hashedCodes, $request->code);

            if ($index !== null) {
                unset($hashedCodes[$index]);
                $user->update(['two_factor_recovery_codes' => json_encode(array_values($hashedCodes))]);
                $verified = true;
            }
        }

        if (!$verified) {
            return back()->with('error', 'Kode ntabwo ari yo.');
        }

        $remember = session('2fa_remember', false);
        session()->forget(['2fa_user_id', '2fa_remember']);

        Auth::guard('student')->login($user, $remember);
        $request->session()->regenerate();

        return redirect()->route('student.dashboard')->with('info', 'Ikaze ' . $user->firstname);
    }
}
