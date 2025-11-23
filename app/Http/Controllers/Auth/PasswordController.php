<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                'string',
                'min:12',
                'regex:/[a-z]/',  // At least one lowercase
                'regex:/[A-Z]/',  // At least one uppercase
                'regex:/[0-9!@#$%^&*]/',  // At least one number or symbol
            ],
        ], [
            'password.min' => 'The password must be at least 12 characters.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, and one number or symbol (!@#$%^&*).',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
