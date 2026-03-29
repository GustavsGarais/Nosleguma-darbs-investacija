<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile / settings form.
     */
    public function edit(Request $request): View
    {
        return view('settings.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information (name only — email is locked).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Only 'name' comes through validated — email is never touched.
        $request->user()->fill($request->validated());
        $request->user()->save();

        return Redirect::route('settings')->with('status', 'profile-updated');
    }

    /**
     * Persist display currency (EUR base) for the authenticated user.
     */
    public function updateCurrency(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'currency_preference' => ['required', 'string', 'in:EUR,USD,GBP,JPY'],
        ]);

        $request->user()->forceFill([
            'currency_preference' => $validated['currency_preference'],
        ])->save();

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'currency_preference' => $request->user()->currency_preference,
            ]);
        }

        return Redirect::route('settings')->with('status', 'currency-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}