<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SupportTicketController extends Controller
{
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
}
