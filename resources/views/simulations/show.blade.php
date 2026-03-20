@extends('layouts.dashboard')

@section('title', $simulation->name)

@section('dashboard_content')
<style>
    .sim-run-shell { display:grid; gap:16px; }
    .sim-run-header { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap; }
    .sim-run-grid { display:grid; grid-template-columns: 1.8fr 0.9fr; gap:16px; align-items:start; }
    .sim-run-main { display:grid; gap:16px; }
    .sim-run-side { position: sticky; top: 16px; display:grid; gap:12px; }
    .sim-run-chartWrap { position:relative; height: min(56vh, 520px); min-height: 340px; }
    .sim-kpis { display:grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap:10px; }
    .sim-kpi { border:1px solid var(--c-border); border-radius:12px; padding:12px; background: color-mix(in srgb, var(--c-surface) 96%, transparent); }
    .sim-kpiLabel { margin:0; color: var(--c-on-surface-2); font-size:12px; text-transform:uppercase; letter-spacing:0.06em; }
    .sim-kpiValue { margin:6px 0 0; font-size:22px; font-weight:800; }
    .sim-accordion { border:1px solid var(--c-border); border-radius:14px; overflow:hidden; background: var(--c-surface); }
    .sim-accordion summary { cursor:pointer; padding:12px 14px; font-weight:700; list-style:none; display:flex; justify-content:space-between; align-items:center; gap:12px; }
    .sim-accordion summary::-webkit-details-marker { display:none; }
    .sim-accordion summary:hover { background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); }
    .sim-accordionBody { padding:14px; border-top:1px solid var(--c-border); }
    @media (max-width: 1100px) {
        .sim-run-grid { grid-template-columns: 1fr; }
        .sim-run-side { position: static; }
        .sim-kpis { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
    }
</style>

<section class="sim-run-shell" aria-label="Simulation details">
    <div class="sim-run-grid">
        <main class="sim-run-main">
            <section class="auth-card" aria-label="Simulation header" style="padding:16px;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; flex-wrap:wrap;">
                    <div style="min-width:260px;">
                        <h1 style="margin:0;">{{ $simulation->name }}</h1>
                        <p style="margin:6px 0 0; color:var(--c-on-surface-2); font-size:13px;">
                            {{ __('Tip: keep the chart visible while you adjust controls. Use Step for learning; Run for long horizons.') }}
                        </p>
                    </div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <button id="start-tutorial" class="btn btn-outline" type="button">📚 {{ __('Start Tutorial') }}</button>
                        <a class="btn btn-secondary" href="{{ route('simulations.edit', $simulation) }}">{{ __('Edit') }}</a>
                        <a class="btn btn-outline" href="{{ route('simulations.index') }}">{{ __('Back') }}</a>
                        <form method="POST" action="{{ route('simulations.destroy', $simulation) }}" onsubmit="return confirm('{{ __('Delete this simulation?') }}');" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>
            </section>

            <section class="auth-card" aria-label="Summary" style="padding:16px;">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:10px;">
                    <h3 style="margin:0;">{{ __('Summary') }}</h3>
                    <span style="color:var(--c-on-surface-2); font-size:13px;">{{ __('Key indicators at a glance') }}</span>
                </div>
                <div class="sim-kpis">
                    <div class="sim-kpi">
                        <p class="sim-kpiLabel">{{ __('Current Value') }}</p>
                        <p id="current-value" class="sim-kpiValue" style="color: var(--c-primary);">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
                    </div>
                    <div class="sim-kpi">
                        <p class="sim-kpiLabel">{{ __('Total Contributed') }}</p>
                        <p id="total-contributed" class="sim-kpiValue">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
                    </div>
                    <div class="sim-kpi">
                        <p class="sim-kpiLabel">{{ __('Total Gain') }}</p>
                        <p id="total-gain" class="sim-kpiValue" style="color: var(--c-primary);">€0.00</p>
                    </div>
                    <div class="sim-kpi">
                        <p class="sim-kpiLabel">{{ __('Real Value (Inflation Adj.)') }}</p>
                        <p id="real-value" class="sim-kpiValue" style="color: var(--c-secondary);">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
                    </div>
                    <div class="sim-kpi">
                        <p class="sim-kpiLabel">{{ __('Max Drawdown') }}</p>
                        <p id="drawdown" class="sim-kpiValue" style="color:#ef4444;">0%</p>
                    </div>
                    <div class="sim-kpi">
                        <p class="sim-kpiLabel">{{ __('Projected CAGR') }}</p>
                        <p id="cagr" class="sim-kpiValue">0%</p>
                    </div>
                </div>
            </section>

            <section class="auth-card" aria-label="Chart" style="padding:16px;">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:10px;">
                    <h3 style="margin:0;">{{ __('Investment Growth Over Time') }}</h3>
                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        <div id="status-display" style="padding:8px 10px; border-radius:10px; border:1px solid var(--c-border); background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); font-weight:700; font-size:13px;">
                            {{ __('Ready') }}
                        </div>
                        <span id="save-status" style="font-size:13px; color:var(--c-on-surface-2);">{{ __('Not saved yet') }}</span>
                    </div>
                </div>
                <div class="sim-run-chartWrap">
                    <canvas id="sim-chart" aria-label="Simulation chart"></canvas>
                </div>
            </section>

            <details class="sim-accordion" open>
                <summary aria-label="Market Events & Teaching Moments">
                    <span>{{ __('Market Events & Teaching Moments') }}</span>
                    <span style="color:var(--c-on-surface-2); font-size:13px;">{{ __('Highlights as you run') }}</span>
                </summary>
                <div class="sim-accordionBody">
                    <ul id="event-log" style="margin:0; padding-left:18px; display:grid; gap:8px; font-size:14px; color:var(--c-on-surface-2);"></ul>
                </div>
            </details>

            <details class="sim-accordion">
                <summary aria-label="Settings">
                    <span>{{ __('Simulation Parameters') }}</span>
                    <span style="color:var(--c-on-surface-2); font-size:13px;">{{ __('What you assumed') }}</span>
                </summary>
                <div class="sim-accordionBody">
                    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:10px;">
                        <div style="padding:12px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                            <span style="color: var(--c-on-surface-2); font-size:13px;">{{ __('Initial Investment') }}</span>
                            <p style="margin:4px 0 0; font-size:18px; font-weight:800;">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
                        </div>
                        <div style="padding:12px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                            <span style="color: var(--c-on-surface-2); font-size:13px;">{{ __('Monthly Contribution') }}</span>
                            <p style="margin:4px 0 0; font-size:18px; font-weight:800;">€{{ number_format($simulation->settings['monthlyContribution'], 2) }}</p>
                        </div>
                        <div style="padding:12px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                            <span style="color: var(--c-on-surface-2); font-size:13px;">{{ __('Annual Growth Rate') }}</span>
                            <p style="margin:4px 0 0; font-size:18px; font-weight:800;">{{ number_format($simulation->settings['growthRate'] * 100, 2) }}%</p>
                        </div>
                        <div style="padding:12px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                            <span style="color: var(--c-on-surface-2); font-size:13px;">{{ __('Inflation Rate') }}</span>
                            <p style="margin:4px 0 0; font-size:18px; font-weight:800;">{{ number_format($simulation->settings['inflationRate'] * 100, 2) }}%</p>
                        </div>
                        <div style="padding:12px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                            <span style="color: var(--c-on-surface-2); font-size:13px;">{{ __('Risk Appetite') }}</span>
                            <p style="margin:4px 0 0; font-size:18px; font-weight:800;">{{ number_format($simulation->settings['riskAppetite'] * 100, 0) }}%</p>
                        </div>
                        <div style="padding:12px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                            <span style="color: var(--c-on-surface-2); font-size:13px;">{{ __('Market Influence') }}</span>
                            <p style="margin:4px 0 0; font-size:18px; font-weight:800;">{{ number_format($simulation->settings['marketInfluence'] * 100, 0) }}%</p>
                        </div>
                    </div>
                </div>
            </details>
        </main>

        <aside class="sim-run-side" aria-label="Run controls">
            <section class="auth-card" aria-label="Run controls" style="padding:16px;">
                <h3 style="margin:0 0 10px;">{{ __('Simulation Controls') }}</h3>
                <div style="display:grid; grid-template-columns: 1fr; gap:10px; margin-bottom:12px;">
                    <label style="display:grid; gap:6px;">
                        <span style="font-weight:700;">{{ __('Duration (months)') }}</span>
                        <input id="months-input" type="number" min="12" max="600" step="12" value="120" class="footer-email-input" />
                    </label>
                    <label style="display:grid; gap:6px;">
                        <span style="font-weight:700;">{{ __('Speed (seconds/step)') }}</span>
                        <input id="speed-input" type="number" min="0.1" max="10" step="0.1" value="0.25" class="footer-email-input" />
                    </label>
                    <label style="display:grid; gap:6px;">
                        <span style="font-weight:700;">{{ __('Market Regime') }}</span>
                        <select id="preset-select" class="footer-email-input">
                            <option value="balanced">{{ __('Balanced (default)') }}</option>
                            <option value="growth">{{ __('Growth / Bullish') }}</option>
                            <option value="defensive">{{ __('Defensive / Bearish') }}</option>
                            <option value="volatile">{{ __('Choppy & volatile') }}</option>
                            <option value="shock">{{ __('Stress test (crash + recovery)') }}</option>
                        </select>
                    </label>
                </div>

                <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
                    <button id="btn-run" class="btn btn-primary" style="flex:1; justify-content:center;">▶ {{ __('Run') }}</button>
                    <button id="btn-pause" class="btn btn-secondary" style="flex:1; justify-content:center;" disabled>⏸ {{ __('Pause') }}</button>
                    <button id="btn-step" class="btn btn-secondary" style="flex:1; justify-content:center;" title="{{ __('Advance by one month') }}">➜ {{ __('Step') }}</button>
                    <button id="btn-reset" class="btn btn-outline" style="flex:1; justify-content:center;">🔄 {{ __('Reset') }}</button>
                    <button id="btn-save" class="btn btn-outline" style="flex:1; justify-content:center;" title="{{ __('Save the latest simulation results to your dashboard') }}">💾 {{ __('Save Progress') }}</button>
                </div>

                <div style="margin-top:12px; display:grid; gap:10px;">
                    <div id="learning-note" style="padding:12px; border-radius:12px; border:1px solid var(--c-border); background:color-mix(in srgb, var(--c-surface) 90%, var(--c-primary) 10%); font-size:14px; line-height:1.6;"></div>
                    <div id="risk-tip" style="padding:12px; border-radius:12px; border:1px solid var(--c-border); background:color-mix(in srgb, var(--c-surface) 94%, var(--c-secondary) 6%); font-size:14px; line-height:1.6;"></div>
                </div>
            </section>
        </aside>
    </div>
</section>
@endsection

@include('components.tutorial', ['currentPage' => 'show'])

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
 document.addEventListener('DOMContentLoaded', () => {
    const chartCanvas = document.getElementById('sim-chart');
    const btnRun = document.getElementById('btn-run');
    const btnStep = document.getElementById('btn-step');
    const btnPause = document.getElementById('btn-pause');
    const btnReset = document.getElementById('btn-reset');
    const btnSave = document.getElementById('btn-save');
    const monthsInput = document.getElementById('months-input');
    const speedInput = document.getElementById('speed-input');
    const presetSelect = document.getElementById('preset-select');
    const statusDisplay = document.getElementById('status-display');
    const currentValueEl = document.getElementById('current-value');
    const totalContributedEl = document.getElementById('total-contributed');
    const totalGainEl = document.getElementById('total-gain');
    const realValueEl = document.getElementById('real-value');
    const drawdownEl = document.getElementById('drawdown');
    const cagrEl = document.getElementById('cagr');
    const learningNoteEl = document.getElementById('learning-note');
    const riskTipEl = document.getElementById('risk-tip');
    const eventLogEl = document.getElementById('event-log');
    const saveStatusEl = document.getElementById('save-status');
    const snapshotUrl = "{{ route('simulations.snapshot', $simulation) }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const i18n = @json([
        'ready' => __('Ready'),
        'running' => __('Running...'),
        'paused' => __('Paused'),
        'complete' => __('Complete'),
        'month' => __('Month :current / :total'),
        'saving' => __('Saving…'),
        'savedAt' => __('Saved :time'),
        'saveFailed' => __('Save failed. Try again.'),
        'noEvents' => __('No notable events yet. Run or step the simulation.'),
        'marketShock' => __('Market shock: :pct% month (:label)'),
        'newHigh' => __('New portfolio high reached in month :month.'),
        'drawdownCoaching' => __('Drawdown :pct% — keep contributions consistent.'),
        'presetLabel' => __('Preset: :label'),
        'balancedLabel' => __('Balanced (default)'),
    ]);

    if (!chartCanvas || !btnRun || !btnPause || !btnReset || !monthsInput || !speedInput || !presetSelect) {
        console.warn('Simulation controls are missing from the DOM. Skipping initialization.');
        return;
    }

    const chartCtx = chartCanvas.getContext('2d');
    if (!chartCtx || typeof Chart === 'undefined') {
        console.warn('Chart.js is not available, unable to render simulation chart.');
        return;
    }

    // Settings from Laravel (coerced to numbers for safety)
    const rawSettings = @json($simulation->settings);

    const asNumber = (value, fallback = 0) => {
        if (typeof value === 'string') {
            value = value.replace(/[^0-9.\-]/g, '');
        }
        const num = Number(value);
        return Number.isFinite(num) ? num : fallback;
    };

    const settings = {
        initialInvestment: asNumber(rawSettings.initialInvestment, 0),
        monthlyContribution: asNumber(rawSettings.monthlyContribution, 0),
        growthRate: asNumber(rawSettings.growthRate, 0.05),
        inflationRate: asNumber(rawSettings.inflationRate, 0.02),
        riskAppetite: Math.min(1, Math.max(0, asNumber(rawSettings.riskAppetite, 0.5))),
        marketInfluence: Math.min(1, Math.max(0, asNumber(rawSettings.marketInfluence, 0.5)))
    };

    // Currency preference
    const defaultRates = {
        EUR: 1,
        USD: 1.08,
        GBP: 0.86,
        JPY: 162.5,
    };

    function loadCachedRates() {
        try {
            const cached = localStorage.getItem('nosleguma-currency-rates');
            if (!cached) return { ...defaultRates };
            const parsed = JSON.parse(cached);
            if (!parsed?.rates) return { ...defaultRates };
            // Accept cached rates up to 24h old (same as Settings page)
            if (parsed.timestamp && (Date.now() - parsed.timestamp) < 24 * 60 * 60 * 1000) {
                return { ...defaultRates, ...parsed.rates };
            }
        } catch (e) {}
        return { ...defaultRates };
    }

    let currencyRates = loadCachedRates();

    const currencySymbols = {
        EUR: '€',
        USD: '$',
        GBP: '£',
        JPY: '¥',
    };

    function getPreferredCurrency() {
        try {
            const stored = localStorage.getItem('nosleguma-currency-preference');
            if (stored && currencyRates[stored]) {
                return stored;
            }
        } catch (e) {}
        return 'EUR';
    }

    let activeCurrency = getPreferredCurrency();

    function convertAmount(euroAmount) {
        const rate = currencyRates[activeCurrency] ?? 1;
        return euroAmount * rate;
    }

    function formatConverted(amount) {
        const symbol = currencySymbols[activeCurrency] ?? '€';
        const sign = amount < 0 ? '-' : '';
        const formatted = Math.abs(amount).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        return `${sign}${symbol}${formatted}`;
    }

    function formatCurrency(euroAmount) {
        return formatConverted(convertAmount(euroAmount));
    }

    function updateCurrencyLabels() {
        chart.data.datasets[0].label = `Nominal Value (${activeCurrency})`;
        chart.data.datasets[1].label = `Real Value (${activeCurrency})`;
        if (chart.options?.scales?.y?.title) {
            chart.options.scales.y.title.text = `Value (${activeCurrency})`;
        }
    }

    function updateSaveStatus(text, color) {
        if (!saveStatusEl) return;
        saveStatusEl.textContent = text;
        saveStatusEl.style.color = color || 'var(--c-on-surface-2)';
    }
    
    const presetConfigs = {
        balanced: {
            label: i18n.balancedLabel,
            expectedAnnual: settings.growthRate,
            monthlyVolatility: 0.02,
            shockChance: 0.05,
            shockImpact: () => -0.08,
            recoveryBias: 0.003,
            lesson: __('Balanced portfolios rely on regular contributions and modest volatility. Focus on time in the market.'),
        },
        growth: {
            label: __('Growth / Bullish'),
            expectedAnnual: Math.max(settings.growthRate + 0.02, settings.growthRate),
            monthlyVolatility: 0.03,
            shockChance: 0.04,
            shockImpact: () => -0.1,
            recoveryBias: 0.006,
            lesson: __('Growth tilt: higher expected return but bigger swings. Stick to a plan when volatility hits.'),
        },
        defensive: {
            label: __('Defensive / Bearish'),
            expectedAnnual: Math.max(settings.growthRate - 0.02, 0.02),
            monthlyVolatility: 0.012,
            shockChance: 0.03,
            shockImpact: () => -0.05,
            recoveryBias: 0.002,
            lesson: __('Defensive stance tempers losses but can lag in bull markets. Contributions matter more.'),
        },
        volatile: {
            label: __('Choppy & volatile'),
            expectedAnnual: settings.growthRate,
            monthlyVolatility: 0.045,
            shockChance: 0.08,
            shockImpact: () => (Math.random() > 0.4 ? -0.12 : 0.08),
            recoveryBias: 0.004,
            lesson: __('Choppy markets teach discipline: expect whiplash and focus on long-term averages.'),
        },
        shock: {
            label: __('Stress test (crash + recovery)'),
            expectedAnnual: Math.max(settings.growthRate - 0.01, 0.03),
            monthlyVolatility: 0.035,
            shockChance: 0.12,
            shockImpact: () => -0.18,
            recoveryBias: 0.008,
            lesson: __('Stress test simulates a crash and recovery. Diversification and time horizon matter most.'),
        }
    };

    // Simulation state
    let isRunning = false;
    let currentMonth = 0;
    let simulationData = [];
    let intervalId = null;
    let peakValue = settings.initialInvestment;
    let maxDrawdown = 0;
    let activePresetKey = 'balanced';
    let eventLog = [];

    const baseMonthlyReturnRate = Math.pow(1 + settings.growthRate, 1 / 12) - 1;
    const monthlyInflationRate = Math.pow(1 + settings.inflationRate, 1 / 12) - 1;
    const volatilityInfluence = (settings.riskAppetite + settings.marketInfluence) / 2;

    // Get theme colors
    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--c-primary').trim() || '#07a05a';
    const secondaryColor = getComputedStyle(document.documentElement).getPropertyValue('--c-secondary').trim() || '#d98e12';

    // Initialize Chart
    const chart = new Chart(chartCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Nominal Value',
                    data: [],
                    borderColor: primaryColor,
                    backgroundColor: primaryColor + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6
                },
                {
                    label: 'Real Value (Inflation Adjusted)',
                    data: [],
                    borderColor: secondaryColor,
                    backgroundColor: secondaryColor + '20',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const label = context.dataset.label ? context.dataset.label + ': ' : '';
                            return label + formatConverted(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Month',
                        font: {
                            size: 14,
                            weight: '600'
                        }
                    },
                    grid: {
                        display: false
                    }
                },
                y: {
                    display: true,
                    title: {
                        display: true,
                        text: `Value (${activeCurrency})`,
                        font: {
                            size: 14,
                            weight: '600'
                        }
                    },
                    ticks: {
                        callback: function(value) {
                            return formatConverted(value);
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                }
            },
            animation: {
                duration: 0
            }
        }
    });

    function gaussianRandom() {
        let u = 0, v = 0;
        while (u === 0) u = Math.random();
        while (v === 0) v = Math.random();
        return Math.sqrt(-2.0 * Math.log(u)) * Math.cos(2.0 * Math.PI * v);
    }

    // Light realism additions (still educational):
    // - tiny annual fee drag (common ETF expense ratios)
    // - mild return autocorrelation so “paths” feel more market-like
    const annualFeeRate = 0.002; // 0.20% / year
    const monthlyFeeRate = annualFeeRate / 12;
    let lastMonthlyReturn = baseMonthlyReturnRate;

    function seedInitialState() {
        currentMonth = 0;
        peakValue = settings.initialInvestment;
        maxDrawdown = 0;
        eventLog = [];
        simulationData = [{
            month: 0,
            value: settings.initialInvestment,
            inflationAdjusted: settings.initialInvestment,
            contributions: settings.initialInvestment,
            interestEarned: 0
        }];

        rebuildChartData('resize');
        updateSummary();
        updateLearningNote();
        updateRiskTip();
        renderEvents();
        statusDisplay.textContent = i18n.ready;
        statusDisplay.style.background = 'color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%)';
    }

    function nextMonthlyReturn() {
        const preset = presetConfigs[activePresetKey] ?? presetConfigs.balanced;
        const targetMonthly = Math.pow(1 + preset.expectedAnnual, 1 / 12) - 1;
        const noise = gaussianRandom() * (preset.monthlyVolatility || 0.02) * (1 + volatilityInfluence * 0.6);

        let shock = 0;
        if (Math.random() < preset.shockChance) {
            shock = preset.shockImpact();
            pushEvent(i18n.marketShock
                .replace(':pct', (shock * 100).toFixed(1))
                .replace(':label', preset.label));
        }

        const rawReturn = targetMonthly + noise + preset.recoveryBias + shock;
        const clamped = Math.max(-0.35, Math.min(rawReturn, 0.12));

        // Autocorrelation (smoother return paths)
        const smoothed = (0.55 * lastMonthlyReturn) + (0.45 * clamped);
        lastMonthlyReturn = smoothed;

        return smoothed;
    }

    // Calculate next month's values using compound growth with volatility
    function calculateNextMonth() {
        const lastEntry = simulationData[simulationData.length - 1];
        const nextMonth = lastEntry.month + 1;

        const monthlyReturn = nextMonthlyReturn();
        const valueAfterGrowth = lastEntry.value * (1 + monthlyReturn);
        const valueAfterFees = valueAfterGrowth * (1 - monthlyFeeRate);
        const interestEarned = valueAfterFees - lastEntry.value;
        const contributions = lastEntry.contributions + settings.monthlyContribution;
        const newValue = Math.max(0, valueAfterFees + settings.monthlyContribution);
        const inflationAdjusted = newValue / Math.pow(1 + monthlyInflationRate, nextMonth);

        const nextEntry = {
            month: nextMonth,
            value: newValue,
            inflationAdjusted,
            contributions,
            interestEarned
        };

        simulationData.push(nextEntry);
        currentMonth = nextMonth;

        if (newValue > peakValue) {
            peakValue = newValue;
            pushEvent(i18n.newHigh.replace(':month', String(nextMonth)));
        }

        const drawdown = (newValue - peakValue) / peakValue;
        maxDrawdown = Math.min(maxDrawdown, drawdown);
        if (drawdown < -0.1 && drawdown.toFixed(2) === maxDrawdown.toFixed(2)) {
            pushEvent(i18n.drawdownCoaching.replace(':pct', Math.abs(drawdown * 100).toFixed(1)));
        }
    }

    function rebuildChartData(animation = 'none') {
        chart.data.labels = simulationData.map(entry => entry.month);
        chart.data.datasets[0].data = simulationData.map(entry => convertAmount(entry.value));
        chart.data.datasets[1].data = simulationData.map(entry => convertAmount(entry.inflationAdjusted));
        updateCurrencyLabels();
        chart.update(animation);
    }

    // Update summary displays
    function updateSummary() {
        const data = simulationData[simulationData.length - 1] || simulationData[0];
        const totalGain = data.value - data.contributions;
        const years = Math.max(data.month, 1) / 12;
        // More meaningful than "vs initial investment" when contributions exist:
        // compare portfolio value to total contributed so far.
        const cagr = Math.pow(data.value / Math.max(data.contributions, 1e-6), 1 / years) - 1;

        currentValueEl.textContent = formatCurrency(data.value);
        
        totalContributedEl.textContent = formatCurrency(data.contributions);
        
        totalGainEl.textContent = formatCurrency(totalGain);
        totalGainEl.style.color = totalGain >= 0 ? primaryColor : '#ef4444';
        
        realValueEl.textContent = formatCurrency(data.inflationAdjusted);
        drawdownEl.textContent = `${(maxDrawdown * 100).toFixed(1)}%`;
        cagrEl.textContent = `${(cagr * 100).toFixed(2)}%`;
    }

    function updateLearningNote() {
        const preset = presetConfigs[activePresetKey] ?? presetConfigs.balanced;
        if (learningNoteEl) {
            learningNoteEl.textContent = preset.lesson || __('Stay invested and watch how contributions and volatility interact.');
        }
    }

    function updateRiskTip() {
        if (!riskTipEl) return;
        const risk = settings.riskAppetite;
        const market = settings.marketInfluence;
        const inflation = settings.inflationRate;
        const tips = [
            risk > 0.6 ? __('High risk appetite means larger swings. Keep an emergency fund outside this simulation.') : null,
            market > 0.6 ? __('Strong market influence toggled: external shocks will matter more. Rebalance if needed.') : null,
            inflation > 0.03 ? __('Inflation is elevated; compare nominal vs real value to see purchasing power.') : __('Inflation is moderate; compounding still beats it over time.')
        ].filter(Boolean);
        riskTipEl.textContent = tips.join(' ') || __('Use Step mode to see how each month contributes to long-term results.');
    }

    function pushEvent(text) {
        eventLog.unshift({ text, time: new Date() });
        eventLog = eventLog.slice(0, 6);
        renderEvents();
    }

    function renderEvents() {
        if (!eventLogEl) return;
        if (!eventLog.length) {
            eventLogEl.innerHTML = `<li>${i18n.noEvents}</li>`;
            return;
        }
        eventLogEl.innerHTML = eventLog.map(ev => `<li>${ev.text}</li>`).join('');
    }

    // Start simulation
    function startSimulation() {
        if (isRunning) return;
        
        isRunning = true;
        btnRun.disabled = true;
        btnPause.disabled = false;
        
        const maxMonths = parseInt(monthsInput.value);
        const stepSeconds = Math.max(0.1, parseFloat(speedInput.value) || 0.25);
        const speed = stepSeconds * 1000;
        
        statusDisplay.textContent = i18n.running;
        statusDisplay.style.background = 'color-mix(in srgb, var(--c-primary) 20%, var(--c-surface))';
        
        intervalId = setInterval(() => {
            if (currentMonth >= maxMonths) {
                pauseSimulation();
                statusDisplay.textContent = i18n.complete;
                statusDisplay.style.background = 'color-mix(in srgb, var(--c-primary) 30%, var(--c-surface))';
                queueSnapshotSave();
                return;
            }
            
            calculateNextMonth();
            rebuildChartData('none');
            updateSummary();
            statusDisplay.textContent = i18n.month
                .replace(':current', String(currentMonth))
                .replace(':total', String(maxMonths));
        }, speed);
    }

    function stepOnce() {
        pauseSimulation();
        const maxMonths = parseInt(monthsInput.value) || 120;
        if (currentMonth >= maxMonths) {
            statusDisplay.textContent = i18n.complete;
            statusDisplay.style.background = 'color-mix(in srgb, var(--c-primary) 30%, var(--c-surface))';
            queueSnapshotSave();
            return;
        }
        calculateNextMonth();
        rebuildChartData('none');
        updateSummary();
        statusDisplay.textContent = i18n.month
            .replace(':current', String(currentMonth))
            .replace(':total', String(maxMonths));
    }

    // Pause simulation
    function pauseSimulation() {
        isRunning = false;
        btnRun.disabled = false;
        btnPause.disabled = true;
        
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
        
        statusDisplay.textContent = i18n.paused;
        statusDisplay.style.background = 'color-mix(in srgb, var(--c-secondary) 20%, var(--c-surface))';
        queueSnapshotSave();
    }

    // Reset simulation
    function resetSimulation() {
        pauseSimulation();
        
        seedInitialState();
    }

    // Event listeners
    seedInitialState();

    btnRun.addEventListener('click', startSimulation);
    btnStep.addEventListener('click', stepOnce);
    btnPause.addEventListener('click', pauseSimulation);
    btnReset.addEventListener('click', resetSimulation);
    btnSave?.addEventListener('click', () => {
        updateSaveStatus('Saving…', 'var(--c-on-surface)');
        queueSnapshotSave(true);
    });
    presetSelect.addEventListener('change', (e) => {
        activePresetKey = e.target.value;
        updateLearningNote();
        updateRiskTip();
        if (!isRunning) {
            const label = presetConfigs[activePresetKey]?.label ?? i18n.balancedLabel;
            statusDisplay.textContent = i18n.presetLabel.replace(':label', label);
        }
    });

    let snapshotTimeout = null;
    function queueSnapshotSave(immediate = false) {
        if (!snapshotUrl || !csrfToken || !simulationData.length || typeof window.fetch !== 'function') return;

        const sendSnapshot = () => {
            const latestEntry = simulationData[simulationData.length - 1];
            if (!latestEntry) return;
            const totalGain = latestEntry.value - latestEntry.contributions;
            updateSaveStatus(i18n.saving, 'var(--c-on-surface)');
            fetch(snapshotUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    month: latestEntry.month,
                    value: latestEntry.value,
                    real_value: latestEntry.inflationAdjusted,
                    contributions: latestEntry.contributions,
                    total_gain: totalGain,
                    currency: activeCurrency
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Snapshot save failed');
                return response.json();
            })
            .then(() => {
                updateSaveStatus(i18n.savedAt.replace(':time', new Date().toLocaleTimeString()), 'var(--c-primary)');
            })
            .catch((error) => {
                console.warn('Unable to save simulation snapshot', error);
                updateSaveStatus(i18n.saveFailed, '#ef4444');
            });
        };

        if (immediate) {
            if (snapshotTimeout) {
                clearTimeout(snapshotTimeout);
                snapshotTimeout = null;
            }
            sendSnapshot();
            return;
        }

        if (snapshotTimeout) {
            clearTimeout(snapshotTimeout);
        }
        snapshotTimeout = setTimeout(() => {
            sendSnapshot();
            snapshotTimeout = null;
        }, 1200);
    }

    function handleCurrencyPreferenceChange() {
        // Refresh cached rates if Settings page updated them in localStorage.
        currencyRates = loadCachedRates();
        const next = getPreferredCurrency();
        if (next !== activeCurrency) {
            activeCurrency = next;
            rebuildChartData();
            updateSummary();
        }
    }

    window.addEventListener('storage', (event) => {
        if (event.key === 'nosleguma-currency-preference') {
            handleCurrencyPreferenceChange();
        }
    });
});
</script>
@endpush