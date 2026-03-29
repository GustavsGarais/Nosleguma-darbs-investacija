@extends('layouts.dashboard')

@section('title', 'New Simulation')

@section('dashboard_content')
<section class="auth-card" aria-label="Create Simulation">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
        <h1 style="margin:0;">{{ __('Create Simulation') }}</h1>
        <a href="{{ route('simulations.index') }}" class="btn btn-secondary">← {{ __('Back') }}</a>
    </div>

    @if ($errors->any())
        <div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li style="color: var(--c-on-surface);">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('simulations.store') }}" style="display:grid; gap:16px;">
        @csrf

        <label style="display:grid; gap:6px;">
            <div style="display:flex; align-items:center; gap:6px;">
                <span>{{ __('Name') }}</span>
                <span style="font-size:12px; color:var(--c-on-surface-2);">{{ __('(max 30 characters)') }}</span>
            </div>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="30" class="footer-email-input" />
        </label>

        <div class="create-sim-grid" style="display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:16px;">
            <label style="display:grid; gap:6px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span>Initial Investment</span>
                    <div class="info-bubble" data-tooltip="The starting amount you invest in euros. This is your initial capital before any growth or contributions.">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
                <div class="num-input-group" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" name="initial_investment" value="{{ old('initial_investment', 1000) }}" required class="footer-email-input" data-accel="number" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label style="display:grid; gap:6px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span>Monthly Contribution</span>
                    <div class="info-bubble" data-tooltip="The amount you add to your investment each month. Regular contributions help your portfolio grow faster through compound interest.">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
                <div class="num-input-group" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" name="monthly_contribution" value="{{ old('monthly_contribution', 100) }}" required class="footer-email-input" data-accel="number" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label style="display:grid; gap:6px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span>{{ __('Growth Rate (annual, % )') }}</span>
                    <div class="info-bubble" data-tooltip="{{ __('Expected annual return in percent (0-100). Example: 7% annual growth. Higher rates mean more potential gains but also more risk.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
                <div class="num-input-group" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="growth_rate" value="{{ old('growth_rate', 7) }}" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label style="display:grid; gap:6px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span>{{ __('Risk Appetite (%)') }}</span>
                    <div class="info-bubble" data-tooltip="{{ __('How much volatility you are comfortable with (0-100%). Higher values mean your investment will have bigger swings up and down, simulating a more aggressive strategy.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
                <div class="num-input-group" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="risk_appetite" value="{{ old('risk_appetite', 50) }}" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label style="display:grid; gap:6px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span>{{ __('Market Influence (%)') }}</span>
                    <div class="info-bubble" data-tooltip="{{ __('How much external market factors affect your simulation (0-100%). Higher values add more realistic market fluctuations, making the simulation more dynamic and unpredictable.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
                <div class="num-input-group" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="market_influence" value="{{ old('market_influence', 50) }}" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label style="display:grid; gap:6px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span>{{ __('Inflation Rate (annual, % )') }}</span>
                    <div class="info-bubble" data-tooltip="{{ __('The annual inflation percentage (0-100). Example: 2% inflation. This helps you see the real purchasing power of your investment over time, accounting for price increases.') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
                <div class="num-input-group" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="inflation_rate" value="{{ old('inflation_rate', 2) }}" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label style="display:grid; gap:6px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span>Investors (count)</span>
                    <div class="info-bubble" data-tooltip="The number of investors participating in this simulation. Useful for tracking group investments or comparing different scenarios.">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
                <div class="num-input-group" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="1" min="1" name="investors" value="{{ old('investors', 1) }}" required class="footer-email-input" data-accel="int" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
        </div>

        <div style="display:flex; gap:12px;">
            <a href="{{ route('simulations.index') }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Create</button>
        </div>
    </form>
</section>
@endsection

@include('components.tutorial', ['currentPage' => 'create'])

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
        // percent/number: accelerate by x1 -> x10 -> x100 using the input's step as base
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

        const tick = (dir) => {
            const elapsed = Date.now() - start;
            const step = computeStep(elapsed, baseStep, mode) * dir;
            const current = Number(input.value || 0);
            const nextRaw = clamp(current + step, min, max);

            const factor = decimals > 0 ? Math.pow(10, decimals) : 1;
            const next = decimals > 0 ? Math.round(nextRaw * factor) / factor : Math.round(nextRaw);

            input.value = formatValue(next, decimals);
            input.dispatchEvent(new Event('input', { bubbles: true }));
        };

        const startHold = (dir) => {
            if (timer) clearInterval(timer);
            start = Date.now();
            tick(dir);
            timer = setInterval(() => tick(dir), 50);
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

    document.querySelectorAll('.num-input-group').forEach(setupAccel);
});
</script>
@endpush

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
    z-index: 1001;
    pointer-events: none;
    filter: drop-shadow(0 -2px 4px rgba(0,0,0,0.1));
}
</style>


