<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    /** Stored verbatim for admin automation (2FA recovery flow); use {@see getDisplaySubject()} in UI. */
    public const INTERNAL_TWO_FACTOR_RECOVERY_SUBJECT = 'Lost 2FA / Account Recovery';

    protected $fillable = [
        'user_id',
        'subject',
        'contact_email',
        'description',
        'error_type',
        'status',
        'priority',
        'admin_response',
        'assigned_to',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved' || $this->status === 'closed';
    }

    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'urgent' => '#ef4444',
            'high' => '#f59e0b',
            'medium' => '#3b82f6',
            'low' => '#10b981',
            default => '#6b7280',
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'open' => '#3b82f6',
            'in_progress' => '#f59e0b',
            'resolved' => '#10b981',
            'closed' => '#6b7280',
            default => '#6b7280',
        };
    }

    public function getErrorTypeLabel(): string
    {
        return match($this->error_type) {
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

    public function isTwoFactorRecoveryTicket(): bool
    {
        return $this->subject === self::INTERNAL_TWO_FACTOR_RECOVERY_SUBJECT;
    }

    public function getDisplaySubject(): string
    {
        if ($this->isTwoFactorRecoveryTicket()) {
            return __('support.two_factor_recovery_subject');
        }

        return $this->subject;
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'open' => __('ticket.status.open'),
            'in_progress' => __('ticket.status.in_progress'),
            'resolved' => __('ticket.status.resolved'),
            'closed' => __('ticket.status.closed'),
            default => $this->status,
        };
    }

    public function getPriorityLabel(): string
    {
        return match ($this->priority) {
            'low' => __('ticket.priority.low'),
            'medium' => __('ticket.priority.medium'),
            'high' => __('ticket.priority.high'),
            'urgent' => __('ticket.priority.urgent'),
            default => $this->priority,
        };
    }
}
