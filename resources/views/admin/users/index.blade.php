@extends('layouts.admin')

@section('title', __('Users management'))

@section('content')
<div class="admin-header">
    <h1>{{ __('Users management') }}</h1>
    <p>{{ __('Manage user accounts and permissions') }}</p>
</div>

<div class="admin-card">
    <form method="GET" action="{{ route('admin.users.index') }}" style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
        <input 
            type="text" 
            name="search" 
            placeholder="{{ __('Search by name or email...') }}" 
            value="{{ request('search') }}"
            class="admin-input"
            style="flex: 1; min-width: 200px;"
        >
        <select name="filter" class="admin-select" style="width: 180px;">
            <option value="">{{ __('All users') }}</option>
            <option value="admins" {{ request('filter') === 'admins' ? 'selected' : '' }}>{{ __('Admins only') }}</option>
            <option value="users" {{ request('filter') === 'users' ? 'selected' : '' }}>{{ __('Regular users') }}</option>
        </select>
        <button type="submit" class="admin-btn admin-btn-primary">{{ __('Search') }}</button>
        @if(request('search') || request('filter'))
            <a href="{{ route('admin.users.index') }}" class="admin-btn admin-btn-secondary">{{ __('Clear') }}</a>
        @endif
    </form>

    @if($users->count())
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Simulations') }}</th>
                        <th>{{ __('Joined') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" style="color: var(--admin-text); text-decoration: none; font-weight: 500;">{{ $user->name }}</a>
                            </td>
                            <td style="color: var(--admin-text-muted);">{{ $user->email }}</td>
                            <td>
                                @if($user->is_admin)
                                    <span class="admin-badge" style="background: var(--admin-primary)20; color: var(--admin-primary);">{{ __('Admin') }}</span>
                                @else
                                    <span class="admin-badge" style="background: var(--admin-border); color: var(--admin-text-muted);">{{ __('User') }}</span>
                                @endif
                            </td>
                            <td style="color: var(--admin-text-muted);">{{ $user->simulations_count }}</td>
                            <td style="color: var(--admin-text-muted); font-size: 13px;">
                                {{ $user->created_at->translatedFormat('d MMM Y') }}
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="{{ route('admin.users.show', $user) }}" class="admin-btn admin-btn-secondary" style="padding: 6px 12px; font-size: 13px;">{{ __('View') }}</a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="admin-btn admin-btn-secondary" style="padding: 6px 12px; font-size: 13px;">{{ __('Edit') }}</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            {{ $users->links() }}
        </div>
    @else
        <p style="color: var(--admin-text-muted); text-align: center; padding: 40px;">{{ __('No users found.') }}</p>
    @endif
</div>
@endsection
