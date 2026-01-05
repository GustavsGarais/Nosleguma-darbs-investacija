@extends('layouts.dashboard')

@section('title', isset($simulation) ? $simulation->name : __('Simulations'))

@section('dashboard_content')
<div class="simulations-page">
    @if(!isset($simulation))
    <!-- Welcome Section + Simulation List -->
    <section aria-label="Welcome" class="auth-card" style="margin-top:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <h1 style="margin:0 0 8px;">{{ __('Welcome :name!', ['name' => auth()->user()->name]) }}</h1>
                <p style="margin:0; color: var(--c-on-surface);">{{ __("You're signed in. Your data is loaded from the database.") }}</p>
            </div>
            @if(auth()->user()->tutorial_completed)
                <button id="start-tutorial" class="btn btn-outline" type="button">📚 {{ __('Start Tutorial') }}</button>
            @endif
        </div>
    </section>

    <section class="auth-card" aria-label="Simulations" style="margin-top:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
            <h2 style="margin:0;">{{ __('Your Simulations') }}</h2>
            <a href="{{ route('simulations.create') }}" class="btn btn-primary">New Simulation</a>
        </div>

        @if(session('success'))
            <div role="status" style="margin-top:12px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
                {{ session('success') }}
            </div>
        @endif

        @if($simulations->count())
            <div style="overflow:auto; margin-top:16px;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">Name</th>
                            <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">Latest Value</th>
                            <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">Last Updated</th>
                            <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">Created</th>
                            <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($simulations as $sim)
                            @php
                                $snapshot = $sim->data['snapshot'] ?? null;
                                $lastValue = $snapshot['value'] ?? ($sim->settings['initialInvestment'] ?? 0);
                                $capturedAt = $snapshot['captured_at'] ?? null;
                                $updatedText = $capturedAt
                                    ? \Illuminate\Support\Carbon::parse($capturedAt)->diffForHumans()
                                    : 'Not saved yet';
                            @endphp
                            <tr>
                                <td style="padding:8px; border-bottom:1px solid var(--c-border);">
                                    <a href="{{ route('simulations.index', ['simulation' => $sim->id]) }}">{{ $sim->name }}</a>
                                </td>
                                <td style="padding:8px; border-bottom:1px solid var(--c-border);">
                                    <span class="currency-value" data-currency-value="{{ $lastValue }}">{{ '€'.number_format($lastValue, 2) }}</span>
                                </td>
                                <td style="padding:8px; border-bottom:1px solid var(--c-border);">{{ $updatedText }}</td>
                                <td style="padding:8px; border-bottom:1px solid var(--c-border);">{{ $sim->created_at->diffForHumans() }}</td>
                                <td style="padding:8px; border-bottom:1px solid var(--c-border); display:flex; gap:8px;">
                                    <a class="btn btn-secondary btn-sm" href="{{ route('simulations.edit', $sim) }}">Edit</a>
                                    <form method="POST" action="{{ route('simulations.destroy', $sim) }}" onsubmit="return confirm('Delete this simulation?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:12px;">
                {{ $simulations->links() }}
            </div>
        @else
            <p style="margin-top:16px;">{{ __('No simulations yet.') }} <a href="{{ route('simulations.create') }}">{{ __('Create your first simulation') }}</a>.</p>
        @endif
    </section>
    @else
    <!-- Simulation View: Graph as Main Focus -->
    <style>
        .simulation-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-height: calc(100vh - 140px);
        }
        .sim-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            flex-shrink: 0;
        }
        .sim-main-content {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 20px;
            flex: 1;
            min-height: 0;
        }
        .sim-metrics {
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 8px;
            max-height: calc(100vh - 300px);
        }
        .sim-metrics::-webkit-scrollbar {
            width: 6px;
        }
        .sim-metrics::-webkit-scrollbar-track {
            background: transparent;
        }
        .sim-metrics::-webkit-scrollbar-thumb {
            background: var(--c-border);
            border-radius: 3px;
        }
        .sim-chart-area {
            display: flex;
            flex-direction: column;
            min-height: 600px;
            max-height: calc(100vh - 300px);
        }
        .sim-controls-bottom {
            margin-top: 0;
        }
        .metric-card {
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: 10px;
            padding: 14px;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .metric-card:hover {
            border-color: var(--c-primary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .metric-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--c-on-surface-2);
            font-weight: 600;
            margin-bottom: 6px;
        }
        .metric-value {
            font-size: 24px;
            font-weight: 700;
            line-height: 1.2;
        }
        .chart-container {
            flex: 1;
            min-height: 600px;
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            display: flex;
            flex-direction: column;
        }
        .chart-canvas-wrapper {
            flex: 1;
            min-height: 500px;
            position: relative;
        }
        .controls-toggle {
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: 12px;
            padding: 12px 16px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            transition: all 0.2s;
            margin-bottom: 0;
        }
        .controls-toggle:hover {
            background: color-mix(in srgb, var(--c-surface) 95%, var(--c-primary) 5%);
            border-color: var(--c-primary);
        }
        .controls-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        .controls-content.expanded {
            max-height: 800px;
            transition: max-height 0.5s ease-in;
        }
        .controls-inner {
            padding: 20px;
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: 12px;
            border-top: none;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
            margin-top: 0;
        }
        @media (max-width: 1200px) {
            .sim-main-content {
                grid-template-columns: 240px 1fr;
            }
        }
        @media (max-width: 968px) {
            .sim-main-content {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr;
            }
            .sim-metrics {
                flex-direction: row;
                overflow-x: auto;
                overflow-y: visible;
                padding-right: 0;
                padding-bottom: 8px;
                max-height: none;
            }
            .sim-chart-area {
                min-height: 500px;
                max-height: none;
            }
        }
    </style>
    
    <div class="simulation-container" style="margin-top: 20px;">
        <!-- Header -->
        @php
            $nameWords = preg_split('/\s+/', trim($simulation->name ?? ''), -1, PREG_SPLIT_NO_EMPTY);
            if ($nameWords && count($nameWords) > 25) {
                $displayName = implode(' ', array_slice($nameWords, 0, 25)) . '...';
            } else {
                $displayName = $simulation->name;
            }
        @endphp
        <div class="sim-header">
            <div style="display: flex; align-items: center; gap: 16px;">
                <a href="{{ route('simulations.index') }}" class="btn btn-outline" style="display:inline-flex; align-items:center; gap:6px; padding:8px 12px;">
                    ← Back
                </a>
                <h1 style="margin:0; font-size:24px; font-weight:700; max-width: 520px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $displayName }}
                </h1>
            </div>
            <div style="display: flex; gap: 8px; align-items:center;">
                <form method="POST" action="{{ route('simulations.destroy', $simulation) }}" onsubmit="return confirm('Delete this simulation?');" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline">Delete</button>
                </form>
            </div>
        </div>

        <!-- Main Content: Metrics + Chart -->
        <div class="sim-main-content">
            <!-- Left: Key Metrics -->
            <div class="sim-metrics">
                <div class="metric-card">
                    <div class="metric-label">Current Value</div>
                    <div id="current-value" class="metric-value" style="color: var(--c-primary);">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Total Contributed</div>
                    <div id="total-contributed" class="metric-value">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Total Gain</div>
                    <div id="total-gain" class="metric-value" style="color: var(--c-primary);">€0.00</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Real Value (Inflation Adj.)</div>
                    <div id="real-value" class="metric-value" style="color: var(--c-secondary);">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Max Drawdown</div>
                    <div id="drawdown" class="metric-value" style="color:#ef4444;">0%</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Projected CAGR</div>
                    <div id="cagr" class="metric-value">0%</div>
                </div>
            </div>

            <!-- Center: Main Chart Area -->
            <div class="sim-chart-area">
                <div class="chart-container">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-shrink: 0; gap:16px; flex-wrap:wrap;">
                        <h2 style="margin:0; font-size:20px; font-weight:700;">Investment Growth Over Time</h2>
                        <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
                            <button id="btn-run" class="btn btn-primary">▶ Start</button>
                            <button id="btn-save" class="btn btn-outline" title="Save the latest simulation results">💾 Save</button>
                            <a class="btn btn-secondary" href="{{ route('simulations.edit', $simulation) }}">Edit</a>
                            <div id="status-display" style="padding:6px 12px; border-radius:999px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border); font-size:12px; font-weight:600;">
                                Ready
                            </div>
                        </div>
                    </div>
                    <div class="chart-canvas-wrapper">
                        <canvas id="sim-chart" aria-label="Simulation chart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom: Collapsible Controls & Parameters -->
        <div class="sim-controls-bottom">
            <!-- Controls Toggle -->
            <div class="controls-toggle" onclick="toggleControls()">
                <span>⚙️ Simulation Controls & Parameters</span>
                <span id="controls-arrow" style="transition: transform 0.3s;">▲</span>
            </div>
            
            <!-- Collapsible Content -->
            <div id="controls-content" class="controls-content expanded">
                <div class="controls-inner">
                    <div style="display: grid; grid-template-columns: auto 1fr; gap: 20px; align-items: start;">
                        <!-- Controls -->
                        <div style="min-width:320px;">
                            <h3 style="margin:0 0 16px; font-size:16px; font-weight:700;">Controls</h3>
                            <div style="display:grid; gap:12px; margin-bottom:16px;">
                                <label style="display:grid; gap:6px;">
                                    <span style="font-weight:600; font-size:13px;">Duration (months)</span>
                                    <input id="months-input" type="number" min="12" max="600" step="12" value="120" class="footer-email-input" />
                                </label>
                                <label style="display:grid; gap:6px;">
                                    <span style="font-weight:600; font-size:13px;">Speed (seconds per step)</span>
                                    <input id="speed-input" type="number" min="0.1" max="10" step="0.1" value="0.25" class="footer-email-input" />
                                </label>
                                <label style="display:grid; gap:6px;">
                                    <span style="font-weight:600; font-size:13px;">Market Regime</span>
                                    <select id="preset-select" class="footer-email-input">
                                        <option value="balanced">Balanced (default)</option>
                                        <option value="growth">Growth / Bullish</option>
                                        <option value="defensive">Defensive / Bearish</option>
                                        <option value="volatile">Choppy & volatile</option>
                                        <option value="shock">Stress test (crash + recovery)</option>
                                    </select>
                                </label>
                            </div>
                            <div style="display:flex; flex-wrap:wrap; gap:8px;">
                                <button id="btn-step" class="btn btn-secondary" title="Advance by one month">➜ Step</button>
                                <button id="btn-pause" class="btn btn-secondary" disabled>⏸ Pause</button>
                                <button id="btn-reset" class="btn btn-outline">🔄 Reset</button>
                            </div>
                            <div id="save-status" style="margin-top:12px; font-size:12px; color:var(--c-on-surface-2);">Not saved yet</div>
                        </div>

                        <!-- Parameters -->
                        <div>
                            <h3 style="margin:0 0 16px; font-size:16px; font-weight:700;">Simulation Parameters</h3>
                            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:12px;">
                                <div style="padding:12px; border-radius:8px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                                    <span style="color: var(--c-on-surface-2); font-size:12px; display:block; margin-bottom:4px;">Initial Investment</span>
                                    <span style="font-size:18px; font-weight:700;">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</span>
                                </div>
                                <div style="padding:12px; border-radius:8px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                                    <span style="color: var(--c-on-surface-2); font-size:12px; display:block; margin-bottom:4px;">Monthly Contribution</span>
                                    <span style="font-size:18px; font-weight:700;">€{{ number_format($simulation->settings['monthlyContribution'], 2) }}</span>
                                </div>
                                <div style="padding:12px; border-radius:8px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                                    <span style="color: var(--c-on-surface-2); font-size:12px; display:block; margin-bottom:4px;">Annual Growth Rate</span>
                                    <span style="font-size:18px; font-weight:700;">{{ number_format($simulation->settings['growthRate'] * 100, 2) }}%</span>
                                </div>
                                <div style="padding:12px; border-radius:8px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                                    <span style="color: var(--c-on-surface-2); font-size:12px; display:block; margin-bottom:4px;">Inflation Rate</span>
                                    <span style="font-size:18px; font-weight:700;">{{ number_format($simulation->settings['inflationRate'] * 100, 2) }}%</span>
                                </div>
                                <div style="padding:12px; border-radius:8px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                                    <span style="color: var(--c-on-surface-2); font-size:12px; display:block; margin-bottom:4px;">Risk Appetite</span>
                                    <span style="font-size:18px; font-weight:700;">{{ number_format($simulation->settings['riskAppetite'] * 100, 0) }}%</span>
                                </div>
                                <div style="padding:12px; border-radius:8px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                                    <span style="color: var(--c-on-surface-2); font-size:12px; display:block; margin-bottom:4px;">Market Influence</span>
                                    <span style="font-size:18px; font-weight:700;">{{ number_format($simulation->settings['marketInfluence'] * 100, 0) }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        function toggleControls() {
            const content = document.getElementById('controls-content');
            const arrow = document.getElementById('controls-arrow');
            const isExpanded = content.classList.contains('expanded');
            if (isExpanded) {
                content.classList.remove('expanded');
                arrow.textContent = '▼';
                arrow.style.transform = 'rotate(0deg)';
            } else {
                content.classList.add('expanded');
                arrow.textContent = '▲';
                arrow.style.transform = 'rotate(180deg)';
            }
        }
        </script>
    </div>
    @endif
</div>
@endsection

@if(isset($simulation))
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
    const learningNoteEl = null; // Removed from new layout
    const riskTipEl = null; // Removed from new layout
    const eventLogEl = null; // Removed from new layout
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

    // Currency preference - fetch from API
    const defaultRates = {
        EUR: 1,
        USD: 1.08,
        GBP: 0.86,
        JPY: 162.5,
    };

    let currencyRates = { ...defaultRates };

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
                currencyRates = {
                    EUR: 1,
                    USD: data.rates.USD || defaultRates.USD,
                    GBP: data.rates.GBP || defaultRates.GBP,
                    JPY: data.rates.JPY || defaultRates.JPY,
                };
                
                // Store rates with timestamp
                try {
                    localStorage.setItem('nosleguma-currency-rates', JSON.stringify({
                        rates: currencyRates,
                        timestamp: Date.now()
                    }));
                } catch (e) {}
                
                // Update chart if it exists
                if (typeof rebuildChartData === 'function') {
                    rebuildChartData();
                    updateSummary();
                }
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
                        currencyRates = parsed.rates || defaultRates;
                        return true;
                    }
                }
            } catch (e) {}
            currencyRates = { ...defaultRates };
            return false;
        }
        return false;
    }

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

    // Fetch rates on load
    fetchExchangeRates();

    // Re-fetch rates every hour
    setInterval(fetchExchangeRates, 60 * 60 * 1000);

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
        if (statusDisplay) {
            statusDisplay.textContent = 'Ready';
            statusDisplay.style.background = 'color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%)';
        }
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
        if (!isRunning && statusDisplay) {
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
@endif

@include('components.currency-script')
@include('components.tutorial', ['currentPage' => 'dashboard'])
