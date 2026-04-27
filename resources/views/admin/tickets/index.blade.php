@extends('layouts.admin')

@section('title', __('Support Tickets'))

@section('content')
<div class="admin-header">
    <h1>{{ __('Support Tickets') }}</h1>
    <p>{{ __('Manage and respond to user support requests') }}</p>
</div>

<div class="admin-card">
    <form method="GET" action="{{ route('admin.tickets.index') }}" style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
        <input 
            type="text" 
            name="search" 
            placeholder="{{ __('Search tickets...') }}" 
            value="{{ request('search') }}"
            class="admin-input"
            style="flex: 1; min-width: 200px;"
        >
        <select name="status" class="admin-select" style="width: 180px;">
            <option value="">{{ __('All statuses') }}</option>
            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>{{ __('ticket.status.open') }}</option>
            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>{{ __('ticket.status.in_progress') }}</option>
            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>{{ __('ticket.status.resolved') }}</option>
            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>{{ __('ticket.status.closed') }}</option>
        </select>
        <select name="priority" class="admin-select" style="width: 180px;">
            <option value="">{{ __('All priorities') }}</option>
            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>{{ __('ticket.priority.urgent') }}</option>
            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>{{ __('ticket.priority.high') }}</option>
            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>{{ __('ticket.priority.medium') }}</option>
            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>{{ __('ticket.priority.low') }}</option>
        </select>
        <button type="submit" class="admin-btn admin-btn-primary">{{ __('Search') }}</button>
        @if(request('search') || request('status') || request('priority'))
            <a href="{{ route('admin.tickets.index') }}" class="admin-btn admin-btn-secondary">{{ __('Clear') }}</a>
        @endif
    </form>

    <div class="admin-stats-grid admin-stats-grid--flex" style="margin-bottom: 24px;">
        <div class="admin-stat-card">
            <div class="admin-stat-label">{{ __('Total') }}</div>
            <div class="admin-stat-value">{{ $stats['total'] }}</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">{{ __('ticket.status.open') }}</div>
            <div class="admin-stat-value" style="color: var(--admin-primary);">{{ $stats['open'] }}</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">{{ __('ticket.status.in_progress') }}</div>
            <div class="admin-stat-value" style="color: var(--admin-warning);">{{ $stats['in_progress'] }}</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">{{ __('ticket.status.resolved') }}</div>
            <div class="admin-stat-value" style="color: var(--admin-success);">{{ $stats['resolved'] }}</div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">{{ __('ticket.priority.urgent') }}</div>
            <div class="admin-stat-value" style="color: var(--admin-danger);">{{ $stats['urgent'] }}</div>
        </div>
    </div>

    @if($tickets->count())
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('User') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Assigned to') }}</th>
                        <th>{{ __('Created') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr>
                            <td>#{{ $ticket->id }}</td>
                            <td>
                                <a href="{{ route('admin.tickets.show', $ticket) }}" style="color: var(--admin-text); text-decoration: none; font-weight: 500;">
                                    {{ Str::limit($ticket->getDisplaySubject(), 50) }}
                                </a>
                            </td>
                            <td>
                                @if($ticket->user)
                                    <a href="{{ route('admin.users.show', $ticket->user) }}" style="color: var(--admin-primary); text-decoration: none;">
                                        {{ $ticket->user->name }}
                                    </a>
                                @else
                                    <span style="color: var(--admin-text-muted);">{{ __('Anonymous') }}</span>
                                @endif
                            </td>
                            <td style="color: var(--admin-text-muted); font-size: 13px;">
                                {{ $ticket->getErrorTypeLabel() }}
                            </td>
                            <td>
                                <span class="admin-badge" style="background: {{ $ticket->getPriorityColor() }}20; color: {{ $ticket->getPriorityColor() }};">
                                    {{ $ticket->getPriorityLabel() }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge" style="background: {{ $ticket->getStatusColor() }}20; color: {{ $ticket->getStatusColor() }};">
                                    {{ $ticket->getStatusLabel() }}
                                </span>
                            </td>
                            <td>
                                @if($ticket->assignedAdmin)
                                    {{ $ticket->assignedAdmin->name }}
                                @else
                                    <span style="color: var(--admin-text-muted);">{{ __('Unassigned') }}</span>
                                @endif
                            </td>
                            <td style="color: var(--admin-text-muted); font-size: 13px;">
                                {{ $ticket->created_at->translatedFormat('d MMM Y') }}
                            </td>
                            <td>
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="admin-btn admin-btn-secondary" style="padding: 6px 12px; font-size: 13px;">{{ __('View') }}</a>
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
        <p style="color: var(--admin-text-muted); text-align: center; padding: 40px;">{{ __('No tickets found.') }}</p>
    @endif
</div>
@endsection
