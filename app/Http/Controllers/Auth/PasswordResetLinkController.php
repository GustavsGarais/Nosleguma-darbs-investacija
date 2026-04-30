<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetInitiatedMail;
use App\Models\BlockedEmail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Throwable;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(Request $request): View
    {
        return view('auth.forgot-password', [
            'fromSupport' => $request->query('from') === 'support',
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'email' => ['required', 'email'],
        ];

        if ($request->boolean('from_support')) {
            $rules['support_message'] = ['nullable', 'string', 'max:2000'];
        }

        $validated = $request->validate($rules);

        if (BlockedEmail::isBlocked($validated['email'])) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('This email address is blocked.')]);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status !== Password::RESET_LINK_SENT) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }

        $user = User::where('email', $validated['email'])->first();

        if ($user !== null) {
            $note = $request->boolean('from_support')
                ? (isset($validated['support_message']) ? trim((string) $validated['support_message']) : '')
                : null;
            if ($note === '') {
                $note = null;
            }

            try {
                Mail::to($user->email)->send(new PasswordResetInitiatedMail($user, $note));
            } catch (Throwable $e) {
                report($e);
            }
        }

        $input = $request->only('email');
        if ($request->boolean('from_support')) {
            $input['from_support'] = '1';
            if ($request->filled('support_message')) {
                $input['support_message'] = $request->input('support_message');
            }
        }

        return back()->with('status', __($status))->withInput($input);
    }
}
