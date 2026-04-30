@extends('layouts.dashboard')

@section('title', __('Edit Simulation'))

@section('dashboard_content')
<section class="auth-card sim-edit-card" aria-label="{{ __('Edit Simulation') }}">
    <header class="sim-edit-header">
        <div>
            <h1 class="sim-edit-title">{{ __('Edit Simulation') }}</h1>
            <p class="sim-edit-lead">{{ __('Edit simulation subtitle') }}</p>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <x-help-sheet id="sim-edit-help" :title="__('Simulation help')" :button-label="__('Open help')">
                <h3>{{ __('Tip') }}</h3>
                <p>{{ __('Small changes in risk or market influence can drastically change drawdowns. Save and compare multiple scenarios instead of trying to “perfect” one.') }}</p>

                <h3>{{ __('Market Regime (on run page)') }}</h3>
                <p>{{ __('Market Regime is chosen on the run page. It changes market behavior (drift/volatility/shocks) while keeping your scenario inputs the same.') }}</p>
            </x-help-sheet>
            <a class="btn btn-secondary sim-edit-back" href="{{ route('simulations.show', $simulation) }}">{{ __('Back') }}</a>
        </div>
    </header>

    @if ($errors->any())
        <div role="alert" aria-live="polite" class="sim-edit-errors">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="sim-edit-form" method="POST" action="{{ route('simulations.update', $simulation) }}">
        @csrf
        @method('PUT')

        <label class="sim-edit-name">
            <span class="sim-edit-label-row">
                <span>{{ __('Name') }}</span>
                <span class="sim-edit-hint">{{ __('(max 30 characters)') }}</span>
            </span>
            <input type="text" name="name" value="{{ old('name', $simulation->name) }}" required maxlength="30" class="footer-email-input" />
        </label>

        @php
            $s = $simulation->settings;
            $savedMode = ($s['defaultRunnerMode'] ?? 'classic') === 'playground' ? 'playground' : 'classic';
        @endphp
        <fieldset class="sim-edit-mode-fieldset" style="border:1px solid var(--c-border); border-radius:12px; padding:14px 16px; margin:0;">
            <legend class="sim-edit-mode-legend" style="font-weight:700; padding:0 6px;">{{ __('Simulation mode') }}</legend>
            <p style="margin:0 0 10px; font-size:13px; color:var(--c-on-surface-2); line-height:1.5;">{{ __('Choose the default mode when you open this simulation. You can switch modes anytime on the run page.') }}</p>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="radio" name="simulation_mode" value="classic" @checked(old('simulation_mode', $savedMode) === 'classic') />
                    <span>{{ __('Classic (auto monthly)') }}</span>
                </label>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="radio" name="simulation_mode" value="playground" @checked(old('simulation_mode', $savedMode) === 'playground') />
                    <span>{{ __('Hands-on portfolio lab') }}</span>
                </label>
            </div>
        </fieldset>

        <div class="sim-edit-grid">
            <label>
                <span style="display:flex; align-items:center; gap:6px;">
                    <span>{{ __('Initial Investment') }}</span>
                    <div class="info-bubble" tabindex="0" role="button" aria-label="{{ __('Explain Initial Investment') }}"
                        data-tooltip="{{ __('The starting amount you invest in euros. This is your initial capital before any growth or contributions.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </span>
                <div class="accel-input">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="1" min="0" name="initial_investment" value="{{ old('initial_investment', $s['initialInvestment'] ?? 1000) }}" required class="footer-email-input" data-accel="int" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label>
                <span style="display:flex; align-items:center; gap:6px;">
                    <span>{{ __('Monthly Contribution') }}</span>
                    <div class="info-bubble" tabindex="0" role="button" aria-label="{{ __('Explain Monthly Contribution') }}"
                        data-tooltip="{{ __('The amount you add to your investment each month. Regular contributions help your portfolio grow through compound interest.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </span>
                <div class="accel-input">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="1" min="0" name="monthly_contribution" value="{{ old('monthly_contribution', $s['monthlyContribution'] ?? 100) }}" required class="footer-email-input" data-accel="int" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label>
                <span style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                    <span>{{ __('Growth Rate (annual)') }} <span style="font-size:12px; color:var(--c-on-surface-2);">0–100%</span></span>
                    <div class="info-bubble" tabindex="0" role="button" aria-label="{{ __('Explain Growth Rate') }}"
                        data-tooltip="{{ __('Expected annual return in percent (0-100). Example: 7% annual growth. Higher rates raise average returns but also interact with volatility.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </span>
                <div class="accel-input">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="growth_rate" value="{{ old('growth_rate', round(($s['growthRate'] ?? 0.07) * 100, 2)) }}" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label>
                <span style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                    <span>{{ __('Risk Appetite') }} <span style="font-size:12px; color:var(--c-on-surface-2);">0–100%</span></span>
                    <div class="info-bubble" tabindex="0" role="button" aria-label="{{ __('Explain Risk Appetite') }}"
                        data-tooltip="{{ __('How much volatility you are comfortable with (0-100%). Higher values mean bigger swings up and down.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </span>
                <div class="accel-input">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="risk_appetite" value="{{ old('risk_appetite', round(($s['riskAppetite'] ?? 0.5) * 100, 2)) }}" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label>
                <span style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                    <span>{{ __('Market Influence') }} <span style="font-size:12px; color:var(--c-on-surface-2);">0–100%</span></span>
                    <div class="info-bubble" tabindex="0" role="button" aria-label="{{ __('Explain Market Influence') }}"
                        data-tooltip="{{ __('How much external market behavior affects your simulation (0-100%). Higher values amplify crowd waves and shocks, making paths more dynamic.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </span>
                <div class="accel-input">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="market_influence" value="{{ old('market_influence', round(($s['marketInfluence'] ?? 0.5) * 100, 2)) }}" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label>
                <span style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                    <span>{{ __('Inflation Rate (annual)') }} <span style="font-size:12px; color:var(--c-on-surface-2);">0–100%</span></span>
                    <div class="info-bubble" tabindex="0" role="button" aria-label="{{ __('Explain Inflation Rate') }}"
                        data-tooltip="{{ __('Annual inflation percentage. Used to show the real (inflation-adjusted) value so you can compare purchasing power over time.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </span>
                <div class="accel-input">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="inflation_rate" value="{{ old('inflation_rate', round(($s['inflationRate'] ?? 0.02) * 100, 2)) }}" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label>
                <span style="display:flex; align-items:center; gap:6px;">
                    <span>{{ __('Investors (count)') }}</span>
                    <div class="info-bubble" tabindex="0" role="button" aria-label="{{ __('Explain Investors') }}"
                        data-tooltip="{{ __('Number of investors in this scenario. More investors can amplify crowd behavior (profit-taking and panic waves), especially with high Market Influence.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </span>
                <div class="accel-input">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="1" min="1" name="investors" value="{{ old('investors', $s['investors'] ?? 1) }}" required class="footer-email-input" data-accel="int" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
        </div>

        <div class="sim-edit-actions">
            <a href="{{ route('simulations.show', $simulation) }}" class="btn btn-outline">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const clamp = (v, min, max) => Math.min(max, Math.max(min, v));

    const stepDecimals = (stepValue) => {
        const s = String(stepValue ?? '');
        const idx = s.indexOf('.');
        return idx >= 0 ? Math.min(6, s.length - idx - 1) : 0;
    };

    const computeStep = (elapsedMs, baseStep, mode) => {
        if (mode === 'int') {
            if (elapsedMs >= 4000) return 10;
            if (elapsedMs >= 2000) return 5;
            return 1;
        }
        if (elapsedMs >= 4000) return baseStep * 100;
        if (elapsedMs >= 2000) return baseStep * 10;
        return baseStep;
    };

    const formatValue = (value, decimals) => {
        if (decimals <= 0) return String(Math.round(value));
        return value.toFixed(decimals).replace(/\.0+$/, '').replace(/(\.\d*[1-9])0+$/, '$1');
    };

    const setupAccel = (container) => {
        const input = container.querySelector('input[data-accel]');
        const minus = container.querySelector('.accel-minus');
        const plus = container.querySelector('.accel-plus');
        if (!input || !minus || !plus) return;

        const mode = input.dataset.accel || 'number';
        const min = input.min === '' ? -Infinity : Number(input.min);
        const max = input.max === '' ? Infinity : Number(input.max);
        const baseStep = Math.max(0.000001, Number(input.step || 1));
        const decimals = mode === 'int' ? 0 : stepDecimals(input.step || baseStep);

        let timer = null;
        let start = 0;
        let holdInt = 0;
        let holdDir = 1;
        const scale = decimals > 0 ? Math.pow(10, decimals) : 1;
        const minInt = Number.isFinite(min) ? Math.round(min * scale) : -Number.MAX_SAFE_INTEGER;
        const maxInt = Number.isFinite(max) ? Math.round(max * scale) : Number.MAX_SAFE_INTEGER;

        const applyStep = () => {
            const elapsed = Date.now() - start;
            const mag = computeStep(elapsed, baseStep, mode);
            const deltaInt = mode === 'int'
                ? Math.round(mag) * holdDir
                : Math.round(mag * scale) * holdDir;
            holdInt = clamp(holdInt + deltaInt, minInt, maxInt);
            const next = holdInt / scale;
            input.value = mode === 'int'
                ? String(holdInt)
                : formatValue(next, decimals);
            input.dispatchEvent(new Event('input', { bubbles: true }));
        };

        const startHold = (dir) => {
            if (timer) clearInterval(timer);
            holdDir = dir;
            start = Date.now();
            const raw = Number(String(input.value || 0).replace(',', '.')) || 0;
            holdInt = mode === 'int'
                ? Math.round(raw)
                : Math.round(raw * scale);
            holdInt = clamp(holdInt, minInt, maxInt);
            applyStep();
            timer = setInterval(applyStep, 50);
        };

        const stopHold = () => {
            if (timer) clearInterval(timer);
            timer = null;
        };

        const bind = (btn, dir) => {
            btn.addEventListener('mousedown', () => startHold(dir));
            btn.addEventListener('touchstart', (e) => { e.preventDefault(); startHold(dir); }, { passive:false });
        };

        bind(minus, -1);
        bind(plus, 1);
        ['mouseup','mouseleave','touchend','touchcancel'].forEach(evt => {
            minus.addEventListener(evt, stopHold);
            plus.addEventListener(evt, stopHold);
        });
        document.addEventListener('mouseup', stopHold);
        document.addEventListener('touchend', stopHold);
    };

    document.querySelectorAll('.accel-input').forEach(setupAccel);
});
</script>
@endpush


