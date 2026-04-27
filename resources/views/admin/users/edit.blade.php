@extends('layouts.admin')

@section('title', __('Edit user'))

@section('content')
<div class="admin-header" style="display: flex; justify-content: space-between; align-items: start;">
    <div>
        <h1>{{ __('Edit user') }}</h1>
        <p>{{ $user->name }}</p>
    </div>
    <a href="{{ route('admin.users.show', $user) }}" class="admin-btn admin-btn-secondary">{{ __('Back to user') }}</a>
</div>

@if($errors->any())
    <div class="admin-alert admin-alert-error">
        <ul style="margin: 0; padding-left: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="admin-card">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PATCH')

        <div style="display: grid; gap: 20px;">
            <div>
                <label for="name" style="display: block; margin-bottom: 6px; font-weight: 500;">{{ __('Name') }}</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $user->name) }}" 
                    required
                    class="admin-input"
                >
            </div>

            <div>
                <p style="margin: 0 0 6px; font-weight: 500;">{{ __('Email') }}</p>
                <div class="admin-input" style="margin: 0; background: color-mix(in srgb, var(--admin-surface-light) 85%, var(--admin-border)); cursor: default;">
                    {{ $user->email }}
                </div>
                <p style="margin: 8px 0 0; font-size: 13px; color: var(--admin-text-muted);">{{ __('Administrators cannot change a user\'s email address.') }}</p>
            </div>

            <div>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input 
                        type="checkbox" 
                        name="is_admin" 
                        value="1" 
                        {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                        style="width: 18px; height: 18px; cursor: pointer;"
                    >
                    <span style="font-weight: 500;">{{ __('Admin user') }}</span>
                </label>
                <p style="margin: 8px 0 0; font-size: 13px; color: var(--admin-text-muted);">{{ __('Grant admin privileges to this user') }}</p>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button type="submit" class="admin-btn admin-btn-primary">{{ __('Update user') }}</button>
                <a href="{{ route('admin.users.show', $user) }}" class="admin-btn admin-btn-secondary">{{ __('Cancel') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection
