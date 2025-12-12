@extends('layouts.dashboard')

@section('title', $simulation->name)

@section('dashboard_content')
<section class="auth-card" aria-label="Simulation details">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <h1 style="margin:0;">{{ $simulation->name }}</h1>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <a class="btn btn-secondary" href="{{ route('simulations.edit', $simulation) }}">Edit</a>
            <form method="POST" action="{{ route('simulations.destroy', $simulation) }}" onsubmit="return confirm('Delete this simulation?');" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline">Delete</button>
            </form>
            <a class="btn btn-outline" href="{{ route('simulations.index') }}">Back</a>
        </div>
    </div>

    <div style="margin-top:16px; display:grid; gap:16px;">
        <!-- Simulation Controls -->
        <section class="auth-card" aria-label="Run controls" style="padding:16px;">
            <h3 style="margin:0 0 12px;">Simulation Controls</h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px; margin-bottom:16px;">
                <label style="display:grid; gap:6px;">
                    <span style="font-weight:600;">Duration (months)</span>
                    <input id="months-input" type="number" min="12" max="600" step="12" value="120" class="footer-email-input" />
                </label>
                <label style="display:grid; gap:6px;">
                    <span style="font-weight:600;">Speed (seconds per step)</span>
                    <input id="speed-input" type="number" min="0.1" max="10" step="0.1" value="0.25" class="footer-email-input" />
                </label>
                <label style="display:grid; gap:6px;">
                    <span style="font-weight:600;">Market Regime</span>
                    <select id="preset-select" class="footer-email-input">
                        <option value="balanced">Balanced (default)</option>
                        <option value="growth">Growth / Bullish</option>
                        <option value="defensive">Defensive / Bearish</option>
                        <option value="volatile">Choppy & volatile</option>
                        <option value="shock">Stress test (crash + recovery)</option>
                    </select>
                </label>
                <div style="display:grid; gap:6px;">
                    <span style="font-weight:600;">Status</span>
                    <div id="status-display" style="padding:10px 12px; border-radius:8px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                        Ready
                    </div>
                </div>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
                <button id="btn-run" class="btn btn-primary">▶ Run Simulation</button>
                <button id="btn-step" class="btn btn-secondary" title="Advance by one month">➜ Step</button>
                <button id="btn-pause" class="btn btn-secondary" disabled>⏸ Pause</button>
                <button id="btn-reset" class="btn btn-outline">🔄 Reset</button>
                <button id="btn-save" class="btn btn-outline" title="Save the latest simulation results to your dashboard">💾 Save Progress</button>
                <span id="save-status" style="font-size:13px; color:var(--c-on-surface-2);">Not saved yet</span>
            </div>
            <div style="margin-top:12px; display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:10px;">
                <div id="learning-note" style="padding:12px; border-radius:10px; border:1px solid var(--c-border); background:color-mix(in srgb, var(--c-surface) 90%, var(--c-primary) 10%); font-size:14px;"></div>
                <div id="risk-tip" style="padding:12px; border-radius:10px; border:1px solid var(--c-border); background:color-mix(in srgb, var(--c-surface) 94%, var(--c-secondary) 6%); font-size:14px;"></div>
            </div>
        </section>

        <!-- Summary Cards -->
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:12px;">
            <div class="auth-card" style="padding:16px;">
                <h4 style="margin:0 0 8px; color: var(--c-on-surface-2); font-size:14px; text-transform:uppercase;">Current Value</h4>
                <p id="current-value" style="margin:0; font-size:24px; font-weight:700; color: var(--c-primary);">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
            </div>
            <div class="auth-card" style="padding:16px;">
                <h4 style="margin:0 0 8px; color: var(--c-on-surface-2); font-size:14px; text-transform:uppercase;">Total Contributed</h4>
                <p id="total-contributed" style="margin:0; font-size:24px; font-weight:700;">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
            </div>
            <div class="auth-card" style="padding:16px;">
                <h4 style="margin:0 0 8px; color: var(--c-on-surface-2); font-size:14px; text-transform:uppercase;">Total Gain</h4>
                <p id="total-gain" style="margin:0; font-size:24px; font-weight:700; color: var(--c-primary);">€0.00</p>
            </div>
            <div class="auth-card" style="padding:16px;">
                <h4 style="margin:0 0 8px; color: var(--c-on-surface-2); font-size:14px; text-transform:uppercase;">Real Value (Inflation Adj.)</h4>
                <p id="real-value" style="margin:0; font-size:24px; font-weight:700; color: var(--c-secondary);">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
            </div>
            <div class="auth-card" style="padding:16px;">
                <h4 style="margin:0 0 8px; color: var(--c-on-surface-2); font-size:14px; text-transform:uppercase;">Max Drawdown</h4>
                <p id="drawdown" style="margin:0; font-size:24px; font-weight:700; color:#ef4444;">0%</p>
            </div>
            <div class="auth-card" style="padding:16px;">
                <h4 style="margin:0 0 8px; color: var(--c-on-surface-2); font-size:14px; text-transform:uppercase;">Projected CAGR</h4>
                <p id="cagr" style="margin:0; font-size:24px; font-weight:700;">0%</p>
            </div>
        </div>

        <!-- Chart -->
        <section class="auth-card" aria-label="Chart" style="padding:16px;">
            <h3 style="margin:0 0 12px;">Investment Growth Over Time</h3>
            <div style="position:relative; height:400px;">
                <canvas id="sim-chart" aria-label="Simulation chart"></canvas>
            </div>
        </section>

        <!-- Settings Display -->
        <section class="auth-card" aria-label="Settings" style="padding:16px;">
            <h3 style="margin:0 0 12px;">Simulation Parameters</h3>
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:12px;">
                <div style="padding:12px; border-radius:8px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:14px;">Initial Investment</span>
                    <p style="margin:4px 0 0; font-size:18px; font-weight:700;">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
                </div>
                <div style="padding:12px; border-radius:8px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:14px;">Monthly Contribution</span>
                    <p style="margin:4px 0 0; font-size:18px; font-weight:700;">€{{ number_format($simulation->settings['monthlyContribution'], 2) }}</p>
                </div>
                <div style="padding:12px; border-radius:8px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:14px;">Annual Growth Rate</span>
                    <p style="margin:4px 0 0; font-size:18px; font-weight:700;">{{ number_format($simulation->settings['growthRate'] * 100, 2) }}%</p>
                </div>
                <div style="padding:12px; border-radius:8px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:14px;">Inflation Rate</span>
                    <p style="margin:4px 0 0; font-size:18px; font-weight:700;">{{ number_format($simulation->settings['inflationRate'] * 100, 2) }}%</p>
                </div>
                <div style="padding:12px; border-radius:8px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:14px;">Risk Appetite</span>
                    <p style="margin:4px 0 0; font-size:18px; font-weight:700;">{{ number_format($simulation->settings['riskAppetite'] * 100, 0) }}%</p>
                </div>
                <div style="padding:12px; border-radius:8px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:14px;">Market Influence</span>
                    <p style="margin:4px 0 0; font-size:18px; font-weight:700;">{{ number_format($simulation->settings['marketInfluence'] * 100, 0) }}%</p>
                </div>
            </div>
        </section>

        <!-- Event log for educational feedback -->
        <section class="auth-card" aria-label="Notable events" style="padding:16px;">
            <h3 style="margin:0 0 12px;">Market Events & Teaching Moments</h3>
            <ul id="event-log" style="margin:0; padding-left:18px; display:grid; gap:8px; font-size:14px; color:var(--c-on-surface-2);"></ul>
        </section>
    </div>
