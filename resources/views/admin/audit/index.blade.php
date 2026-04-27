@extends('layouts.admin')

@section('title', __('Audit log'))

@section('content')
<div class="admin-header" style="display:flex; justify-content:space-between; align-items:start; gap:16px; flex-wrap:wrap;">
    <div>
        <h1 style="margin:0 0 6px;">{{ __('Audit log') }}</h1>
        <p style="margin:0; color: var(--admin-text-muted); max-width: 720px;">
            {{ __('Recent security-related actions performed in the admin panel.') }}
        </p>
    </div>
</div>

<div class="admin-card" style="margin-top: 16px;">
    @if($logs->count())
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('When') }}</th>
                        <th>{{ __('Admin') }}</th>
                        <th>{{ __('Action') }}</th>
                        <th>{{ __('Details') }}</th>
                        <th>{{ __('IP') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td style="color: var(--admin-text-muted); font-size: 13px; white-space:nowrap;">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                            <td style="font-size: 13px;">
                                @if($log->admin)
                                    <div style="font-weight:600;">{{ $log->admin->name }}</div>
                                    <div style="color: var(--admin-text-muted);">{{ $log->admin->email }}</div>
                                @else
                                    —
                                @endif
                            </td>
                            <td style="font-weight:600; font-size: 13px;">{{ $log->action }}</td>
                            <td style="font-size: 12px; color: var(--admin-text-muted); max-width: 420px;">
                                @if(!empty($log->meta))
                                    <pre style="margin:0; white-space:pre-wrap; word-break:break-word; font-family:ui-monospace,monospace;">{{ json_encode($log->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    —
                                @endif
                            </td>
                            <td style="font-size: 12px; color: var(--admin-text-muted); white-space:nowrap;">{{ $log->ip_address ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 16px;">
            {{ $logs->links() }}
        </div>
    @else
        <p style="margin:0; color: var(--admin-text-muted);">{{ __('No audit entries yet.') }}</p>
    @endif
</div>
@endsection
