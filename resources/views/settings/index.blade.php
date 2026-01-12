@extends('layouts.dashboard')

@section('title', 'Settings')

@section('dashboard_content')
<section class="auth-card" aria-label="Settings overview" style="padding:32px; display:flex; flex-direction:column; gap:24px;">
    <header style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px;">
        <div>
            <h1 style="margin:0;">{{ __('Account') }}</h1>
            <p style="margin:6px 0 0; color:var(--c-on-surface-2);">{{ __('Manage profile info, security, notifications, and currency in one place.') }}</p>
        </div>
        <a class="btn btn-outline" href="{{ route('simulations.index') }}">← {{ __('Back') }}</a>
    </header>

    @if (session('status') === 'profile-updated')
        <div role="status" style="padding:12px 16px; border-radius:10px; background:color-mix(in srgb, var(--c-primary) 18%, var(--c-surface)); border:1px solid color-mix(in srgb, var(--c-primary) 35%, var(--c-border));">
            <strong>{{ __('Saved.') }}</strong> {{ __('Your account details are up to date.') }}
        </div>
    @endif

    @if ($errors->any())
        <div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid #e53935; border-radius:10px; background: color-mix(in srgb, #e53935 10%, var(--c-surface));">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li style="color: var(--c-on-surface);">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display:flex; flex-direction:column; gap:24px;">
        <!-- Profile -->
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; background:color-mix(in srgb, var(--c-surface) 96%, var(--c-primary) 4%); display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <h2 style="margin:0;">{{ __('Profile') }}</h2>
                    <div class="info-bubble" data-tooltip="{{ __('Your name and email sync across reports, invites, and alerts.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('settings.profile') }}" style="display:flex; flex-direction:column; gap:16px;">
                @csrf
                @method('patch')

                <label style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;">{{ __('Full name') }}</span>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="footer-email-input" required />
                    @error('name')
                        <span style="color:#e53935; font-size:12px;">{{ $message }}</span>
                    @enderror
                </label>

                <label style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;">{{ __('Email address') }}</span>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="footer-email-input" required />
                    @error('email')
                        <span style="color:#e53935; font-size:12px;">{{ $message }}</span>
                    @enderror
                </label>

                <button type="submit" class="btn btn-primary" style="align-self:flex-start;">{{ __('Save profile') }}</button>
            </form>
        </article>

        <!-- Security -->
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <h2 style="margin:0;">{{ __('Security') }}</h2>
                    <div class="info-bubble" data-tooltip="{{ __('Manage your password and two-factor authentication to keep your account secure.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div style="display:grid; gap:16px;">
                <div style="border-radius:12px; padding:16px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-secondary) 5%); display:flex; justify-content:space-between; align-items:center; gap:12px;">
                    <div>
                        <h4 style="margin:0 0 4px;">{{ __('Password') }}</h4>
                        <p style="margin:0; color:var(--c-on-surface-2); font-size:13px;">{{ __('Minimum 12 characters with uppercase, lowercase, and numbers or symbols.') }}</p>
                    </div>
                    <a class="btn btn-outline" href="{{ route('password.request') }}">{{ __('Reset') }}</a>
                </div>
                <div style="border-radius:12px; padding:16px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-secondary) 5%); display:flex; justify-content:space-between; align-items:center; gap:12px;">
                    <div>
                        <h4 style="margin:0 0 4px;">{{ __('Two-Factor Authentication (2FA)') }}</h4>
                        <p style="margin:0; color:var(--c-on-surface-2); font-size:13px;">{{ __('Add an extra layer of security to your account.') }}</p>
                    </div>
                    <button class="btn btn-outline" type="button" disabled>{{ __('Coming Soon') }}</button>
                </div>
            </div>
        </article>

        <!-- Notifications -->
        <article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <h2 style="margin:0;">{{ __('Notifications') }}</h2>
                    <div class="info-bubble" data-tooltip="{{ __('These toggles only affect this browser for now and are stored locally.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div style="display:grid; gap:12px;">
                @foreach ([
                    ['label' => __('Monthly performance insights'), 'key' => 'notify-performance'],
                    ['label' => __('Security alerts & logins'), 'key' => 'notify-security'],
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
                <div style="display:flex; align-items:center; gap:6px;">
                    <h2 style="margin:0;">{{ __('Currency & Region') }}</h2>
                    <div class="info-bubble" data-tooltip="{{ __('Rates are fetched from real-time exchange rate API and refresh on load.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div style="display:grid; gap:12px;">
                <label style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;">{{ __('Amount in euros') }}</span>
                    <input type="number" id="currency-amount" class="footer-email-input" min="0" step="100" value="1000" />
                </label>
                <label style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;">{{ __('Target currency') }}</span>
                    <select id="currency-select" class="footer-email-input">
                        <option value="USD">{{ __('US Dollar (USD)') }}</option>
                        <option value="GBP">{{ __('British Pound (GBP)') }}</option>
                        <option value="JPY">{{ __('Japanese Yen (JPY)') }}</option>
                        <option value="EUR">{{ __('Euro (EUR)') }}</option>
                    </select>
                </label>
                <div style="border:1px solid var(--c-border); border-radius:10px; padding:16px;">
                    <p style="margin:0; color:var(--c-on-surface-2); font-size:14px;">{{ __('Converted amount') }}</p>
                    <p id="converted-amount" style="margin:4px 0 0; font-size:24px; font-weight:700;">—</p>
                </div>
                <div style="border:1px dashed var(--c-border); border-radius:10px; padding:16px;">
                    <p style="margin:0 0 6px; font-weight:600;">{{ __('Quick previews') }}</p>
                    <ul style="margin:0; padding-left:18px; display:grid; gap:4px; font-size:14px;">
                        <li data-preview-base="5000">{{ __('Emergency fund') }}: <span class="currency-preview">—</span></li>
                        <li data-preview-base="25000">{{ __('Long-term goal') }}: <span class="currency-preview">—</span></li>
                        <li data-preview-base="100000">{{ __('Retirement fund') }}: <span class="currency-preview">—</span></li>
                    </ul>
                </div>
            </div>
        </article>

        <!-- Danger zone -->
        <article style="border:1px solid #e53935; border-radius:16px; padding:24px; background:color-mix(in srgb, #e53935 10%, var(--c-surface)); display:flex; flex-direction:column; gap:16px;">
            <div style="display:flex; justify-content:space-between; gap:12px; align-items:flex-start;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <h2 style="margin:0; color:#e53935;">{{ __('Danger zone') }}</h2>
                    <div class="info-bubble" data-tooltip="{{ __('Deleting your account clears every simulation and cannot be undone.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#e53935; cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('settings.destroy') }}" style="display:flex; flex-direction:column; gap:16px;" onsubmit="return confirm('{{ __('Delete your account and all simulations? This cannot be undone.') }}');">
                @csrf
                @method('delete')
                <label style="display:flex; flex-direction:column; gap:6px;">
                    <span style="font-weight:600;">{{ __('Confirm password') }}</span>
                    <input type="password" name="password" class="footer-email-input" required />
                    @error('password', 'userDeletion')
                        <span style="color:#e53935; font-size:12px;">{{ $message }}</span>
                    @enderror
                </label>
                <button type="submit" class="btn btn-outline" style="color:#e53935; border-color:#e53935;">{{ __('Delete account') }}</button>
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

    // Default fallback rates (used if API fails)
    const defaultRates = {
        EUR: 1,
        USD: 1.08,
        GBP: 0.86,
        JPY: 162.5,
    };

    let rates = { ...defaultRates };

    const currencySymbols = {
        EUR: '€',
        USD: '$',
        GBP: '£',
        JPY: '¥',
    };

    // Fetch real-time exchange rates from API
    async function fetchExchangeRates() {
        try {
            const response = await fetch('https://api.exchangerate-api.com/v4/latest/EUR');
            if (!response.ok) throw new Error('API request failed');
            
            const data = await response.json();
            if (data && data.rates) {
                rates = {
                    EUR: 1,
                    USD: data.rates.USD || defaultRates.USD,
                    GBP: data.rates.GBP || defaultRates.GBP,
                    JPY: data.rates.JPY || defaultRates.JPY,
                };
                
                // Store rates with timestamp
                try {
                    localStorage.setItem('nosleguma-currency-rates', JSON.stringify({
                        rates: rates,
                        timestamp: Date.now()
                    }));
                } catch (e) {}
                
                renderCurrency();
                return true;
            }
        } catch (error) {
            console.warn('Failed to fetch exchange rates, using cached or default rates:', error);
            // Try cached rates
            try {
                const cached = localStorage.getItem('nosleguma-currency-rates');
                if (cached) {
                    const parsed = JSON.parse(cached);
                    if (parsed.timestamp && (Date.now() - parsed.timestamp) < 24 * 60 * 60 * 1000) {
                        rates = parsed.rates || defaultRates;
                        renderCurrency();
                        return true;
                    }
                }
            } catch (e) {}
            rates = { ...defaultRates };
            renderCurrency();
            return false;
        }
        return false;
    }

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

    // Fetch rates on load
    fetchExchangeRates();

    const storedCurrency = localStorage.getItem('nosleguma-currency-preference');
    if (storedCurrency && rates[storedCurrency]) {
        select.value = storedCurrency;
    }

    amountInput?.addEventListener('input', renderCurrency);
    select?.addEventListener('change', async () => {
        // Fetch fresh rates when currency changes
        await fetchExchangeRates();
        renderCurrency();
    });

    renderCurrency();
});
</script>
<style>
.info-bubble {
    position: relative;
    display: inline-flex;
    align-items: center;
    transition: opacity 0.2s;
}

.info-bubble:hover {
    opacity: 0.8;
}

.info-bubble:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: calc(100% + 12px);
    left: 50%;
    transform: translateX(-50%);
    padding: 14px 18px;
    background: var(--c-surface) !important;
    border: 2px solid var(--c-border);
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    width: max-content;
    max-width: 300px;
    min-width: 220px;
    font-size: 14px;
    line-height: 1.7;
    color: var(--c-on-surface) !important;
    font-weight: 500;
    z-index: 10000;
    pointer-events: none;
    white-space: normal;
    text-align: left;
    opacity: 1 !important;
}

.info-bubble:hover::before {
    content: '';
    position: absolute;
    bottom: calc(100% + 4px);
    left: 50%;
    transform: translateX(-50%);
    border: 7px solid transparent;
    border-top-color: var(--c-border);
    z-index: 10001;
    pointer-events: none;
    filter: drop-shadow(0 -2px 4px rgba(0,0,0,0.1));
}
</style>
@endpush

