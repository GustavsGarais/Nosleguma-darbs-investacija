@extends('layouts.admin')

@section('title', 'Ticket #' . $ticket->id)

@section('content')
<div class="admin-header" style="display: flex; justify-content: space-between; align-items: start;">
    <div>
        <h1>Ticket #{{ $ticket->id }}</h1>
        <p>{{ $ticket->subject }}</p>
    </div>
    <a href="{{ route('admin.tickets.index') }}" class="admin-btn admin-btn-secondary">Back to Tickets</a>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <div>
        <div class="admin-card">
            <h2 style="margin: 0 0 16px; font-size: 18px; font-weight: 600;">Ticket Details</h2>
            <div style="margin-bottom: 20px;">
                <p style="margin: 0 0 8px; font-size: 13px; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Subject</p>
                <p style="margin: 0; font-size: 16px; font-weight: 500;">{{ $ticket->subject }}</p>
            </div>
            <div style="margin-bottom: 20px;">
                <p style="margin: 0 0 8px; font-size: 13px; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Description</p>
                <div style="padding: 16px; background: var(--admin-surface-light); border-radius: 8px; white-space: pre-wrap; line-height: 1.6;">
                    {{ $ticket->description }}
                </div>
            </div>
            @if($ticket->admin_response)
                <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--admin-border);">
                    <p style="margin: 0 0 8px; font-size: 13px; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Admin Response</p>
                    <div style="padding: 16px; background: rgba(59, 130, 246, 0.1); border-left: 3px solid var(--admin-primary); border-radius: 8px; white-space: pre-wrap; line-height: 1.6;">
                        {{ $ticket->admin_response }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div>
        <div class="admin-card">
            <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">Update Ticket</h2>
            <form method="POST" action="{{ route('admin.tickets.updateStatus', $ticket) }}">
                @csrf
                @method('PATCH')

                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Status</label>
                    <select name="status" class="admin-select" required>
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Priority</label>
                    <select name="priority" class="admin-select" required>
                        <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Assign To</label>
                    <select name="assigned_to" class="admin-select">
                        <option value="">Unassigned</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ $ticket->assigned_to === $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Admin Response</label>
                    <textarea name="admin_response" rows="6" class="admin-textarea" placeholder="Add your response here...">{{ old('admin_response', $ticket->admin_response) }}</textarea>
                </div>

                <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">Update Ticket</button>
            </form>
        </div>

        <div class="admin-card" style="margin-top: 24px;">
            <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">Ticket Information</h2>
            <div style="display: grid; gap: 12px;">
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">User</p>
                    @if($ticket->user)
                        <a href="{{ route('admin.users.show', $ticket->user) }}" style="color: var(--admin-primary); text-decoration: none; font-weight: 500;">
                            {{ $ticket->user->name }}
                        </a>
                        <p style="margin: 4px 0 0; font-size: 12px; color: var(--admin-text-muted);">{{ $ticket->user->email }}</p>
                    @else
                        <span style="color: var(--admin-text-muted);">Anonymous</span>
                    @endif
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Error Type</p>
                    <p style="margin: 0; font-weight: 500;">{{ $ticket->getErrorTypeLabel() }}</p>
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Status</p>
                    <span class="admin-badge" style="background: {{ $ticket->getStatusColor() }}20; color: {{ $ticket->getStatusColor() }};">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                    </span>
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Priority</p>
                    <span class="admin-badge" style="background: {{ $ticket->getPriorityColor() }}20; color: {{ $ticket->getPriorityColor() }};">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Assigned To</p>
                    @if($ticket->assignedAdmin)
                        <span style="font-weight: 500;">{{ $ticket->assignedAdmin->name }}</span>
                    @else
                        <span style="color: var(--admin-text-muted);">Unassigned</span>
                    @endif
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Created</p>
                    <span style="font-size: 13px;">{{ $ticket->created_at->format('M d, Y H:i') }}</span>
                </div>
                @if($ticket->resolved_at)
                    <div>
                        <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Resolved</p>
                        <span style="font-size: 13px;">{{ $ticket->resolved_at->format('M d, Y H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

