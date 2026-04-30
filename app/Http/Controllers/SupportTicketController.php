<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    /**
     * Store a newly created ticket
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('ticket', [
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'error_type' => 'required|in:simulation_error,visual_error,personal_error,translation_error,performance_issue,bug_report,feature_request,other',
        ], [
            'subject.required' => __('Please provide a title for your report.'),
            'subject.max' => __('The title must not exceed 255 characters.'),
            'description.required' => __('Please describe the issue you are experiencing.'),
            'description.max' => __('The description must not exceed 2000 characters (approximately 400 words).'),
            'error_type.required' => __('Please select the type of error you are reporting.'),
        ]);

        // Count words in description (handles multiple languages better)
        $text = trim($validated['description']);
        $words = preg_split('/\s+/', $text);
        $words = array_filter($words, function ($word) {
            return ! empty(trim($word));
        });
        $wordCount = count($words);

        if ($wordCount > 400) {
            return back()
                ->withInput()
                ->withErrors(
                    ['description' => __('The description must not exceed 400 words. You have :count words.', ['count' => $wordCount])],
                    'ticket',
                );
        }

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'error_type' => $validated['error_type'],
            'status' => 'open',
            'priority' => $this->determinePriority($validated['error_type']),
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', __('Your support ticket has been submitted successfully! We will review it and get back to you soon.'));
    }

    /**
     * Display the specified ticket
     */
    public function show(SupportTicket $ticket): View
    {
        // Ensure user can only view their own tickets
        if ($ticket->user_id !== auth()->id()) {
            abort(403, __('Unauthorized access.'));
        }

        return view('tickets.show', compact('ticket'));
    }

    /**
     * Display user's tickets
     */
    public function index(): View
    {
        $tickets = SupportTicket::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Determine priority based on error type
     */
    private function determinePriority(string $errorType): string
    {
        return match ($errorType) {
            'simulation_error', 'performance_issue' => 'high',
            'visual_error', 'translation_error' => 'medium',
            'bug_report' => 'high',
            'personal_error' => 'urgent',
            'feature_request', 'other' => 'low',
            default => 'medium',
        };
    }

    /**
     * Get error type label
     */
    public static function getErrorTypeLabel(string $type): string
    {
        return match ($type) {
            'simulation_error' => __('ticket_type.simulation_error'),
            'visual_error' => __('ticket_type.visual_error'),
            'personal_error' => __('ticket_type.personal_error'),
            'translation_error' => __('ticket_type.translation_error'),
            'performance_issue' => __('ticket_type.performance_issue'),
            'bug_report' => __('ticket_type.bug_report'),
            'feature_request' => __('ticket_type.feature_request'),
            'other' => __('ticket_type.other'),
            default => __('ticket_type.unknown'),
        };
    }
}
