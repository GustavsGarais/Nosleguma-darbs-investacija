<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use PragmaRX\Google2FALaravel\Google2FA;
use Illuminate\Support\Facades\Crypt;

class TwoFactorVerificationController extends Controller
{
    /**
     * Show the 2FA verification form
     */
    public function create(Request $request): View
    {
        if (!$request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Handle the 2FA verification
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        if (!$request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        $userId = (int) $request->session()->get('login.id');
        $throttleKey = '2fa:challenge:' . $userId . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'code' => __('Too many attempts. Please try again in :seconds seconds.', [
                    'seconds' => $seconds,
                ]),
            ]);
        }

        $user = User::findOrFail($userId);

        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('login');
        }

        /** @var \PragmaRX\Google2FALaravel\Google2FA $google2fa */
        $google2fa = app(Google2FA::class);
        $secret = Crypt::decryptString($user->two_factor_secret);
        $code = preg_replace('/\s+/', '', trim((string) $request->code));
        
        // Try to verify as TOTP code first
        $valid = ctype_digit($code) && strlen($code) === 6
            ? $google2fa->verifyKey($secret, $code, 2)
            : false;
        
        // If not valid, try recovery code
        if (!$valid) {
            $valid = $user->useRecoveryCode($code);
        }

        if (!$valid) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['code' => __('The provided two factor authentication code was invalid.')]);
        }

        RateLimiter::clear($throttleKey);

        // Get remember flag from session
        $remember = $request->session()->get('login.remember', false);
        
        // Clear the login session
        $request->session()->forget('login.id');
        $request->session()->forget('login.remember');

        // Log the user in
        Auth::login($user, $remember || $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
