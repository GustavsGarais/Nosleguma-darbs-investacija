@extends('layouts.admin')

@section('title', 'Support Tickets')

@section('content')
<div class="admin-header">
    <h1>Support Tickets</h1>
    <p>Manage and respond to user support requests</p>
</div>

<div class="admin-card">
    <form method="GET" action="{{ route('admin.tickets.index') }}" style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
        <input 
            type="text" 
            name="search" 
            placeholder="Search tickets..." 
            value="{{ request('search') }}"
            class="admin-input"
            style="flex: 1; min-width: 200px;"
        >
        <select name="status" class="admin-select" style="width: 180px;">
            <option value="">All Statuses</option>
            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Open</option>
            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
        </select>
        <select name="priority" class="admin-select" style="width: 180px;">
            <option value="">All Priorities</option>
            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>High</option>
            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Low</option>
        </select>
        <button type="submit" class="admin-btn admin-btn-primary">Search</button>
        @if(request('search') || request('status') || request('priority'))
            <a href="{{ route('admin.tickets.index') }}" class="admin-btn admin-btn-secondary">Clear</a>
        @endif
    </form>

    <div class="admin-stats-grid" style="margin-bottom: 24px;">
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total</div>
            <div class="admin-stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Open</div>
            <div class="admin-stat-value" style="color: var(--admin-primary);">{{ $stats['open'] }}</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">In Progress</div>
            <div class="admin-stat-value" style="color: var(--admin-warning);">{{ $stats['in_progress'] }}</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Resolved</div>
            <div class="admin-stat-value" style="color: var(--admin-success);">{{ $stats['resolved'] }}</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Urgent</div>
            <div class="admin-stat-value" style="color: var(--admin-danger);">{{ $stats['urgent'] }}</div>
        </div>
    </div>

    @if($tickets->count())
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr>
                            <td>#{{ $ticket->id }}</td>
                            <td>
                                <a href="{{ route('admin.tickets.show', $ticket) }}" style="color: var(--admin-text); text-decoration: none; font-weight: 500;">
                                    {{ Str::limit($ticket->subject, 50) }}
                                </a>
                            </td>
                            <td>
                                @if($ticket->user)
                                    <a href="{{ route('admin.users.show', $ticket->user) }}" style="color: var(--admin-primary); text-decoration: none;">
                                        {{ $ticket->user->name }}
                                    </a>
                                @else
                                    <span style="color: var(--admin-text-muted);">Anonymous</span>
                                @endif
                            </td>
                            <td style="color: var(--admin-text-muted); font-size: 13px;">
                                {{ $ticket->getErrorTypeLabel() }}
                            </td>
                            <td>
                                <span class="admin-badge" style="background: {{ $ticket->getPriorityColor() }}20; color: {{ $ticket->getPriorityColor() }};">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge" style="background: {{ $ticket->getStatusColor() }}20; color: {{ $ticket->getStatusColor() }};">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td>
                                @if($ticket->assignedAdmin)
                                    {{ $ticket->assignedAdmin->name }}
                                @else
                                    <span style="color: var(--admin-text-muted);">Unassigned</span>
                                @endif
                            </td>
                            <td style="color: var(--admin-text-muted); font-size: 13px;">
                                {{ $ticket->created_at->format('M d, Y') }}
                            </td>
                            <td>
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="admin-btn admin-btn-secondary" style="padding: 6px 12px; font-size: 13px;">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $tickets->links() }}
        </div>
    @else
        <p style="color: var(--admin-text-muted); text-align: center; padding: 40px;">No tickets found.</p>
    @endif
</div>
@endsection

