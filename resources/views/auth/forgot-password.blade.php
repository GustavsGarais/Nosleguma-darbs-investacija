@extends('layouts.app')

@section('title', __('Forgot Password'))

@section('content')
<div class="auth-page">
    <section class="auth-card" aria-label="{{ __('Forgot Password') }}">
        <h1 style="margin:0 0 6px;">{{ __('Forgot your password?') }}</h1>
        <p style="margin:0 0 16px; color: var(--c-on-surface-2);">
            {{ __('No problem. Enter your email and we will send a reset link.') }}
        </p>

        @if (session('status'))
            <div role="status" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
                {{ session('status') }}
            </div>
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

            <label style="display:grid; gap:6px;">
                <span style="font-weight:700; color: var(--c-on-surface);">{{ __('Email') }}</span>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="footer-email-input" autocomplete="email" />
            </label>

            <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <a href="{{ route('login') }}" class="btn btn-outline">{{ __('Back to login') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Email Password Reset Link') }}</button>
            </div>
        </form>
    </section>
</div>
@endsection
