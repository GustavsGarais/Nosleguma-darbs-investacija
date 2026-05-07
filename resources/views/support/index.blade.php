@extends('layouts.dashboard')

@section('title', __('Support'))

@section('dashboard_content')
<section class="auth-card" aria-label="{{ __('Support') }}" style="padding:32px; display:flex; flex-direction:column; gap:24px;">
    <header style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px;">
        <div>
            <h1 style="margin:0;">{{ __('Support') }}</h1>
            <p style="margin:6px 0 0; color:var(--c-on-surface-2); max-width:62ch; line-height:1.55;">{{ __('Support hub intro') }}</p>
        </div>
        <a class="btn btn-secondary" href="{{ auth()->check() ? route('simulations.index') : url('/') }}">← {{ __('Back') }}</a>
    </header>

    @if (session('password_reset_reported'))
        <div role="status" aria-live="polite" style="padding:12px 16px; border-radius:10px; background:color-mix(in srgb, var(--c-primary) 18%, var(--c-surface)); border:1px solid color-mix(in srgb, var(--c-primary) 35%, var(--c-border));">
            {{ __('Password reset unauthorized acknowledged') }}
        </div>
    @endif

    @guest
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; background:color-mix(in srgb, var(--c-surface) 96%, var(--c-primary) 4%); display:flex; flex-direction:column; gap:16px;">
            <h2 style="margin:0; font-size:1.15rem;">{{ __('Forgot password?') }}</h2>
            <p style="margin:0; color:var(--c-on-surface-2); line-height:1.55;">{{ __('Password reset from support intro') }}</p>
            @include('support.partials.password-reset-cta')
        </article>
    @endguest

    @auth
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; background:color-mix(in srgb, var(--c-surface) 96%, var(--c-primary) 4%); display:flex; flex-direction:column; gap:16px;">
            <h2 style="margin:0; font-size:1.15rem;">{{ __('Report an Issue') }}</h2>
            <p style="margin:0; color:var(--c-on-surface-2); line-height:1.55;">{{ __('Submit a detailed report while signed in. We will link it to your account.') }}</p>
            @include('support.partials.ticket-form')
        </article>
    @else
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; background:color-mix(in srgb, var(--c-surface) 96%, var(--c-primary) 4%); display:flex; flex-direction:column; gap:16px;">
            <h2 style="margin:0; font-size:1.15rem;">{{ __('Report an Issue') }}</h2>
            <p style="margin:0; color:var(--c-on-surface-2); line-height:1.55;">{{ __('Support sign in to report hint') }}</p>
            <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center;">
                <a href="{{ route('login', ['redirect' => route('support.index')]) }}" class="btn btn-primary">{{ __('Log In') }}</a>
                <a href="{{ route('password.request') }}" class="btn btn-outline">{{ __('Forgot password?') }}</a>
            </div>
        </article>
    @endauth

    <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; background:color-mix(in srgb, var(--c-surface) 96%, var(--c-primary) 4%); display:flex; flex-direction:column; gap:16px;">
        <h2 style="margin:0; font-size:1.15rem;">{{ __('Account access & recovery') }}</h2>
        <p style="margin:0; color:var(--c-on-surface-2); line-height:1.55;">{{ __('Account recovery help text') }}</p>
        @include('support.partials.recovery-form')
    </article>
</section>
@endsection