</section>
@endsection

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
    const currencyRates = {
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
            label: 'Balanced (steady compounding)',
            expectedAnnual: settings.growthRate,
            monthlyVolatility: 0.02,
            shockChance: 0.05,
            shockImpact: () => -0.08,
            recoveryBias: 0.003,
            lesson: 'Balanced portfolios rely on regular contributions and modest volatility. Focus on time in the market.',
        },
        growth: {
            label: 'Growth / Bullish',
            expectedAnnual: Math.max(settings.growthRate + 0.02, settings.growthRate),
            monthlyVolatility: 0.03,
            shockChance: 0.04,
            shockImpact: () => -0.1,
            recoveryBias: 0.006,
            lesson: 'Growth tilt: higher expected return but bigger swings. Stick to a plan when volatility hits.',
        },
        defensive: {
            label: 'Defensive / Bearish',
            expectedAnnual: Math.max(settings.growthRate - 0.02, 0.02),
            monthlyVolatility: 0.012,
            shockChance: 0.03,
            shockImpact: () => -0.05,
            recoveryBias: 0.002,
            lesson: 'Defensive stance tempers losses but can lag in bull markets. Contributions matter more.',
        },
        volatile: {
            label: 'Choppy & volatile',
            expectedAnnual: settings.growthRate,
            monthlyVolatility: 0.045,
            shockChance: 0.08,
            shockImpact: () => (Math.random() > 0.4 ? -0.12 : 0.08),
            recoveryBias: 0.004,
            lesson: 'Choppy markets teach discipline: expect whiplash and focus on long-term averages.',
        },
        shock: {
            label: 'Stress test (crash + recovery)',
            expectedAnnual: Math.max(settings.growthRate - 0.01, 0.03),
            monthlyVolatility: 0.035,
            shockChance: 0.12,
            shockImpact: () => -0.18,
            recoveryBias: 0.008,
            lesson: 'Stress test simulates a crash and recovery. Diversification and time horizon matter most.',
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
        statusDisplay.textContent = 'Ready';
        statusDisplay.style.background = 'color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%)';
    }

    function nextMonthlyReturn() {
        const preset = presetConfigs[activePresetKey] ?? presetConfigs.balanced;
        const targetMonthly = Math.pow(1 + preset.expectedAnnual, 1 / 12) - 1;
        const noise = gaussianRandom() * (preset.monthlyVolatility || 0.02) * (1 + volatilityInfluence * 0.6);

        let shock = 0;
        if (Math.random() < preset.shockChance) {
            shock = preset.shockImpact();
            pushEvent(`Market shock: ${(shock * 100).toFixed(1)}% month (${preset.label})`);
        }

        const adjustedReturn = targetMonthly + noise + preset.recoveryBias + shock;
        return Math.max(-0.35, Math.min(adjustedReturn, 0.12));
    }

    // Calculate next month's values using compound growth with volatility
    function calculateNextMonth() {
        const lastEntry = simulationData[simulationData.length - 1];
        const nextMonth = lastEntry.month + 1;

        const monthlyReturn = nextMonthlyReturn();
        const valueAfterGrowth = lastEntry.value * (1 + monthlyReturn);
        const interestEarned = valueAfterGrowth - lastEntry.value;
        const contributions = lastEntry.contributions + settings.monthlyContribution;
        const newValue = Math.max(0, valueAfterGrowth + settings.monthlyContribution);
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
            pushEvent(`New portfolio high reached in month ${nextMonth}.`);
        }

        const drawdown = (newValue - peakValue) / peakValue;
        maxDrawdown = Math.min(maxDrawdown, drawdown);
        if (drawdown < -0.1 && drawdown.toFixed(2) === maxDrawdown.toFixed(2)) {
            pushEvent(`Drawdown ${Math.abs(drawdown * 100).toFixed(1)}% — keep contributions consistent.`);
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
        const cagr = Math.pow(data.value / Math.max(settings.initialInvestment, 1e-6), 1 / years) - 1;

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
            learningNoteEl.textContent = preset.lesson || 'Stay invested and watch how contributions and volatility interact.';
        }
    }

    function updateRiskTip() {
        if (!riskTipEl) return;
        const risk = settings.riskAppetite;
        const market = settings.marketInfluence;
        const inflation = settings.inflationRate;
        const tips = [
            risk > 0.6 ? 'High risk appetite means larger swings. Keep an emergency fund outside this simulation.' : null,
            market > 0.6 ? 'Strong market influence toggled: external shocks will matter more. Rebalance if needed.' : null,
            inflation > 0.03 ? 'Inflation is elevated; compare nominal vs real value to see purchasing power.' : 'Inflation is moderate; compounding still beats it over time.'
        ].filter(Boolean);
        riskTipEl.textContent = tips.join(' ') || 'Use Step mode to see how each month contributes to long-term results.';
    }

    function pushEvent(text) {
        eventLog.unshift({ text, time: new Date() });
        eventLog = eventLog.slice(0, 6);
        renderEvents();
    }

    function renderEvents() {
        if (!eventLogEl) return;
        if (!eventLog.length) {
            eventLogEl.innerHTML = '<li>No notable events yet. Run or step the simulation.</li>';
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
        
        statusDisplay.textContent = 'Running...';
        statusDisplay.style.background = 'color-mix(in srgb, var(--c-primary) 20%, var(--c-surface))';
        
        intervalId = setInterval(() => {
            if (currentMonth >= maxMonths) {
                pauseSimulation();
                statusDisplay.textContent = 'Complete';
                statusDisplay.style.background = 'color-mix(in srgb, var(--c-primary) 30%, var(--c-surface))';
                queueSnapshotSave();
                return;
            }
            
            calculateNextMonth();
            rebuildChartData('none');
            updateSummary();
            statusDisplay.textContent = `Month ${currentMonth} / ${maxMonths}`;
        }, speed);
    }

    function stepOnce() {
        pauseSimulation();
        const maxMonths = parseInt(monthsInput.value) || 120;
        if (currentMonth >= maxMonths) {
            statusDisplay.textContent = 'Complete';
            statusDisplay.style.background = 'color-mix(in srgb, var(--c-primary) 30%, var(--c-surface))';
            queueSnapshotSave();
            return;
        }
        calculateNextMonth();
        rebuildChartData('none');
        updateSummary();
        statusDisplay.textContent = `Month ${currentMonth} / ${maxMonths}`;
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
        
        statusDisplay.textContent = 'Paused';
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
            statusDisplay.textContent = `Preset: ${presetConfigs[activePresetKey]?.label ?? 'Balanced'}`;
        }
    });

    let snapshotTimeout = null;
    function queueSnapshotSave(immediate = false) {
        if (!snapshotUrl || !csrfToken || !simulationData.length || typeof window.fetch !== 'function') return;

        const sendSnapshot = () => {
            const latestEntry = simulationData[simulationData.length - 1];
            if (!latestEntry) return;
            const totalGain = latestEntry.value - latestEntry.contributions;
            updateSaveStatus('Saving…', 'var(--c-on-surface)');
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
                updateSaveStatus(`Saved ${new Date().toLocaleTimeString()}`, 'var(--c-primary)');
            })
            .catch((error) => {
                console.warn('Unable to save simulation snapshot', error);
                updateSaveStatus('Save failed. Try again.', '#ef4444');
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