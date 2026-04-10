<section class="auth-card" style="margin-top:24px;" aria-label="{{ __('Password reset from support') }}">
    <h2 style="margin:0 0 8px; font-size:1.25rem;">{{ __('Password reset from support') }}</h2>
    <p style="margin:0 0 16px; color:var(--c-on-surface-2); font-size:14px; line-height:1.6;">
        {{ __('Password reset from support intro') }}
    </p>
    <a href="{{ route('password.request', ['from' => 'support']) }}" class="btn btn-primary">{{ __('Request password reset email') }}</a>
</section>
