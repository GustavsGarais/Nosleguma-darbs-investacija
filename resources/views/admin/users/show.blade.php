@extends('layouts.admin')

@section('title', __('User details'))

@section('content')
<div class="admin-header" style="display: flex; justify-content: space-between; align-items: start;">
    <div>
        <h1>{{ __('User details') }}</h1>
        <p>{{ $user->name }}</p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-secondary">{{ __('Back to users') }}</a>
        <a href="{{ route('admin.users.edit', $user) }}" class="admin-btn admin-btn-primary">{{ __('Edit user') }}</a>
    </div>
</div>

<div class="admin-user-detail-grid">
    <div class="admin-card">
        <h2 style="margin: 0 0 16px; font-size: 18px; font-weight: 600;">{{ __('User information & security') }}</h2>
        <div style="display: grid; gap: 16px;">
            <div style="display: grid; gap: 12px;">
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">{{ __('Name') }}</p>
                    <p style="margin: 0; font-weight: 600;">{{ $user->name }}</p>
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">{{ __('Email') }}</p>
                    <p style="margin: 0; font-weight: 600;">{{ $user->email }}</p>
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">{{ __('Role') }}</p>
                    <p style="margin: 0;">
                        @if($user->is_admin)
                            <span class="admin-badge" style="background: var(--admin-primary)20; color: var(--admin-primary);">{{ __('Admin') }}</span>
                        @else
                            <span class="admin-badge" style="background: var(--admin-border); color: var(--admin-text-muted);">{{ __('User') }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">{{ __('Member since') }}</p>
                    <p style="margin: 0; font-weight: 600;">{{ $user->created_at->translatedFormat('d MMMM Y') }}</p>
                    <p style="margin: 4px 0 0; font-size: 12px; color: var(--admin-text-muted);">{{ $user->created_at->diffForHumans() }}</p>
                </div>
            </div>

            <div style="border-top: 1px solid var(--admin-border); padding-top: 16px;">
                <h3 style="margin: 0 0 12px; font-size: 15px; font-weight: 600;">{{ __('Two-Factor Authentication') }}</h3>
                <div style="display: grid; gap: 12px;">
                    <div>
                        <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">{{ __('Status') }}</p>
                        @if($user->hasTwoFactorEnabled())
                            <span class="admin-badge" style="background: #10b98120; color: #10b981;">{{ __('Enabled') }}</span>
                        @else
                            <span class="admin-badge" style="background: var(--admin-border); color: var(--admin-text-muted);">{{ __('Disabled') }}</span>
                        @endif
                    </div>

                    @if($user->hasTwoFactorEnabled() && $user->id !== auth()->id())
                        <div>
                            <p style="margin: 0 0 8px; font-size: 13px; color: var(--admin-text-muted); line-height:1.5;">
                                {{ __('Disabling 2FA clears the user\'s secret so they can sign in again without an authenticator code.') }}
                            </p>
                            <form method="POST" action="{{ route('admin.users.disableTwoFactor', $user) }}" onsubmit="return confirm('{{ __('Disable 2FA for this user?') }}');">
                                @csrf
                                <button type="submit" class="admin-btn admin-btn-danger" style="width:100%;">{{ __('Disable 2FA') }}</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2 style="margin: 0 0 16px; font-size: 18px; font-weight: 600;">{{ __('Statistics') }}</h2>
        <div style="display: grid; gap: 12px;">
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">{{ __('Total simulations') }}</p>
                <p style="margin: 0; font-size: 24px; font-weight: 700;">{{ $user->simulations->count() }}</p>
            </div>
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">{{ __('Support tickets') }}</p>
                <p style="margin: 0; font-size: 24px; font-weight: 700;">{{ $tickets->total() }}</p>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">{{ __('User support tickets') }}</h2>
    
    @if($tickets->count())
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Subject') }}</th>
                        <th>{{ __('Priority') }}</th>
                        <th>{{ __('Status') }}</th>
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
                                <span class="admin-badge" style="background: {{ $ticket->getPriorityColor() }}20; color: {{ $ticket->getPriorityColor() }};">
                                    {{ $ticket->getPriorityLabel() }}
                                </span>
                            </td>
                            <td>
                                <span class="admin-badge" style="background: {{ $ticket->getStatusColor() }}20; color: {{ $ticket->getStatusColor() }};">
                                    {{ $ticket->getStatusLabel() }}
                                </span>
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

        <div style="margin-top: 16px;">
            {{ $tickets->links() }}
        </div>
    @else
        <p style="margin: 0; color: var(--admin-text-muted);">{{ __('This user has no support tickets yet.') }}</p>
    @endif
</div>
@endsection
