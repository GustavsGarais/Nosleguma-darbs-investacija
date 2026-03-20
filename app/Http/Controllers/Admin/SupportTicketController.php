<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorDisabledMail;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SupportTicketController extends Controller
{
    private const SUPPORT_SUBJECT = 'Lost 2FA / Account Recovery';

    /**
     * Display all support tickets
     */
    public function index(Request $request): View
    {
        $query = SupportTicket::with(['user', 'assignedAdmin']);

        // Filter by status
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        // Filter by priority
        if ($request->has('priority') && $request->get('priority') !== '') {
            $query->where('priority', $request->get('priority'));
        }

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'urgent' => SupportTicket::where('priority', 'urgent')->whereIn('status', ['open', 'in_progress'])->count(),
        ];

        return view('admin.tickets.index', compact('tickets', 'stats'));
    }

    /**
     * Display ticket details
     */
    public function show(SupportTicket $ticket): View
    {
        $ticket->load(['user', 'assignedAdmin']);
        $admins = User::where('is_admin', true)->get();

        return view('admin.tickets.show', compact('ticket', 'admins'));
    }

    /**
     * Update ticket status
     */
    public function updateStatus(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'admin_response' => 'nullable|string|max:5000',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $ticket->status = $validated['status'];
        $ticket->priority = $validated['priority'];
        
        if (isset($validated['admin_response'])) {
            $ticket->admin_response = $validated['admin_response'];
        } else {
            $ticket->admin_response = null;
        }

        if (isset($validated['assigned_to'])) {
            $ticket->assigned_to = $validated['assigned_to'];
        } else {
            $ticket->assigned_to = null;
        }

        if ($validated['status'] === 'resolved' && !$ticket->resolved_at) {
            $ticket->resolved_at = now();
        }

        $ticket->save();

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully!');
    }

    /**
     * Delete ticket
     */
    public function destroy(SupportTicket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket deleted successfully!');
    }

    /**
     * Admin action: disable 2FA for the user related to this ticket and email them.
     */
    public function disableTwoFactor(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'admin_response' => 'nullable|string|max:5000',
        ]);

        if ($ticket->subject !== self::SUPPORT_SUBJECT) {
            abort(404);
        }

        $ticket->loadMissing(['user']);

        $user = $ticket->user;
        if (!$user && $ticket->contact_email) {
            $user = User::where('email', $ticket->contact_email)->first();
        }

        // Mark ticket resolved and store the admin response.
        $ticket->status = 'resolved';
        $ticket->priority = 'urgent';
        $ticket->resolved_at = now();
        $ticket->admin_response = $validated['admin_response']
            ?? '2FA was disabled by an admin after an account recovery request.';
        $ticket->save();

        if (!$user) {
            return redirect()->route('admin.tickets.show', $ticket)
                ->with('success', 'Ticket resolved, but no matching user was found for the provided email.');
        }

        // Disable 2FA so the user can log in again.
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        // Send email (depends on your MAIL_* configuration; default is MAIL_MAILER=log).
        try {
            Mail::to($user->email)->send(new TwoFactorDisabledMail($user->name, $ticket->id));
        } catch (\Throwable $e) {
            // Redirect successfully, but log the error so you can debug email sending.
            Log::error('Failed to send 2FA disabled email', [
                'user_id' => $user?->id,
                'ticket_id' => $ticket->id,
                'contact_email' => $ticket->contact_email,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', '2FA disabled and user notified (if mail is configured).');
    }
}
