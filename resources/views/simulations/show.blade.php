@extends('layouts.dashboard')

@php
    $simulationRunnerConfig = [
        'snapshotUrl' => route('simulations.snapshot', $simulation),
        'runnerStateUrl' => route('simulations.runner-state', $simulation),
        'csrfToken' => csrf_token(),
        'settings' => $simulation->settings,
        'savedRunner' => $simulation->data['runner'] ?? null,
        'i18n' => [
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
            'balancedLesson' => __('Balanced portfolios rely on regular contributions and modest volatility. Focus on time in the market.'),
            'growthLabel' => __('Growth / Bullish'),
            'growthLesson' => __('Growth tilt: higher expected return but bigger swings. Stick to a plan when volatility hits.'),
            'defensiveLabel' => __('Defensive / Bearish'),
            'defensiveLesson' => __('Defensive stance tempers losses but can lag in bull markets. Contributions matter more.'),
            'volatileLabel' => __('Choppy & volatile'),
            'volatileLesson' => __('Choppy markets teach discipline: expect whiplash and focus on long-term averages.'),
            'shockLabel' => __('Stress test (crash + recovery)'),
            'shockLesson' => __('Stress test simulates rare tail events and recovery. Use it to think about resilience, not timing.'),
            'stayInvested' => __('Stay invested and watch how contributions and volatility interact.'),
            'riskHigh' => __('High risk appetite means larger swings. Keep an emergency fund outside this simulation.'),
            'riskMarket' => __('Strong market influence toggled: external shocks will matter more. Rebalance if needed.'),
            'inflHigh' => __('Inflation is elevated; compare nominal vs real value to see purchasing power.'),
            'inflMod' => __('Inflation is moderate; compounding still beats it over time.'),
            'riskDefault' => __('Use Step mode to see how each month contributes to long-term results.'),
            'chartNominal' => __('Portfolio value'),
            'chartReal' => __('Real value (inflation-adjusted)'),
            'chartContributed' => __('Total contributed'),
            'chartCompare' => __('Alternative: extra monthly'),
            'chartSor' => __('Same returns, reversed order'),
            'secondaryLabel' => __('Second scenario'),
            'secondaryNone' => __('None (single path)'),
            'secondaryCompare' => __('Invest €100 / month more'),
            'secondarySor' => __('Sequence-of-returns (reversed)'),
            'compareExtraHint' => __('Extra € per month vs your baseline.'),
            'fatTailEvent' => __('Rare tail event (~:pct% this month)'),
            'crowdSelling' => __('Heavy selling from other investors weighed on prices (~:pct% crowd drag).'),
            'crowdBuying' => __('Broad buying from other investors helped prices (~:pct% crowd lift).'),
            'thisMonth' => __('this month'),
            'vsContributed' => __('vs contributed'),
            'fromPeak' => __('from peak'),
            'onContributed' => __('on total contributed'),
            'mom' => __('MoM'),
            'cagr' => __('CAGR'),
            'compareExplainer' => __('Second line adds your extra monthly amount with the same monthly returns as the base scenario.'),
            'sorExplainer' => __('Blue line uses the same return magnitudes in reverse order — average return matches, ending wealth usually does not.'),
            'simModeClassic' => __('Classic (auto monthly)'),
            'simModePlayground' => __('Hands-on portfolio lab'),
            'simulationMode' => __('Simulation mode'),
            'playgroundHelp' => __('Buy with extra cash or sell to move money into your wallet. The orange line is your net result versus every euro you ever contributed — it can dip below zero.'),
            'playgroundNextStep' => __('Use Step or Run in the bar above: each month reprices your holdings and redraws the chart.'),
            'playgroundBuyHint' => __('Add to position'),
            'playgroundCustomAdd' => __('Custom amount'),
            'playgroundSellHint' => __('Sell part of holdings (priced at current month)'),
            'playgroundBought' => __('Added :amount — more shares at current price.'),
            'playgroundSold' => __('Sold :pct% of position. This trade P/L: :gain.'),
            'chartNetWorth' => __('Net worth (cash + invested)'),
            'chartTotalPL' => __('Total gain/loss vs contributed'),
            'playgroundLesson' => __('Try selling after a peak versus after a dip to see how timing changes locked-in gains. This is a teaching model, not live trading.'),
            'playgroundRealizedLabel' => __('Realized P/L (closed trades)'),
            'modeLabel' => __('Mode'),
        ],
    ];
