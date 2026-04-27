@extends('layouts.app')

@section('title', __('Support request received'))

@section('content')
    <section class="auth-card" aria-label="{{ __('Support request received') }}">
        <h1 style="margin:0 0 12px;">{{ __('Support request received') }}</h1>

        @if(session('success'))
            <div role="status" style="padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
                {{ session('success') }}
            </div>
        @else
            <div role="status" style="padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
                {{ __('Your request was submitted successfully.') }}
            </div>
        @endif

        <p style="margin:16px 0 0; color:var(--c-on-surface-2); line-height:1.6;">
            {{ __('You can continue using the site. An admin will review your request and email you when it is resolved.') }}
        </p>

        <div style="margin-top:16px; display:flex; gap:12px; flex-wrap:wrap;">
            <a href="{{ url('/') }}" class="btn btn-outline">{{ __('Back to home') }}</a>
            @if(auth()->check())
                <a href="{{ route('tickets.index') }}" class="btn btn-primary">{{ __('View my tickets') }}</a>
            @endif
        </div>
    </section>
@endsection
