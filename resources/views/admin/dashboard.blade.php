@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="admin-header">
    <h1>Dashboard</h1>
    <p>Overview of system statistics and recent activity</p>
</div>

<div class="admin-stats-grid">
    <div class="admin-stat-card">
        <div class="admin-stat-label">Total Users</div>
        <div class="admin-stat-value">{{ $totalUsers }}</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label">Total Tickets</div>
        <div class="admin-stat-value">{{ $totalTickets }}</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label">Open Tickets</div>
        <div class="admin-stat-value" style="color: var(--admin-primary);">{{ $openTickets }}</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label">In Progress</div>
        <div class="admin-stat-value" style="color: var(--admin-warning);">{{ $inProgressTickets }}</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label">Urgent Tickets</div>
        <div class="admin-stat-value" style="color: var(--admin-danger);">{{ $urgentTickets }}</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label">Unassigned</div>
        <div class="admin-stat-value" style="color: var(--admin-warning);">{{ $unassignedTickets }}</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <div class="admin-card">
        <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">Recent Users</h2>
        @if($latestUsers->count())
            <div style="display: grid; gap: 12px;">
                @foreach($latestUsers as $user)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: var(--admin-surface-light); border-radius: 8px;">
                        <div>
                            <a href="{{ route('admin.users.show', $user) }}" style="color: var(--admin-text); text-decoration: none; font-weight: 500;">{{ $user->name }}</a>
                            <p style="margin: 4px 0 0; font-size: 12px; color: var(--admin-text-muted);">{{ $user->email }}</p>
                        </div>
                        <span style="font-size: 12px; color: var(--admin-text-muted);">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
            <div style="margin-top: 16px;">
                <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-secondary">View All Users</a>
            </div>
        @else
            <p style="color: var(--admin-text-muted);">No users yet.</p>
        @endif
    </div>

    <div class="admin-card">
        <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">Recent Tickets</h2>
        @if($latestTickets->count())
            <div style="display: grid; gap: 12px;">
                @foreach($latestTickets as $ticket)
                    <div style="padding: 12px; background: var(--admin-surface-light); border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                            <a href="{{ route('admin.tickets.show', $ticket) }}" style="color: var(--admin-text); text-decoration: none; font-weight: 500; flex: 1;">{{ $ticket->subject }}</a>
                            <span class="admin-badge" style="background: {{ $ticket->getStatusColor() }}20; color: {{ $ticket->getStatusColor() }};">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </div>
                        <p style="margin: 0; font-size: 12px; color: var(--admin-text-muted);">
                            {{ $ticket->user->name ?? 'Anonymous' }} • {{ $ticket->created_at->diffForHumans() }}
                        </p>
                    </div>
                @endforeach
            </div>
            <div style="margin-top: 16px;">
                <a href="{{ route('admin.tickets.index') }}" class="admin-btn admin-btn-secondary">View All Tickets</a>
            </div>
        @else
            <p style="color: var(--admin-text-muted);">No tickets yet.</p>
        @endif
    </div>
</div>
@endsection
