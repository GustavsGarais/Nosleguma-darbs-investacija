@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="admin-header" style="display: flex; justify-content: space-between; align-items: start;">
    <div>
        <h1>Edit User</h1>
        <p>{{ $user->name }}</p>
    </div>
    <a href="{{ route('admin.users.show', $user) }}" class="admin-btn admin-btn-secondary">Back to User</a>
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
                <label for="name" style="display: block; margin-bottom: 6px; font-weight: 500;">Name</label>
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
                <label for="email" style="display: block; margin-bottom: 6px; font-weight: 500;">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email', $user->email) }}" 
                    required
                    class="admin-input"
                >
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
                    <span style="font-weight: 500;">Admin User</span>
                </label>
                <p style="margin: 8px 0 0; font-size: 13px; color: var(--admin-text-muted);">Grant admin privileges to this user</p>
            </div>

            <div>
                <label for="password" style="display: block; margin-bottom: 6px; font-weight: 500;">New Password (optional)</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="admin-input"
                >
                <p style="margin: 8px 0 0; font-size: 13px; color: var(--admin-text-muted);">Leave blank to keep current password</p>
            </div>

            <div>
                <label for="password_confirmation" style="display: block; margin-bottom: 6px; font-weight: 500;">Confirm New Password</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="admin-input"
                >
            </div>

            <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button type="submit" class="admin-btn admin-btn-primary">Update User</button>
                <a href="{{ route('admin.users.show', $user) }}" class="admin-btn admin-btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
