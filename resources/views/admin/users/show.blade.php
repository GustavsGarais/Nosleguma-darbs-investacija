@extends('layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="admin-header" style="display: flex; justify-content: space-between; align-items: start;">
    <div>
        <h1>User Details</h1>
        <p>{{ $user->name }}</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-secondary">Back to Users</a>
        <a href="{{ route('admin.users.edit', $user) }}" class="admin-btn admin-btn-primary">Edit User</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px;">
    <div class="admin-card">
        <h2 style="margin: 0 0 16px; font-size: 18px; font-weight: 600;">User Information</h2>
        <div style="display: grid; gap: 12px;">
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Name</p>
                <p style="margin: 0; font-weight: 600;">{{ $user->name }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Email</p>
                <p style="margin: 0; font-weight: 600;">{{ $user->email }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Role</p>
                <p style="margin: 0;">
                    @if($user->is_admin)
                        <span class="admin-badge" style="background: var(--admin-primary)20; color: var(--admin-primary);">Admin</span>
                    @else
                        <span class="admin-badge" style="background: var(--admin-border); color: var(--admin-text-muted);">User</span>
                    @endif
                </p>
            </div>
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Member Since</p>
                <p style="margin: 0; font-weight: 600;">{{ $user->created_at->format('F d, Y') }}</p>
                <p style="margin: 4px 0 0; font-size: 12px; color: var(--admin-text-muted);">{{ $user->created_at->diffForHumans() }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Tutorial Completed</p>
                <p style="margin: 0;">
                    @if($user->tutorial_completed)
                        <span style="color: var(--admin-success); font-weight: 600;">Yes</span>
                    @else
                        <span style="color: var(--admin-text-muted);">No</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2 style="margin: 0 0 16px; font-size: 18px; font-weight: 600;">Statistics</h2>
        <div style="display: grid; gap: 12px;">
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Total Simulations</p>
                <p style="margin: 0; font-size: 24px; font-weight: 700;">{{ $user->simulations->count() }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Support Tickets</p>
                <p style="margin: 0; font-size: 24px; font-weight: 700;">{{ $tickets->total() }}</p>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">User Support Tickets</h2>
    
    @if($tickets->count())
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Priority</th>
                        <th>Status</th>
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
                                <span class="admin-badge" style="background: {{ $ticket->getPriorityColor() }}20; color: {{ $ticket->getPriorityColor() }};">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge" style="background: {{ $ticket->getStatusColor() }}20; color: {{ $ticket->getStatusColor() }};">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
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

        <div style="margin-top: 16px;">
            {{ $tickets->links() }}
        </div>
    @else
        <p style="margin: 0; color: var(--admin-text-muted);">This user has no support tickets yet.</p>
    @endif
</div>
@endsection
