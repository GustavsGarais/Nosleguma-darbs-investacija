@extends('layouts.dashboard')

@section('title', __('Ticket #:id', ['id' => $ticket->id]))

@section('dashboard_content')
<section class="auth-card" aria-label="{{ __('Support Tickets') }}" style="padding:32px; display:flex; flex-direction:column; gap:24px;">
    <header style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px;">
        <div>
            <h1 style="margin:0;">{{ __('Ticket #:id', ['id' => $ticket->id]) }}</h1>
            <p style="margin:6px 0 0; color:var(--c-on-surface-2); line-height:1.5;">{{ $ticket->getDisplaySubject() }}</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">← {{ __('Back to My Tickets') }}</a>
    </header>

    @if(session('success'))
        <div role="status" style="padding:12px 16px; border-radius:10px; background:color-mix(in srgb, var(--c-primary) 18%, var(--c-surface)); border:1px solid color-mix(in srgb, var(--c-primary) 35%, var(--c-border));">
            {{ session('success') }}
        </div>
    @endif

    <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; background:color-mix(in srgb, var(--c-surface) 96%, var(--c-primary) 4%); display:flex; flex-direction:column; gap:16px;">
        <h2 style="margin:0; font-size:1.15rem;">{{ __('Ticket information') }}</h2>
        <div class="ticket-detail__meta" style="padding:0; border-bottom:none;">
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
    </article>

    <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; background:color-mix(in srgb, var(--c-surface) 96%, var(--c-primary) 4%); display:flex; flex-direction:column; gap:16px;">
        <h2 style="margin:0; font-size:1.15rem;">{{ __('Your Report') }}</h2>
        <div class="ticket-detail__prose">{{ $ticket->description }}</div>
    </article>

    @if($ticket->admin_response)
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; background:color-mix(in srgb, var(--c-surface) 96%, var(--c-primary) 4%); display:flex; flex-direction:column; gap:16px;">
            <h2 style="margin:0; font-size:1.15rem;">{{ __('Admin response') }}</h2>
            <div class="ticket-detail__prose ticket-detail__prose--response">{{ $ticket->admin_response }}</div>
        </article>
    @else
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; background:color-mix(in srgb, var(--c-surface) 96%, var(--c-primary) 4%);">
            <p style="margin:0; color:var(--c-on-surface-2); line-height:1.55;">{{ __('Your ticket is being reviewed. We will respond as soon as possible.') }}</p>
        </article>
    @endif
</section>
@endsection
