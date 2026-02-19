<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Crypt;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA setup page
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $google2fa = new Google2FA();
        
        // Generate secret if not exists
        if (!$user->two_factor_secret) {
            $user->two_factor_secret = Crypt::encryptString($google2fa->generateSecretKey());
            $user->save();
        }
        
        $secret = Crypt::decryptString($user->two_factor_secret);
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );
        
        // Generate QR code image
        $qrCode = QrCode::create($qrCodeUrl)
            ->setSize(300)
            ->setMargin(10);
        
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $qrCodeDataUri = 'data:image/png;base64,' . base64_encode($result->getString());
        
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
        $google2fa = new Google2FA();
        $secret = Crypt::decryptString($user->two_factor_secret);
        
        // Verify the code
        $valid = $google2fa->verifyKey($secret, $request->code, 2); // 2 = 2 time windows tolerance
        
        if (!$valid) {
            return back()->withErrors(['code' => __('The provided two factor authentication code was invalid.')]);
        }
        
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
