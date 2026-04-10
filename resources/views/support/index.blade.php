@extends('layouts.app')

@section('title', __('Support'))

@section('content')
<div class="auth-page" style="max-width:720px;">
    @if (session('password_reset_reported'))
        <div role="status" aria-live="polite" style="margin:0 0 16px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
            {{ __('Password reset unauthorized acknowledged') }}
        </div>
    @endif

    <header class="auth-card" aria-label="{{ __('Support') }}">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
            <div>
                <h1 style="margin:0 0 8px;">{{ __('Support') }}</h1>
                <p style="margin:0; color:var(--c-on-surface-2); font-size:14px; line-height:1.6;">
                    {{ __('Support hub intro') }}
                </p>
            </div>
            <a href="{{ url('/') }}" class="btn btn-outline">{{ __('Back') }}</a>
        </div>
    </header>

    @auth
        @include('support.partials.ticket-form')
    @else
        <section class="auth-card" style="margin-top:24px;" aria-label="{{ __('Sign in to report') }}">
            <h2 style="margin:0 0 8px; font-size:1.25rem;">{{ __('Report an Issue') }}</h2>
            <p style="margin:0 0 16px; color:var(--c-on-surface-2); font-size:14px;">
                {{ __('Support sign in to report hint') }}
            </p>
            <a href="{{ route('login', ['redirect' => route('support.index')]) }}" class="btn btn-primary">{{ __('Log In') }}</a>
        </section>
    @endauth

    @include('support.partials.password-reset-cta')

    @include('support.partials.recovery-form')
</div>
@endsection
