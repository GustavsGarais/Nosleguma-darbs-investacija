@extends('layouts.dashboard')

@section('title', 'Edit Simulation')

@section('dashboard_content')
<section class="auth-card" aria-label="{{ __('Edit Simulation') }}">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
        <h1 style="margin:0;">{{ __('Edit Simulation') }}</h1>
        <a class="btn btn-secondary" href="{{ route('simulations.show', $simulation) }}">{{ __('Back') }}</a>
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

    <form method="POST" action="{{ route('simulations.update', $simulation) }}" style="display:grid; gap:12px;">
        @csrf
        @method('PUT')

        <label style="display:grid; gap:6px;">
            <div style="display:flex; align-items:center; gap:6px;">
                <span>{{ __('Name') }}</span>
                <span style="font-size:12px; color:var(--c-on-surface-2);">{{ __('(max 30 characters)') }}</span>
            </div>
            <input type="text" name="name" value="{{ old('name', $simulation->name) }}" required maxlength="30" class="footer-email-input" />
        </label>

        @php($s = $simulation->settings)
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:12px;">
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

        <div style="display:flex; gap:12px;">
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
    const getStep = (elapsedMs) => {
        if (elapsedMs >= 4000) return 1;
        if (elapsedMs >= 2000) return 0.1;
        return 0.01;
    };
    const setupAccel = (container) => {
        const input = container.querySelector('input[data-accel="percent"]');
        const minus = container.querySelector('.accel-minus');
        const plus = container.querySelector('.accel-plus');
        if (!input || !minus || !plus) return;

        const min = Number(input.min ?? 0);
        const max = Number(input.max ?? 100);
        let timer = null;
        let start = 0;

        const tick = (dir) => {
            const elapsed = Date.now() - start;
            const step = getStep(elapsed) * dir;
            const current = Number(input.value || 0);
            const next = clamp(Math.round((current + step) * 100) / 100, min, max);
            input.value = next.toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1');
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

    document.querySelectorAll('.accel-input').forEach(setupAccel);
});
</script>
@endpush


