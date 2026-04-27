@extends('layouts.dashboard')

@section('title', __('Ticket #:id', ['id' => $ticket->id]))

@section('dashboard_content')
<section class="auth-card" aria-label="{{ __('Support Tickets') }}">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 style="margin:0;">{{ __('Ticket #:id', ['id' => $ticket->id]) }}</h1>
            <p style="margin:4px 0 0; color:var(--c-on-surface-2);">{{ $ticket->getDisplaySubject() }}</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-outline">{{ __('Back to My Tickets') }}</a>
    </div>

    @if(session('success'))
        <div role="status" style="margin-top:12px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
            {{ session('success') }}
        </div>
    @endif

    <div style="margin-top:24px; display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:16px;">
        <div class="auth-card">
            <h2 style="margin:0 0 16px; font-size:18px;">{{ __('Ticket information') }}</h2>
            <div style="display:grid; gap:12px;">
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Status') }}</p>
                    <span style="padding:4px 12px; background:{{ $ticket->getStatusColor() }}20; color:{{ $ticket->getStatusColor() }}; border-radius:6px; font-size:12px; font-weight:600;">
                        {{ $ticket->getStatusLabel() }}
                    </span>
                </div>
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Error Type') }}</p>
                    <p style="margin:0; font-weight:600;">{{ $ticket->getErrorTypeLabel() }}</p>
                </div>
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Priority') }}</p>
                    <span style="padding:4px 12px; background:{{ $ticket->getPriorityColor() }}20; color:{{ $ticket->getPriorityColor() }}; border-radius:6px; font-size:12px; font-weight:600;">
                        {{ $ticket->getPriorityLabel() }}
                    </span>
                </div>
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Submitted') }}</p>
                    <p style="margin:0; font-weight:600;">{{ $ticket->created_at->translatedFormat('d MMMM Y') }}</p>
                    <p style="margin:4px 0 0; font-size:12px; color:var(--c-on-surface-2);">{{ $ticket->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-card" style="margin-top:24px;">
        <h2 style="margin:0 0 16px; font-size:18px;">{{ __('Your Report') }}</h2>
        <div style="padding:16px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-primary) 5%); border-radius:8px; white-space:pre-wrap; line-height:1.6;">
            {{ $ticket->description }}
        </div>
    </div>

    @if($ticket->admin_response)
        <div class="auth-card" style="margin-top:24px;">
            <h2 style="margin:0 0 16px; font-size:18px;">{{ __('Admin response') }}</h2>
            <div style="padding:16px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-primary) 5%); border-left:3px solid var(--c-primary); border-radius:8px; white-space:pre-wrap; line-height:1.6;">
                {{ $ticket->admin_response }}
            </div>
        </div>
    @else
        <div class="auth-card" style="margin-top:24px;">
            <p style="margin:0; color:var(--c-on-surface-2);">{{ __('Your ticket is being reviewed. We will respond as soon as possible.') }}</p>
        </div>
    @endif
</section>
@endsection
