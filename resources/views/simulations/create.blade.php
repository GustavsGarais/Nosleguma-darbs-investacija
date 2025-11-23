@extends('layouts.dashboard')

@section('title', 'New Simulation')

@section('dashboard_content')
<section class="auth-card" aria-label="Create Simulation">
    <h1 style="margin:0 0 12px;">Create Simulation</h1>

    @if ($errors->any())
        <div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li style="color: var(--c-on-surface);">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('simulations.store') }}" style="display:grid; gap:12px;">
        @csrf

        <label style="display:grid; gap:6px;">
            <div style="display:flex; align-items:center; gap:6px;">
                <span>Name</span>
            </div>
            <input type="text" name="name" value="{{ old('name') }}" required class="footer-email-input" />
        </label>

        <div style="display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:12px;">
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
                <input type="number" step="0.01" name="initial_investment" value="{{ old('initial_investment', 1000) }}" required class="footer-email-input" />
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
                <input type="number" step="0.01" name="monthly_contribution" value="{{ old('monthly_contribution', 100) }}" required class="footer-email-input" />
            </label>
            <label style="display:grid; gap:6px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span>Growth Rate (0-1 annual)</span>
                    <div class="info-bubble" data-tooltip="Expected annual return as a decimal (0-1). Example: 0.07 = 7% annual growth. Higher rates mean more potential gains but also more risk.">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
                <input type="number" step="0.001" min="0" max="1" name="growth_rate" value="{{ old('growth_rate', 0.07) }}" required class="footer-email-input" />
            </label>
            <label style="display:grid; gap:6px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span>Risk Appetite (0-1)</span>
                    <div class="info-bubble" data-tooltip="How much volatility you're comfortable with (0-1). Higher values mean your investment will have bigger swings up and down, simulating a more aggressive strategy.">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
                <input type="number" step="0.01" min="0" max="1" name="risk_appetite" value="{{ old('risk_appetite', 0.5) }}" required class="footer-email-input" />
            </label>
            <label style="display:grid; gap:6px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span>Market Influence (0-1)</span>
                    <div class="info-bubble" data-tooltip="How much external market factors affect your simulation (0-1). Higher values add more realistic market fluctuations, making the simulation more dynamic and unpredictable.">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
                <input type="number" step="0.01" min="0" max="1" name="market_influence" value="{{ old('market_influence', 0.5) }}" required class="footer-email-input" />
            </label>
            <label style="display:grid; gap:6px;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span>Inflation Rate (0-1 annual)</span>
                    <div class="info-bubble" data-tooltip="The annual inflation percentage as a decimal (0-1). Example: 0.02 = 2% inflation. This helps you see the real purchasing power of your investment over time, accounting for price increases.">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--c-on-surface-2); cursor:help;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                            <path d="M12 17h.01"></path>
                        </svg>
                    </div>
                </div>
                <input type="number" step="0.001" min="0" max="1" name="inflation_rate" value="{{ old('inflation_rate', 0.02) }}" required class="footer-email-input" />
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
                <input type="number" step="1" min="1" name="investors" value="{{ old('investors', 1) }}" required class="footer-email-input" />
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


