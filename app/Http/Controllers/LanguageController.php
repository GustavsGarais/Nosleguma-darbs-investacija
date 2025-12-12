<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Switch the interface language for the active session.
     */
    public function switch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => 'required|in:en,lv',
        ]);

        $request->session()->put('locale', $validated['locale']);

        return back();
    }
}

