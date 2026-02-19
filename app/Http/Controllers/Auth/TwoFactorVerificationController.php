<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use PragmaRX\Google2FA\Google2FA;
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

        $user = User::findOrFail($request->session()->get('login.id'));

        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('login');
        }

        $google2fa = new Google2FA();
        $secret = Crypt::decryptString($user->two_factor_secret);
        
        // Try to verify as TOTP code first
        $valid = $google2fa->verifyKey($secret, $request->code, 2);
        
        // If not valid, try recovery code
        if (!$valid) {
            $valid = $user->useRecoveryCode($request->code);
        }

        if (!$valid) {
            return back()->withErrors(['code' => __('The provided two factor authentication code was invalid.')]);
        }

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
