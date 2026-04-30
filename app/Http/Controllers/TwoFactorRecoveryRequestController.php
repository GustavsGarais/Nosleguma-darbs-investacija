<?php

namespace App\Http\Controllers;

use App\Models\BlockedEmail;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TwoFactorRecoveryRequestController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('recovery', [
            'contact_email' => 'required|email|max:255',
            'description' => 'required|string|max:2000',
        ], [
            'contact_email.required' => __('Please provide your email address.'),
            'contact_email.email' => __('Please enter a valid email address.'),
            'description.required' => __('Please describe your situation.'),
            'description.max' => __('The description must not exceed 2000 characters.'),
        ]);

        // Keep descriptions readable in admin UI (similar to the existing reports page).
        $text = trim($validated['description']);
        $words = preg_split('/\s+/', $text);
        $words = array_filter($words, fn ($word) => ! empty(trim($word)));
        $wordCount = count($words);

        if ($wordCount > 400) {
            return back()
                ->withInput()
                ->withErrors(
                    ['description' => __('The description must not exceed 400 words. You have :count words.', ['count' => $wordCount])],
                    'recovery',
                );
        }

        $contactEmail = $validated['contact_email'];
        if (BlockedEmail::isBlocked($contactEmail)) {
            return back()
                ->withInput()
                ->withErrors(['contact_email' => __('This email address is blocked.')], 'recovery');
        }
        $user = User::where('email', $contactEmail)->first();

        SupportTicket::create([
            'user_id' => $user?->id,
            'contact_email' => $contactEmail,
            'subject' => self::SUPPORT_SUBJECT,
            'description' => $validated['description'],
            // Keep within existing enum while still distinguishing by subject for the admin action.
            'error_type' => 'personal_error',
            'status' => 'open',
            'priority' => 'urgent',
        ]);

        return redirect()
            ->route('support.thanks')
            ->with('success', __('Your account recovery request was submitted. Our team will review it soon.'));
    }
}
