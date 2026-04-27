@extends('layouts.admin')

@section('title', __('Ticket #:id', ['id' => $ticket->id]))

@section('content')
<div class="admin-header admin-header-row">
    <div>
        <h1>{{ __('Ticket #:id', ['id' => $ticket->id]) }}</h1>
        <p>{{ $ticket->getDisplaySubject() }}</p>
    </div>
    <a href="{{ route('admin.tickets.index') }}" class="admin-btn admin-btn-secondary" style="flex-shrink: 0;">{{ __('Back to tickets') }}</a>
</div>

<div class="admin-grid-2">
    <div>
        <div class="admin-card">
            <h2 style="margin: 0 0 16px; font-size: 18px; font-weight: 600;">{{ __('Ticket details') }}</h2>
            <div style="margin-bottom: 20px;">
                <p style="margin: 0 0 8px; font-size: 13px; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Subject') }}</p>
                <p class="admin-prose" style="margin: 0; font-size: 16px; font-weight: 500;">{{ $ticket->getDisplaySubject() }}</p>
            </div>
            <div style="margin-bottom: 20px;">
                <p style="margin: 0 0 8px; font-size: 13px; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Description') }}</p>
                <div class="admin-prose" style="padding: 16px; background: var(--admin-surface-light); border-radius: 8px; white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word; line-height: 1.6;">
                    {{ $ticket->description }}
                </div>
            </div>
            @if($ticket->admin_response)
                <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--admin-border);">
                    <p style="margin: 0 0 8px; font-size: 13px; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Admin response') }}</p>
                    <div class="admin-prose" style="padding: 16px; background: rgba(59, 130, 246, 0.1); border-left: 3px solid var(--admin-primary); border-radius: 8px; white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word; line-height: 1.6;">
                        {{ $ticket->admin_response }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div>
        <div class="admin-card">
            <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">{{ __('Update ticket') }}</h2>
            <form method="POST" action="{{ route('admin.tickets.updateStatus', $ticket) }}">
                @csrf
                @method('PATCH')

                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">{{ __('Status') }}</label>
                    <select name="status" class="admin-select" required>
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>{{ __('ticket.status.open') }}</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>{{ __('ticket.status.in_progress') }}</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>{{ __('ticket.status.resolved') }}</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>{{ __('ticket.status.closed') }}</option>
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">{{ __('Priority') }}</label>
                    <select name="priority" class="admin-select" required>
                        <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>{{ __('ticket.priority.low') }}</option>
                        <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>{{ __('ticket.priority.medium') }}</option>
                        <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>{{ __('ticket.priority.high') }}</option>
                        <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>{{ __('ticket.priority.urgent') }}</option>
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">{{ __('Assign to') }}</label>
                    <select name="assigned_to" class="admin-select">
                        <option value="">{{ __('Unassigned') }}</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ $ticket->assigned_to === $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">{{ __('Admin response') }}</label>
                    <textarea name="admin_response" rows="6" class="admin-textarea" placeholder="{{ __('Add your response here...') }}">{{ old('admin_response', $ticket->admin_response) }}</textarea>
                </div>

                <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">{{ __('Update ticket') }}</button>
            </form>
        </div>

        <div class="admin-card" style="margin-top: 24px;">
            <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">{{ __('Ticket information') }}</h2>
            <div style="display: grid; gap: 12px;">
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">{{ __('User') }}</p>
                    @if($ticket->user)
                        <a href="{{ route('admin.users.show', $ticket->user) }}" style="color: var(--admin-primary); text-decoration: none; font-weight: 500;">
                            {{ $ticket->user->name }}
                        </a>
                        <p style="margin: 4px 0 0; font-size: 12px; color: var(--admin-text-muted);">{{ $ticket->user->email }}</p>
                    @else
                        <span style="color: var(--admin-text-muted);">{{ __('Anonymous') }}</span>
                        @if($ticket->contact_email)
                            <p style="margin: 6px 0 0; font-size: 12px; color: var(--admin-text-muted);">{{ $ticket->contact_email }}</p>
                        @endif
                    @endif
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">{{ __('Error Type') }}</p>
                    <p style="margin: 0; font-weight: 500;">{{ $ticket->getErrorTypeLabel() }}</p>
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">{{ __('Status') }}</p>
                    <span class="admin-badge" style="background: {{ $ticket->getStatusColor() }}20; color: {{ $ticket->getStatusColor() }};">
                        {{ $ticket->getStatusLabel() }}
                    </span>
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">{{ __('Priority') }}</p>
                    <span class="admin-badge" style="background: {{ $ticket->getPriorityColor() }}20; color: {{ $ticket->getPriorityColor() }};">
                        {{ $ticket->getPriorityLabel() }}
                    </span>
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">{{ __('Assigned to') }}</p>
                    @if($ticket->assignedAdmin)
                        <span style="font-weight: 500;">{{ $ticket->assignedAdmin->name }}</span>
                    @else
                        <span style="color: var(--admin-text-muted);">{{ __('Unassigned') }}</span>
                    @endif
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">{{ __('Created') }}</p>
                    <span style="font-size: 13px;">{{ $ticket->created_at->translatedFormat('d MMM Y HH:mm') }}</span>
                </div>
                @if($ticket->resolved_at)
                    <div>
                        <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">{{ __('Resolved at') }}</p>
                        <span style="font-size: 13px;">{{ $ticket->resolved_at->translatedFormat('d MMM Y HH:mm') }}</span>
                    </div>
                @endif
            </div>

            @if($ticket->isTwoFactorRecoveryTicket())
                <div class="admin-card" style="margin-top: 24px;">
                    <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">{{ __('Account recovery (2FA)') }}</h2>

                    <p style="margin: 0 0 14px; color: var(--admin-text-muted); line-height: 1.6;">
                        {{ __('Disable 2FA for the user related to this ticket and notify them by email.') }}
                    </p>

                    <form method="POST" action="{{ route('admin.tickets.disableTwoFactor', $ticket) }}"
                          onsubmit="return confirm('{{ __('Disable 2FA for this account and send the notification email?') }}');">
                        @csrf
                        <textarea name="admin_response" rows="3" class="admin-textarea" placeholder="{{ __('Optional admin note...') }}" style="width:100%; margin-bottom: 12px;"></textarea>
                        <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">{{ __('Disable 2FA & notify') }}</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