@endphp

@section('title', $simulation->name)

@section('dashboard_content')
<section class="sim-run-shell" aria-label="Simulation details">
    <header class="auth-card sim-dash-header" aria-label="Simulation header" style="padding:18px 20px;">
        <div style="min-width:240px;">
            <h1 style="margin:0 0 6px;">{{ $simulation->name }}</h1>
            <p style="margin:0; color:var(--c-on-surface-2); font-size:13px;">
                {{ __('Charts show portfolio vs contributions (break-even). Enable a second scenario to compare decisions or sequence-of-returns risk.') }}
            </p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button id="start-tutorial" class="btn btn-secondary" type="button">📚 {{ __('Start Tutorial') }}</button>
            <a class="btn btn-primary" href="{{ route('simulations.edit', $simulation) }}">{{ __('Edit') }}</a>
            <a class="btn btn-outline" href="{{ route('simulations.index') }}">{{ __('Back') }}</a>
            <form method="POST" action="{{ route('simulations.destroy', $simulation) }}" onsubmit="return confirm('{{ __('Delete this simulation?') }}');" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline">{{ __('Delete') }}</button>
            </form>
        </div>
    </header>

    <div class="sim-dash-toolbar" aria-label="Simulation actions">
        <div class="sim-dash-toolbar-actions">
            <button id="btn-run" class="btn btn-primary" type="button">▶ {{ __('Run') }}</button>
            <button id="btn-pause" class="btn btn-secondary" type="button" disabled>⏸ {{ __('Pause') }}</button>
            <button id="btn-step" class="btn btn-secondary" type="button" title="{{ __('Advance by one month') }}">➜ {{ __('Step') }}</button>
            <button id="btn-reset" class="btn btn-outline" type="button">🔄 {{ __('Reset') }}</button>
            <button id="btn-save" class="btn btn-outline" type="button" title="{{ __('Save results and full monthly history to the server') }}">💾 {{ __('Save') }}</button>
            <button
                id="btn-toggle-controls"
                class="btn btn-outline"
                type="button"
                aria-expanded="true"
                aria-controls="sim-dash-lead"
                title="{{ __('Hide controls panel') }}"
                data-title-hide="{{ __('Hide controls panel') }}"
                data-title-show="{{ __('Show controls panel') }}"
            >⏴ {{ __('Controls') }}</button>
        </div>
        <div class="sim-dash-toolbar-status">
            <div id="status-display" style="padding:8px 12px; border-radius:10px; border:1px solid var(--c-border); background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); font-weight:700; font-size:13px;">
                {{ __('Ready') }}
            </div>
            <span id="save-status" style="font-size:13px; color:var(--c-on-surface-2); white-space:nowrap;">{{ __('Not saved yet') }}</span>
        </div>
    </div>

    <div class="sim-dash-body">
        <div class="sim-dash-lead" id="sim-dash-lead">
        <div class="sim-dash-flyout-stack">
        <div class="sim-controls-flyout sim-controls-flyout--lead" tabindex="-1">
            <div class="sim-controls-flyout__rail" aria-hidden="true">
                <span class="sim-controls-flyout__icon" aria-hidden="true">⚙</span>
                <span class="sim-controls-flyout__label">{{ __('Controls') }}</span>
            </div>
            <aside class="sim-dash-controls sim-controls-flyout__panel" aria-label="{{ __('Simulation Controls') }}">
            <div class="sim-dash-controlsBlock auth-card" style="padding:16px; box-shadow:none;">
                <h3>{{ __('Simulation Controls') }}</h3>
                <fieldset style="border:none; padding:0; margin:0 0 12px;">
                    <legend class="sr-only">{{ __('Simulation mode') }}</legend>
                    <span style="display:block; font-weight:700; margin-bottom:8px;">{{ __('Mode') }}</span>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="radio" name="sim-mode" id="mode-classic" value="classic" checked />
                            <span>{{ __('Classic (auto monthly)') }}</span>
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="radio" name="sim-mode" id="mode-playground" value="playground" />
                            <span>{{ __('Hands-on portfolio lab') }}</span>
                        </label>
                    </div>
                </fieldset>
                <div style="display:grid; gap:10px;">
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
                    <div id="classic-secondary-wrap">
                    <label style="display:grid; gap:6px;">
                        <span style="font-weight:700;">{{ __('Second scenario') }}</span>
                        <select id="secondary-scenario" class="footer-email-input">
                            <option value="none">{{ __('None (single path)') }}</option>
                            <option value="compare">{{ __('Invest €100 / month more') }}</option>
                            <option value="sor">{{ __('Sequence-of-returns (reversed)') }}</option>
                        </select>
                    </label>
                    <label id="compare-extra-wrap" style="display:none; grid-template-columns:1fr; gap:6px;">
                        <span style="font-weight:700;">{{ __('Extra € per month vs your baseline.') }}</span>
                        <input id="compare-extra-monthly" type="number" min="0" step="10" value="100" class="footer-email-input" />
                    </label>
                    </div>
                </div>
            </div>

            <div class="sim-dash-controlsBlock auth-card" style="padding:16px; box-shadow:none;">
                <div style="display:grid; gap:10px;">
                    <div id="learning-note" style="padding:12px; border-radius:12px; border:1px solid var(--c-border); background:color-mix(in srgb, var(--c-surface) 90%, var(--c-primary) 10%); font-size:14px; line-height:1.6;"></div>
                    <div id="risk-tip" style="padding:12px; border-radius:12px; border:1px solid var(--c-border); background:color-mix(in srgb, var(--c-surface) 94%, var(--c-secondary) 6%); font-size:14px; line-height:1.6;"></div>
                </div>
            </div>
            </aside>
        </div>

        {{-- Trading desk stacked under Controls (desktop); hover desk hides controls rail for a single-column flyout. --}}
        <div class="sim-dash-deskSlot" id="sim-dash-main">
            <div id="playground-panel" class="sim-controls-flyout sim-playground-flyout" tabindex="-1" hidden aria-label="{{ __('Trading desk') }}">
                <div class="sim-controls-flyout__rail" aria-hidden="true">
                    <span class="sim-controls-flyout__icon" aria-hidden="true">◇</span>
                    <span class="sim-controls-flyout__label">{{ __('Trading desk') }}</span>
                </div>
                <aside class="sim-controls-flyout__panel sim-playground-panel" aria-label="{{ __('Trading desk') }}">
                    <div class="sim-playground-panel__inner">
                        <p class="sim-playground-flyout__cue">{{ __('When closed, the chart is wider. Hover or focus here to expand buy/sell.') }}</p>
                        <div class="sim-playground-deck__intro">
                            <p id="playground-help" class="sim-playground-deck__lead"></p>
                            <p id="playground-next-step" class="sim-playground-deck__hint"></p>
                        </div>
                        <div class="sim-playground-hud" aria-live="polite">
                            <div class="sim-playground-hud__tile">
                                <span class="sim-playground-hud__label">{{ __('Cash wallet') }}</span>
                                <span class="sim-playground-hud__value" id="playground-hud-cash">—</span>
                            </div>
                            <div class="sim-playground-hud__tile">
                                <span class="sim-playground-hud__label">{{ __('In the market') }}</span>
                                <span class="sim-playground-hud__value" id="playground-hud-invested">—</span>
                            </div>
                            <div class="sim-playground-hud__tile">
                                <span class="sim-playground-hud__label">{{ __('Unrealized on holdings') }}</span>
                                <span class="sim-playground-hud__value" id="playground-hud-unrealized">—</span>
                            </div>
                        </div>
                        <div class="sim-playground-actions">
                            <div class="sim-playground-actions__col">
                                <span class="sim-playground-actions__title">{{ __('Add to position') }}</span>
                                <div class="sim-playground-actions__btns">
                                    <button type="button" class="btn btn-secondary btn-sm pg-buy" data-amount="25">+25</button>
                                    <button type="button" class="btn btn-secondary btn-sm pg-buy" data-amount="50">+50</button>
                                    <button type="button" class="btn btn-secondary btn-sm pg-buy" data-amount="100">+100</button>
                                </div>
                                <label class="sim-playground-custom">
                                    <span class="sim-playground-custom__label">{{ __('Custom amount') }}</span>
                                    <div class="sim-playground-amount-row">
                                        <input id="playground-custom-amount" type="number" min="1" step="1" value="50" class="footer-email-input sim-playground-amount-input" />
                                        <button type="button" id="playground-custom-buy" class="btn btn-primary btn-sm sim-playground-amount-btn">{{ __('Add') }}</button>
                                    </div>
                                </label>
                            </div>
                            <div class="sim-playground-actions__col">
                                <span class="sim-playground-actions__title">{{ __('Sell') }}</span>
                                <p class="sim-playground-actions__note">{{ __('Sell part of holdings (priced at current month)') }}</p>
                                <div class="sim-playground-actions__btns">
                                    <button type="button" class="btn btn-outline btn-sm pg-sell" data-fraction="0.25">25%</button>
                                    <button type="button" class="btn btn-outline btn-sm pg-sell" data-fraction="0.5">50%</button>
                                    <button type="button" class="btn btn-outline btn-sm pg-sell" data-fraction="1">{{ __('All') }}</button>
                                </div>
                            </div>
                        </div>
                        <p id="playground-realized" class="sim-playground-realized"></p>
                    </div>
                </aside>
            </div>
        </div>
        </div>
        </div>

        <div class="sim-dash-work">
        <div class="sim-dash-chartCol">
            <div class="sim-dash-chartCard" aria-label="Chart">
                <h2 class="sim-dash-chartTitle">{{ __('Investment growth over time') }}</h2>
                <div class="sim-run-chartWrap">
                    <canvas id="sim-chart" aria-label="Simulation chart"></canvas>
                </div>
            </div>
        </div>
        </div>
    </div>

    <details class="auth-card sim-kpi-panel" aria-label="{{ __('Key indicators') }}">
        <summary class="sim-kpi-panel__summary">
            <span class="sim-kpi-panel__summary-text">
                <span class="sim-kpi-panel__title">{{ __('Key indicators') }}</span>
                <span class="sim-kpi-panel__subtitle">{{ __('Optional detail — open for numbers and month-over-month hints') }}</span>
            </span>
        </summary>
        <div class="sim-kpi-panel__body">
            <div class="sim-kpiGrid sim-kpis">
                <div class="sim-kpi">
                    <p class="sim-kpiLabel">{{ __('Current Value') }}</p>
                    <p id="current-value" class="sim-kpiValue" style="color: var(--c-primary);">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
                    <p id="current-value-meta" class="sim-kpiMeta"></p>
                </div>
                <div class="sim-kpi">
                    <p class="sim-kpiLabel">{{ __('Total Contributed') }}</p>
                    <p id="total-contributed" class="sim-kpiValue">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
                    <p id="total-contributed-meta" class="sim-kpiMeta"></p>
                </div>
                <div class="sim-kpi">
                    <p class="sim-kpiLabel">{{ __('Total Gain') }}</p>
                    <p id="total-gain" class="sim-kpiValue" style="color: var(--c-primary);">€0.00</p>
                    <p id="total-gain-meta" class="sim-kpiMeta"></p>
                </div>
                <div class="sim-kpi">
                    <p class="sim-kpiLabel">{{ __('Real Value (Inflation Adj.)') }}</p>
                    <p id="real-value" class="sim-kpiValue" style="color: var(--c-secondary);">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
                    <p id="real-value-meta" class="sim-kpiMeta"></p>
                </div>
                <div class="sim-kpi">
                    <p class="sim-kpiLabel">{{ __('Max Drawdown') }}</p>
                    <p id="drawdown" class="sim-kpiValue" style="color:#ef4444;">0%</p>
                    <p id="drawdown-meta" class="sim-kpiMeta"></p>
                </div>
                <div class="sim-kpi">
                    <p class="sim-kpiLabel">{{ __('Projected CAGR') }}</p>
                    <p id="cagr" class="sim-kpiValue">0%</p>
                    <p id="cagr-meta" class="sim-kpiMeta"></p>
                </div>
            </div>
        </div>
    </details>

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
</section>

<script type="application/json" id="simulation-runner-config">@json($simulationRunnerConfig)</script>
@include('components.tutorial', ['currentPage' => 'show'])
@include('components.currency-script')
@endsection

@push('scripts')
    @vite(['resources/js/simulation-runner.js'])
@endpush
