<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountEmailBlockedMail;
use App\Models\AdminAuditLog;
use App\Models\BlockedEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class BlockedEmailController extends Controller
{
    public function index(Request $request): View
    {
        $rules = BlockedEmail::query()
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.blocked-emails.index', compact('rules'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'match_type' => ['required', 'in:email,domain'],
            'pattern' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $pattern = strtolower(trim((string) $validated['pattern']));
        if ($validated['match_type'] === 'domain') {
            $pattern = ltrim($pattern, '@');
        }

        $note = isset($validated['note']) ? trim((string) $validated['note']) : null;
        if ($note === '') {
            $note = null;
        }

        $rule = BlockedEmail::query()->create([
            'match_type' => $validated['match_type'],
            'pattern' => $pattern,
            'note' => $note,
            'created_by' => auth()->id(),
        ]);

        AdminAuditLog::record('blocked_email.created', [
            'id' => $rule->id,
            'match_type' => $rule->match_type,
            'pattern' => $rule->pattern,
            'note' => $rule->note,
        ]);

        if ($validated['match_type'] === 'email' && filter_var($pattern, FILTER_VALIDATE_EMAIL)) {
            try {
                Mail::to($pattern)->send(new AccountEmailBlockedMail($pattern, $note));
            } catch (Throwable $e) {
                Log::error('Failed to send account blocked email', [
                    'email' => $pattern,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('admin.blocked-emails.index')
            ->with('success', __('Blocked email rule added.'));
    }

    public function destroy(BlockedEmail $blockedEmail): RedirectResponse
    {
        AdminAuditLog::record('blocked_email.deleted', [
            'id' => $blockedEmail->id,
            'match_type' => $blockedEmail->match_type,
            'pattern' => $blockedEmail->pattern,
        ]);

        $blockedEmail->delete();

        return redirect()
            ->route('admin.blocked-emails.index')
            ->with('success', __('Blocked email rule removed.'));
    }
}
