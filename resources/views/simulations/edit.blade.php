@extends('layouts.dashboard')

@section('title', 'Edit Simulation')

@section('dashboard_content')
<section class="auth-card sim-edit-card" aria-label="{{ __('Edit Simulation') }}">
    <header class="sim-edit-header">
        <div>
            <h1 class="sim-edit-title">{{ __('Edit Simulation') }}</h1>
            <p class="sim-edit-lead">{{ __('Edit simulation subtitle') }}</p>
        </div>
        <a class="btn btn-secondary sim-edit-back" href="{{ route('simulations.show', $simulation) }}">{{ __('Back') }}</a>
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

        @php($s = $simulation->settings)
        <div class="sim-edit-grid">
            <label style="display:grid; gap:6px;">
                <span>{{ __('Initial Investment') }}</span>
                <input type="number" step="1" name="initial_investment" value="{{ old('initial_investment', $s['initialInvestment'] ?? 1000) }}" required class="footer-email-input" />
            </label>
            <label style="display:grid; gap:6px;">
                <span>{{ __('Monthly Contribution') }}</span>
                <input type="number" step="1" name="monthly_contribution" value="{{ old('monthly_contribution', $s['monthlyContribution'] ?? 100) }}" required class="footer-email-input" />
            </label>
            <label style="display:grid; gap:6px;">
                <span>{{ __('Growth Rate (annual)') }} <span style="font-size:12px; color:var(--c-on-surface-2);">0–100%</span></span>
                <div class="accel-input" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="growth_rate" value="{{ old('growth_rate', round(($s['growthRate'] ?? 0.07) * 100, 2)) }}" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label style="display:grid; gap:6px;">
                <span>{{ __('Risk Appetite') }} <span style="font-size:12px; color:var(--c-on-surface-2);">0–100%</span></span>
                <div class="accel-input" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="risk_appetite" value="{{ old('risk_appetite', round(($s['riskAppetite'] ?? 0.5) * 100, 2)) }}" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label style="display:grid; gap:6px;">
                <span>{{ __('Market Influence') }} <span style="font-size:12px; color:var(--c-on-surface-2);">0–100%</span></span>
                <div class="accel-input" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="market_influence" value="{{ old('market_influence', round(($s['marketInfluence'] ?? 0.5) * 100, 2)) }}" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label style="display:grid; gap:6px;">
                <span>{{ __('Inflation Rate (annual)') }} <span style="font-size:12px; color:var(--c-on-surface-2);">0–100%</span></span>
                <div class="accel-input" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="inflation_rate" value="{{ old('inflation_rate', round(($s['inflationRate'] ?? 0.02) * 100, 2)) }}" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label style="display:grid; gap:6px;">
                <span>{{ __('Investors (count)') }}</span>
                <input type="number" step="1" min="1" name="investors" value="{{ old('investors', $s['investors'] ?? 1) }}" required class="footer-email-input" />
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
    const deltaHundredths = (elapsedMs) => {
        if (elapsedMs >= 4000) return 100;
        if (elapsedMs >= 2000) return 10;
        return 1;
    };
    const setupAccel = (container) => {
        const input = container.querySelector('input[data-accel="percent"]');
        const minus = container.querySelector('.accel-minus');
        const plus = container.querySelector('.accel-plus');
        if (!input || !minus || !plus) return;

        const min = Number(input.min ?? 0);
        const max = Number(input.max ?? 100);
        const scale = 100;
        const minInt = Math.round(min * scale);
        const maxInt = Math.round(max * scale);
        let timer = null;
        let start = 0;
        let holdInt = 0;
        let holdDir = 1;

        const applyStep = () => {
            const elapsed = Date.now() - start;
            const d = deltaHundredths(elapsed) * holdDir;
            holdInt = clamp(holdInt + d, minInt, maxInt);
            const next = holdInt / scale;
            input.value = next.toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1');
            input.dispatchEvent(new Event('input', { bubbles: true }));
        };

        const startHold = (dir) => {
            if (timer) clearInterval(timer);
            holdDir = dir;
            start = Date.now();
            const raw = Number(String(input.value || 0).replace(',', '.')) || 0;
            holdInt = Math.round(raw * scale);
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


