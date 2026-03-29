@extends('layouts.app')

@section('title', __('Account Recovery'))

@section('content')
    <section class="auth-card" aria-label="{{ __('Account Recovery') }}">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
            <h1 style="margin:0;">{{ __('Account Recovery') }}</h1>
            <a href="{{ url('/') }}" class="btn btn-outline">{{ __('Back') }}</a>
        </div>

        <p style="margin:12px 0 0; color:var(--c-on-surface-2); font-size:14px; line-height:1.6;">
            {{ __('Account recovery help text') }}
        </p>

        @if($errors->any())
            <div role="alert" style="margin-top:12px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, #ef4444 8%);">
                <ul style="margin:0; padding-left:20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('support.store') }}" style="margin-top:20px;">
            @csrf

            <div style="display:grid; gap:16px;">
                <div>
                    <label for="contact_email" style="display:block; margin-bottom:6px; font-weight:600;">{{ __('Email for account') }}</label>
                    <input
                        id="contact_email"
                        name="contact_email"
                        type="email"
                        required
                        maxlength="255"
                        value="{{ old('contact_email') }}"
                        placeholder="Enter your account email"
                        style="width:100%; padding:10px 12px; border:1px solid var(--c-border); border-radius:8px; background:var(--c-surface); color:var(--c-on-surface);"
                    />
                </div>

                <div>
                    <label for="description" style="display:block; margin-bottom:6px; font-weight:600;">{{ __('Description') }}</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="10"
                        required
                        maxlength="400"
                        placeholder="{{ __('2FA recovery description placeholder') }}"
                        style="width:100%; padding:10px 12px; border:1px solid var(--c-border); border-radius:8px; background:var(--c-surface); color:var(--c-on-surface); font-family:inherit; resize:vertical;"
                    >{{ old('description') }}</textarea>
                    <p style="margin:8px 0 0; font-size:13px; color:var(--c-on-surface-2);">{{ __('Maximum 400 words hint') }}</p>
                </div>

                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center; min-width:220px;">
                        {{ __('Submit request') }}
                    </button>
                    <a href="{{ url('/') }}" class="btn btn-outline" style="min-width:200px; justify-content:center; display:inline-flex; align-items:center;">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </div>
        </form>
    </section>
@endsection

