@extends('layouts.admin')

@section('title', __('Blocked Emails'))

@section('content')
<div class="admin-header" style="display:flex; justify-content:space-between; align-items:start; gap:16px; flex-wrap:wrap;">
    <div>
        <h1 style="margin:0 0 6px;">{{ __('Blocked Emails') }}</h1>
        <p style="margin:0; color: var(--admin-text-muted); max-width: 720px;">
            {{ __('Add exact emails or domains to prevent sign-up and sign-in. Example: block "spam@example.com" or domain "example.com".') }}
        </p>
    </div>
</div>

<div class="admin-card" style="margin-top: 16px;">
    <h2 style="margin: 0 0 14px; font-size: 18px; font-weight: 600;">{{ __('Add rule') }}</h2>
    <form method="POST" action="{{ route('admin.blocked-emails.store') }}" style="display:grid; gap:12px;">
        @csrf
        <div style="display:grid; grid-template-columns: 180px 1fr; gap:12px; align-items:end;">
            <div>
                <label style="display:block; margin-bottom:6px; font-weight: 500;">{{ __('Type') }}</label>
                <select name="match_type" class="admin-input">
                    <option value="email">{{ __('Exact email') }}</option>
                    <option value="domain">{{ __('Domain') }}</option>
                </select>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; font-weight: 500;">{{ __('Pattern') }}</label>
                <input name="pattern" value="{{ old('pattern') }}" required class="admin-input" placeholder="spam@example.com or example.com" />
            </div>
        </div>
        <div>
            <label style="display:block; margin-bottom:6px; font-weight: 500;">{{ __('Note (optional)') }}</label>
            <input name="note" value="{{ old('note') }}" class="admin-input" maxlength="255" />
        </div>
        <div>
            <button type="submit" class="admin-btn admin-btn-primary">{{ __('Add') }}</button>
        </div>
    </form>
</div>

<div class="admin-card" style="margin-top: 16px;">
    <h2 style="margin: 0 0 14px; font-size: 18px; font-weight: 600;">{{ __('Rules') }}</h2>

    @if($rules->count())
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Pattern') }}</th>
                        <th>{{ __('Note') }}</th>
                        <th>{{ __('Created') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rules as $rule)
                        <tr>
                            <td>{{ $rule->match_type }}</td>
                            <td style="font-weight:600;">{{ $rule->pattern }}</td>
                            <td style="color: var(--admin-text-muted); font-size: 13px;">{{ $rule->note }}</td>
                            <td style="color: var(--admin-text-muted); font-size: 13px;">{{ $rule->created_at?->format('Y-m-d') }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.blocked-emails.delete', $rule) }}" onsubmit="return confirm('{{ __('Remove this block rule?') }}');" style="margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-btn admin-btn-danger" style="padding: 6px 12px; font-size: 13px;">{{ __('Remove') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 16px;">
            {{ $rules->links() }}
        </div>
    @else
        <p style="margin:0; color: var(--admin-text-muted);">{{ __('No blocked email rules yet.') }}</p>
    @endif
</div>
@endsection

