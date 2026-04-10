@extends('layouts.app')

@section('title', __('Forgot Password'))

@section('content')
@php
    $fromSupport = $fromSupport ?? false;
@endphp
<div class="auth-page">
    <section class="auth-card" aria-label="{{ __('Forgot Password') }}">
        <h1 style="margin:0 0 6px;">{{ __('Forgot your password?') }}</h1>
        <p style="margin:0 0 16px; color: var(--c-on-surface-2);">
            {{ __('No problem. Enter your email and we will send a reset link.') }}
        </p>
        @if ($fromSupport)
            <p style="margin:-8px 0 16px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; font-size:14px; color:var(--c-on-surface-2); line-height:1.5;">
                {{ __('Password reset support explainer') }}
            </p>
        @endif

        @if (session('status'))
            <div role="status" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
                {{ session('status') }}
            </div>
            @if (config('mail.default') === 'log')
                <p style="margin:12px 0 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; font-size:14px; color:var(--c-on-surface-2); line-height:1.5;">
                    {{ __('Password reset mail log notice') }}
                </p>
            @endif
        @endif

        @if ($errors->any())
            <div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
                <ul style="margin:0; padding-left:18px;">
                    @foreach ($errors->all() as $error)
                        <li style="color: var(--c-on-surface);">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" style="display:grid; gap:12px;">
            @csrf
            @if ($fromSupport)
                <input type="hidden" name="from_support" value="1" />
            @endif

            <label style="display:grid; gap:6px;">
                <span style="font-weight:700; color: var(--c-on-surface);">{{ __('Email') }}</span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="footer-email-input" autocomplete="email" />
            </label>

            @if ($fromSupport)
                <label style="display:grid; gap:6px;">
                    <span style="font-weight:700; color: var(--c-on-surface);">{{ __('Password reset optional message label') }}</span>
                    <textarea
                        name="support_message"
                        rows="4"
                        maxlength="2000"
                        class="footer-email-input"
                        style="width:100%; padding:10px 12px; font-family:inherit; resize:vertical;"
                        placeholder="{{ __('Password reset optional message placeholder') }}"
                    >{{ old('support_message') }}</textarea>
                    <span style="font-size:13px; color:var(--c-on-surface-2);">{{ __('Password reset optional message hint') }}</span>
                </label>
            @endif

            <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <a href="{{ $fromSupport ? route('support.index') : route('login') }}" class="btn btn-outline">{{ $fromSupport ? __('Back') : __('Back to login') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Email Password Reset Link') }}</button>
            </div>
        </form>
    </section>
</div>
@endsection
