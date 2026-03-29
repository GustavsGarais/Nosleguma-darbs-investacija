<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FALaravel\Google2FA;
use Illuminate\Support\Facades\Crypt;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA setup page
     */
    public function show(Request $request)
    {
        $user = $request->user();
        /** @var \PragmaRX\Google2FALaravel\Google2FA $google2fa */
        $google2fa = app(Google2FA::class);
        
        // Generate secret if not exists (or if stored value can't be decrypted)
        if (!$user->two_factor_secret) {
            $user->two_factor_secret = Crypt::encryptString($google2fa->generateSecretKey());
            $user->save();
        }

        try {
            $secret = Crypt::decryptString($user->two_factor_secret);
        } catch (\Throwable $e) {
            // If APP_KEY changed or value is corrupted, re-generate a fresh secret
            $user->two_factor_secret = Crypt::encryptString($google2fa->generateSecretKey());
            $user->two_factor_confirmed_at = null;
            $user->two_factor_recovery_codes = null;
            $user->save();
            $secret = Crypt::decryptString($user->two_factor_secret);
        }
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Generate QR code inline image (data URI) using Pragmarx QR service
        $qrCodeDataUri = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret,
            300
        );
        
        return view('settings.two-factor', [
            'user' => $user,
            'secret' => $secret,
            'qrCode' => $qrCodeDataUri,
            'recoveryCodes' => $user->getRecoveryCodes(),
        ]);
    }

    /**
     * Enable 2FA after verification
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();
        if (!$user->two_factor_secret) {
            return redirect()->route('settings.two-factor')
                ->withErrors(['code' => __('Two-factor authentication secret is missing. Please reload the setup page and try again.')]);
        }
        $throttleKey = '2fa:enable:' . $user->id . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'code' => __('Too many attempts. Please try again in :seconds seconds.', [
                    'seconds' => $seconds,
                ]),
            ]);
        }

        /** @var \PragmaRX\Google2FALaravel\Google2FA $google2fa */
        $google2fa = app(Google2FA::class);
        try {
            $secret = Crypt::decryptString($user->two_factor_secret);
        } catch (\Throwable $e) {
            return redirect()->route('settings.two-factor')
                ->withErrors(['code' => __('Two-factor authentication secret is invalid. Please reload the setup page and try again.')]);
        }
        $code = preg_replace('/\s+/', '', trim((string) $request->code));
        
        // Verify the code
        $valid = ctype_digit($code) && strlen($code) === 6
            ? $google2fa->verifyKey($secret, $code, 2) // 2 = 2 time windows tolerance
            : false;
        
        if (!$valid) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['code' => __('The provided two factor authentication code was invalid.')]);
        }

        RateLimiter::clear($throttleKey);
        
        // Generate recovery codes
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = Str::random(10);
        }
        
        // Enable 2FA
        $user->two_factor_recovery_codes = $recoveryCodes;
        $user->two_factor_confirmed_at = now();
        $user->save();
        
        return redirect()->route('settings')->with('status', 'two-factor-enabled');
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();
        
        return redirect()->route('settings')->with('status', 'two-factor-disabled');
    }

    /**
     * Regenerate recovery codes
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $user = $request->user();
        
        if (!$user->hasTwoFactorEnabled()) {
            return redirect()->route('settings')->withErrors(['two_factor' => __('Two-factor authentication is not enabled.')]);
        }
        
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = Str::random(10);
        }
        
        $user->two_factor_recovery_codes = $recoveryCodes;
        $user->save();
        
        return redirect()->route('settings.two-factor')->with('recoveryCodes', $recoveryCodes);
    }
}
