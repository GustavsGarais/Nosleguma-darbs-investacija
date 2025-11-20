@extends('layouts.dashboard')

@section('title', 'Settings')

@section('dashboard_content')
<section class="auth-card" aria-label="Settings overview" style="padding:32px; display:flex; flex-direction:column; gap:24px;">
    <header style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px;">
        <div>
            <h1 style="margin:0;">Account</h1>
            <p style="margin:6px 0 0; color:var(--c-on-surface-2);">Manage profile info, security, notifications, and currency in one place.</p>
        </div>
        <a class="btn btn-outline" href="{{ route('dashboard') }}">← Back</a>
    </header>

    @if (session('status') === 'profile-updated')
        <div role="status" style="padding:12px 16px; border-radius:10px; background:color-mix(in srgb, var(--c-primary) 18%, var(--c-surface)); border:1px solid color-mix(in srgb, var(--c-primary) 35%, var(--c-border));">
            <strong>Saved.</strong> Your account details are up to date.
        </div>
    @endif

    <div style="display:flex; flex-direction:column; gap:24px;">
        <!-- Profile -->
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; background:color-mix(in srgb, var(--c-surface) 96%, var(--c-primary) 4%); display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                <div>
                    <h2 style="margin:0;">Profile</h2>
                    <p style="margin:6px 0 0; color:var(--c-on-surface-2); font-size:14px;">Update the details that appear across simulations.</p>
                </div>
                <button type="button" title="Your name and email sync across reports, invites, and alerts." style="border:none; background:transparent; color:var(--c-on-surface-2); font-weight:600; width:24px; height:24px; border-radius:50%; border:1px solid var(--c-border); cursor:help;">?</button>
            </div>
            <form method="POST" action="{{ route('settings.profile') }}" style="display:flex; flex-direction:column; gap:16px;">
                @csrf
                @method('patch')

                <label style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;">Full name</span>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="footer-email-input" required />
                    @error('name')
                        <span style="color:#e53935; font-size:12px;">{{ $message }}</span>
                    @enderror
                </label>

                <label style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;">Email address</span>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="footer-email-input" required />
                    @error('email')
                        <span style="color:#e53935; font-size:12px;">{{ $message }}</span>
                    @enderror
                </label>

                <button type="submit" class="btn btn-primary" style="align-self:flex-start;">Save profile</button>
            </form>
        </article>

        <!-- Security -->
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; display:flex; flex-direction:column; gap:12px;">
            <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                <div>
                    <h2 style="margin:0;">Security</h2>
                    <p style="margin:6px 0 0; color:var(--c-on-surface-2); font-size:14px;">Quick actions that keep your account safe.</p>
                </div>
                <button type="button" title="Reset leaked passwords and close suspicious sessions from here." style="border:none; background:transparent; color:var(--c-on-surface-2); font-weight:600; width:24px; height:24px; border-radius:50%; border:1px solid var(--c-border); cursor:help;">?</button>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:12px;">
                <div style="flex:1 1 280px; border-radius:12px; padding:16px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-secondary) 5%); display:flex; justify-content:space-between; align-items:center; gap:12px;">
                    <div>
                        <h4 style="margin:0 0 4px;">Password</h4>
                        <p style="margin:0; color:var(--c-on-surface-2); font-size:13px;">Use a unique 12+ character password.</p>
                    </div>
                    <a class="btn btn-outline" href="{{ route('password.request') }}">Reset</a>
                </div>
                <div style="flex:1 1 280px; border-radius:12px; padding:16px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-secondary) 5%); display:flex; justify-content:space-between; align-items:center; gap:12px;">
                    <div>
                        <h4 style="margin:0 0 4px;">Sessions</h4>
                        <p style="margin:0; color:var(--c-on-surface-2); font-size:13px;">Sign out if devices look unfamiliar.</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Sign out current session?');">
                        @csrf
                        <button class="btn btn-outline" type="submit">Sign out</button>
                    </form>
                </div>
            </div>
        </article>

        <!-- Notifications -->
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                <div>
                    <h2 style="margin:0;">Notifications</h2>
                    <p style="margin:6px 0 0; color:var(--c-on-surface-2); font-size:14px;">Decide which alerts reach your inbox.</p>
                </div>
                <button type="button" title="These toggles only affect this browser for now and are stored locally." style="border:none; background:transparent; color:var(--c-on-surface-2); font-weight:600; width:24px; height:24px; border-radius:50%; border:1px solid var(--c-border); cursor:help;">?</button>
            </div>
            <div style="display:grid; gap:12px;">
                @foreach ([
                    ['label' => 'Monthly performance insights', 'key' => 'notify-performance'],
                    ['label' => 'Security alerts & logins', 'key' => 'notify-security'],
                ] as $toggle)
                    <label style="display:flex; justify-content:space-between; gap:12px; align-items:center; border:1px solid var(--c-border); border-radius:10px; padding:12px 16px;">
                        <span>{{ $toggle['label'] }}</span>
                        <input type="checkbox" data-setting-key="{{ $toggle['key'] }}" />
                    </label>
                @endforeach
            </div>
        </article>

        <!-- Currency -->
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                <div>
                    <h2 style="margin:0;">Currency & Region</h2>
                    <p style="margin:6px 0 0; color:var(--c-on-surface-2); font-size:14px;">See what your euros become in USD, GBP, or JPY.</p>
                </div>
                <button type="button" title="Rates are illustrative demo values that refresh on load." style="border:none; background:transparent; color:var(--c-on-surface-2); font-weight:600; width:24px; height:24px; border-radius:50%; border:1px solid var(--c-border); cursor:help;">?</button>
            </div>
            <div style="display:grid; gap:12px;">
                <label style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;">Amount in euros</span>
                    <input type="number" id="currency-amount" class="footer-email-input" min="0" step="100" value="1000" />
                </label>
                <label style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;">Target currency</span>
                    <select id="currency-select" class="footer-email-input">
                        <option value="USD">US Dollar (USD)</option>
                        <option value="GBP">British Pound (GBP)</option>
                        <option value="JPY">Japanese Yen (JPY)</option>
                        <option value="EUR">Euro (EUR)</option>
                    </select>
                </label>
                <div style="border:1px solid var(--c-border); border-radius:10px; padding:16px;">
                    <p style="margin:0; color:var(--c-on-surface-2); font-size:14px;">Converted amount</p>
                    <p id="converted-amount" style="margin:4px 0 0; font-size:24px; font-weight:700;">—</p>
                </div>
                <div style="border:1px dashed var(--c-border); border-radius:10px; padding:16px;">
                    <p style="margin:0 0 6px; font-weight:600;">Quick previews</p>
                    <ul style="margin:0; padding-left:18px; display:grid; gap:4px; font-size:14px;">
                        <li data-preview-base="5000">Emergency fund: <span class="currency-preview">—</span></li>
                        <li data-preview-base="25000">Long-term goal: <span class="currency-preview">—</span></li>
                        <li data-preview-base="100000">Retirement fund: <span class="currency-preview">—</span></li>
                    </ul>
                </div>
            </div>
        </article>

        <!-- Danger zone -->
        <article style="border:1px solid #e53935; border-radius:16px; padding:24px; background:color-mix(in srgb, #e53935 10%, var(--c-surface)); display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                <div>
                    <h2 style="margin:0; color:#e53935;">Danger zone</h2>
                    <p style="margin:6px 0 0; color:var(--c-on-surface-2); font-size:14px;">Permanently remove your account and simulation history.</p>
                </div>
                <button type="button" title="Deleting your account clears every simulation and cannot be undone." style="border:none; background:transparent; color:#e53935; font-weight:600; width:24px; height:24px; border-radius:50%; border:1px solid #e53935; cursor:help;">?</button>
            </div>
            <form method="POST" action="{{ route('settings.destroy') }}" style="display:flex; flex-direction:column; gap:16px;" onsubmit="return confirm('Delete your account and all simulations? This cannot be undone.');">
                @csrf
                @method('delete')
                <label style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;">Confirm password</span>
                    <input type="password" name="password" class="footer-email-input" required />
                </label>
                <button type="submit" class="btn btn-outline" style="color:#e53935; border-color:#e53935;">Delete account</button>
            </form>
        </article>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggles = document.querySelectorAll('[data-setting-key]');
    const localKey = 'nosleguma-settings';
    const saved = JSON.parse(localStorage.getItem(localKey) || '{}');

    toggles.forEach(toggle => {
        const key = toggle.dataset.settingKey;
        toggle.checked = Boolean(saved[key]);
        toggle.addEventListener('change', () => {
            saved[key] = toggle.checked;
            localStorage.setItem(localKey, JSON.stringify(saved));
        });
    });

    const amountInput = document.getElementById('currency-amount');
    const select = document.getElementById('currency-select');
    const resultEl = document.getElementById('converted-amount');
    const previewEls = document.querySelectorAll('[data-preview-base]');

    const rates = {
        EUR: 1,
        USD: 1.08,
        GBP: 0.86,
        JPY: 162.5,
    };

    const currencySymbols = {
        EUR: '€',
        USD: '$',
        GBP: '£',
        JPY: '¥',
    };

    function format(amount, currency) {
        return `${currencySymbols[currency]}${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    function convert(amount, currency) {
        const rate = rates[currency] ?? 1;
        return amount * rate;
    }

    function renderCurrency() {
        const amount = parseFloat(amountInput.value) || 0;
        const currency = select.value;
        const converted = convert(amount, currency);
        resultEl.textContent = format(converted, currency);

        previewEls.forEach(el => {
            const base = parseFloat(el.dataset.previewBase) || 0;
            const previewAmount = convert(base, currency);
            const previewLabel = el.querySelector('.currency-preview');
            if (previewLabel) {
                previewLabel.textContent = format(previewAmount, currency);
            }
        });

        localStorage.setItem('nosleguma-currency-preference', currency);
    }

    const storedCurrency = localStorage.getItem('nosleguma-currency-preference');
    if (storedCurrency && rates[storedCurrency]) {
        select.value = storedCurrency;
    }

    amountInput?.addEventListener('input', renderCurrency);
    select?.addEventListener('change', renderCurrency);

    renderCurrency();
});
</script>
@endpush

