<section class="auth-card" style="margin-top:24px;" aria-label="{{ __('Account access & recovery') }}">
    <h2 style="margin:0 0 8px; font-size:1.25rem;">{{ __('Account access & recovery') }}</h2>
    <p style="margin:0 0 16px; color:var(--c-on-surface-2); font-size:14px; line-height:1.6;">
        {{ __('Account recovery help text') }}
    </p>

    @if($errors->getBag('recovery')->isNotEmpty())
        <div role="alert" style="margin-bottom:16px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, #ef4444 8%);">
            <ul style="margin:0; padding-left:20px;">
                @foreach($errors->getBag('recovery')->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('support.recovery.store') }}" style="margin-top:8px;">
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
                    placeholder="{{ __('Enter your account email') }}"
                    class="footer-email-input"
                    style="width:100%; padding:10px 12px;"
                    autocomplete="email"
                />
            </div>

            <div>
                <label for="recovery_description" style="display:block; margin-bottom:6px; font-weight:600;">{{ __('Description') }}</label>
                <textarea
                    id="recovery_description"
                    name="description"
                    rows="10"
                    required
                    maxlength="2000"
                    placeholder="{{ __('2FA recovery description placeholder') }}"
                    class="footer-email-input"
                    style="width:100%; padding:10px 12px; font-family:inherit; resize:vertical;"
                >{{ old('description') }}</textarea>
                <p style="margin:8px 0 0; font-size:13px; color:var(--c-on-surface-2);">{{ __('Maximum 400 words hint') }}</p>
            </div>

            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <button type="submit" class="btn btn-primary" style="min-width:220px;">{{ __('Submit request') }}</button>
                <a href="{{ url('/') }}" class="btn btn-outline">{{ __('Cancel') }}</a>
            </div>
        </div>
    </form>
</section>
