<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
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
            'simulation_error' => 'Simulation Error',
            'visual_error' => 'Visual/UI Error',
            'personal_error' => 'Account/Personal Error',
            'translation_error' => 'Translation Error',
            'performance_issue' => 'Performance Issue',
            'bug_report' => 'Bug Report',
            'feature_request' => 'Feature Request',
            'other' => 'Other',
            default => 'Unknown',
        };
    }
}
