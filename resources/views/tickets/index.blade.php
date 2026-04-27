@extends('layouts.dashboard')

@section('title', __('My Support Tickets'))

@section('dashboard_content')
<section class="auth-card" aria-label="{{ __('My Support Tickets') }}">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <h1 style="margin:0;">{{ __('My Support Tickets') }}</h1>
        <a href="{{ route('support.index') }}" class="btn btn-primary">{{ __('Report New Issue') }}</a>
    </div>

    @if($tickets->count())
        <div style="overflow:auto; margin-top:16px;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">{{ __('ID') }}</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">{{ __('Title') }}</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">{{ __('Type') }}</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">{{ __('Status') }}</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">{{ __('Submitted') }}</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tickets as $ticket)
                        <tr style="border-bottom:1px solid var(--c-border);">
                            <td style="padding:12px 8px;">#{{ $ticket->id }}</td>
                            <td style="padding:12px 8px;">
                                <a href="{{ route('tickets.show', $ticket) }}" style="font-weight:600; color:var(--c-on-surface); text-decoration:none;">{{ Str::limit($ticket->getDisplaySubject(), 50) }}</a>
                            </td>
                            <td style="padding:12px 8px; color:var(--c-on-surface-2);">{{ $ticket->getErrorTypeLabel() }}</td>
                            <td style="padding:12px 8px;">
                                <span style="padding:4px 12px; background:{{ $ticket->getStatusColor() }}20; color:{{ $ticket->getStatusColor() }}; border-radius:6px; font-size:12px; font-weight:600;">
                                    {{ $ticket->getStatusLabel() }}
                                </span>
                            </td>
                            <td style="padding:12px 8px; color:var(--c-on-surface-2); font-size:13px;">
                                {{ $ticket->created_at->translatedFormat('d MMM Y') }}
                            </td>
                            <td style="padding:12px 8px;">
                                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-secondary btn-sm">{{ __('View') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:16px;">
            {{ $tickets->links() }}
        </div>
    @else
        <div style="margin-top:24px; text-align:center; padding:40px;">
            <p style="margin:0 0 16px; color:var(--c-on-surface-2);">{{ __("You haven't submitted any support tickets yet.") }}</p>
            <a href="{{ route('support.index') }}" class="btn btn-primary">{{ __('Report Your First Issue') }}</a>
        </div>
    @endif
</section>
@endsection
