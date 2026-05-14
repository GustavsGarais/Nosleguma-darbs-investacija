@extends('layouts.dashboard')

@php
    $defaultRunnerMode = ($simulation->settings['defaultRunnerMode'] ?? 'classic') === 'playground' ? 'playground' : 'classic';
    $simulationRunnerConfig = [
        'snapshotUrl' => route('simulations.snapshot', $simulation, false),
        'runnerStateUrl' => route('simulations.runner-state', $simulation, false),
        'csrfToken' => csrf_token(),
        'settings' => $simulation->settings,
        'savedRunner' => $simulation->data['runner'] ?? null,
        'savedHistory' => $simulation->data['history'] ?? null,
        'savedSnapshot' => $simulation->data['snapshot'] ?? null,
        'i18n' => [
            'ready' => __('Ready'),
            'running' => __('Running...'),
            'paused' => __('Paused'),
            'complete' => __('Complete'),
            'month' => __('Day :current / :total'),
            'saving' => __('Saving…'),
            'savedAt' => __('Saved :time'),
            'saveFailed' => __('Save failed. Try again.'),
            'noEvents' => __('No notable events yet. Run or step the simulation.'),
            'marketShock' => __('Market shock: :pct% day (:label)'),
            'newHigh' => __('New portfolio high reached on day :month.'),
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
            'investorsHigh' => __('Many investors (crowd) are active: profit-taking and panic waves can amplify swings.'),
            'inflHigh' => __('Inflation is elevated; compare nominal vs real value to see purchasing power.'),
            'inflMod' => __('Inflation is moderate; compounding still beats it over time.'),
            'riskDefault' => __('Use Step mode to see how each day contributes to long-term results.'),
            'chartNominal' => __('Portfolio value'),
            'chartReal' => __('Real value (inflation-adjusted)'),
            'chartContributed' => __('Total you put in'),
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
            'playgroundHelp' => __('Buy adds money into the investment. Sell moves part of the investment back to your cash wallet. The orange line is profit/loss compared to how much you put in (it can go below 0 = loss).'),
            'playgroundNextStep' => __('Hands-on mode uses Step in the toolbar: each day reprices your portfolio and redraws the chart. Use the trading desk to buy or sell, then tap Step.'),
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
            'xAxisDay' => __('Day'),
            'yAxisValue' => __('Value (:currency)'),
        ],
        'terminal' => [
            'modeAuto' => __('sim.terminal.mode_auto'),
            'modeHands' => __('sim.terminal.mode_hands'),
            'railSharpeNa' => '—',
            'allocCash' => __('sim.terminal.alloc_cash'),
            'allocInvested' => __('sim.terminal.alloc_invested'),
            'allocGrowth' => __('sim.terminal.alloc_growth'),
            'allocContrib' => __('sim.terminal.alloc_contrib'),
        ],
    ];
@endphp

@section('title', $simulation->name)

@section('dashboard_content')
<section class="sim-run-shell sim-run-shell--terminal" aria-label="Simulation details">
    <div class="sim-dash-toolbar" aria-label="{{ __('Simulation actions') }}">
        <div class="sim-dash-toolbar-lead">
            <h1 class="sim-dash-toolbar__title">{{ $simulation->name }}</h1>
        </div>
        <div class="sim-dash-toolbar-modes" role="group" aria-label="{{ __('Simulation mode') }}">
            <button type="button" id="sim-mode-pill-classic" class="sim-mode-pill @if($defaultRunnerMode === 'classic') is-active @endif" data-sim-mode="classic" aria-pressed="{{ $defaultRunnerMode === 'classic' ? 'true' : 'false' }}">
                {{ __('sim.terminal.mode_auto') }}
            </button>
            <button type="button" id="sim-mode-pill-playground" class="sim-mode-pill @if($defaultRunnerMode === 'playground') is-active @endif" data-sim-mode="playground" aria-pressed="{{ $defaultRunnerMode === 'playground' ? 'true' : 'false' }}">
                {{ __('sim.terminal.mode_hands') }}
            </button>
        </div>
        <div class="sim-dash-toolbar-actions">
            <button
                id="btn-run"
                class="btn btn-primary"
                type="button"
                data-label-run="{{ __('Run') }}"
                data-label-pause="{{ __('Pause') }}"
                data-icon-run="▶"
                data-icon-pause="⏸"
                aria-pressed="false"
            >▶ {{ __('Run') }}</button>
            <button id="btn-step" class="btn btn-secondary" type="button" title="{{ __('Advance by one day') }}">➜ {{ __('Step') }}</button>
            <button id="btn-reset" class="btn btn-outline" type="button">🔄 {{ __('Reset') }}</button>
            <button id="btn-save" class="btn btn-outline" type="button" title="{{ __('Save results and full monthly history to the server') }}">💾 {{ __('Save') }}</button>
        </div>
        <div class="sim-dash-toolbar-secondary">
            <button id="start-tutorial" class="btn btn-outline btn-sm" type="button">📚 {{ __('Start Tutorial') }}</button>
            <x-help-sheet id="sim-help-sheet" :title="__('Simulation help')" :button-label="__('Open help')">
                <h3>{{ __('Quick start') }}</h3>
                <ul>
                    <li>{{ __('Pick a mode (Classic or Hands-on).') }}</li>
                    <li>{{ __('Set Duration and Speed, then use Step to learn slowly.') }}</li>
                    <li>{{ __('Use Run to watch the full path and Save to keep results.') }}</li>
                </ul>

                <h3>{{ __('Market Regime') }}</h3>
                <p>
                    {{ __('Market Regime changes the “behavior pattern” of returns: drift (average direction), volatility (swing size), and rare shock events. Your inputs stay the same; only the market behavior changes.') }}
                </p>
                <ul>
                    <li>{{ __('Balanced: typical long-run growth with moderate swings.') }}</li>
                    <li>{{ __('Growth/Bullish: higher drift, still volatile.') }}</li>
                    <li>{{ __('Defensive/Bearish: lower drift with deeper drawdowns.') }}</li>
                    <li>{{ __('Choppy & volatile: bigger swings even if averages look similar.') }}</li>
                    <li>{{ __('Stress test: rare crash + recovery behavior.') }}</li>
                </ul>

                <h3>{{ __('Reading the chart') }}</h3>
                <p>
                    {{ __('Compare “Total you put in” vs portfolio value to see break-even. Real value shows purchasing power after inflation.') }}
                </p>
                <p>
                    {!! __('Arrow tip: under the big numbers, <span style="color:var(--c-primary); font-weight:800;">green ↑</span> means that number went up since the previous step, and <span style="color:#dc2626; font-weight:800;">red ↓</span> means it went down. The chart legend lines are separate.') !!}
                </p>
            </x-help-sheet>
            <a class="btn btn-primary btn-sm" href="{{ route('simulations.edit', $simulation) }}">{{ __('Edit') }}</a>
            <a class="btn btn-outline btn-sm" href="{{ route('simulations.index') }}">{{ __('Back') }}</a>
            <form class="sim-dash-toolbar__delete-form" method="POST" action="{{ route('simulations.destroy', $simulation) }}" onsubmit="return confirm('{{ __('Delete this simulation?') }}');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline btn-sm">{{ __('Delete') }}</button>
            </form>
        </div>
        <div class="sim-dash-toolbar-status">
            <div id="status-display" class="sim-dash-toolbar-status__badge">
                {{ __('Ready') }}
            </div>
            <span id="save-status" class="sim-dash-toolbar-status__meta">{{ __('Not saved yet') }}</span>
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
            <div class="sim-dash-controls__scroller">
            <div class="sim-dash-controlsBlock sim-dash-settings-panel">
                <div class="sim-dash-settings-panel__head">
                    <h3 class="sim-dash-settings-panel__heading">{{ __('Simulation Controls') }}</h3>
                    @include('simulations.partials.section-help', [
                        'tooltip' => __('sim.tooltip.simulation_controls'),
                        'label' => __('sim.help.simulation_controls'),
                    ])
                </div>
                <fieldset class="sr-only" aria-hidden="true" tabindex="-1">
                    <legend>{{ __('Simulation mode') }}</legend>
                    <input type="radio" name="sim-mode" id="mode-classic" value="classic" @checked($defaultRunnerMode === 'classic') />
                    <input type="radio" name="sim-mode" id="mode-playground" value="playground" @checked($defaultRunnerMode === 'playground') />
                </fieldset>
                <div style="display:grid; gap:10px;">
                    <label style="display:grid; gap:6px;">
                        <span style="font-weight:700;">{{ __('Duration (days)') }}</span>
                        <input id="months-input" type="number" min="365" max="7300" step="365" value="3650" class="footer-email-input" />
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

            <section id="playground-panel" class="sim-dash-controlsBlock sim-dash-hands-on sim-playground-flyout" tabindex="-1" hidden aria-label="{{ __('Trading desk') }}">
                <div class="sim-dash-hands-on__head">
                    <h3 class="sim-dash-hands-on__title">{{ __('sim.terminal.hands_on_title') }}</h3>
                    @include('simulations.partials.section-help', [
                        'tooltip' => __('sim.tooltip.trading_hands_on'),
                        'label' => __('sim.help.trading_hands_on'),
                    ])
                </div>
                <p id="playground-next-step" class="sim-dash-hands-on__hint" role="status" aria-live="polite"></p>
                <div class="sim-playground-panel__inner">
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
                            <div class="sim-playground-actions__titleRow">
                                <span class="sim-playground-actions__title">{{ __('Add to position') }}</span>
                            </div>
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
                            <div class="sim-playground-actions__titleRow">
                                <span class="sim-playground-actions__title">{{ __('Sell') }}</span>
                                @include('simulations.partials.section-help', [
                                    'tooltip' => __('sim.tooltip.trading_buy_sell'),
                                    'label' => __('sim.help.trading_buy_sell'),
                                ])
                            </div>
                            <div class="sim-playground-actions__btns">
                                <button type="button" class="btn btn-outline btn-sm pg-sell" data-fraction="0.25">25%</button>
                                <button type="button" class="btn btn-outline btn-sm pg-sell" data-fraction="0.5">50%</button>
                                <button type="button" class="btn btn-outline btn-sm pg-sell" data-fraction="1">{{ __('All') }}</button>
                            </div>
                        </div>
                    </div>
                    <p id="playground-realized" class="sim-playground-realized"></p>
                </div>
            </section>

            </div>
            </aside>
        </div>
        </div>
        </div>

        <div
            class="sim-dash-resize sim-dash-resize--lead"
            role="separator"
            aria-orientation="vertical"
            aria-label="{{ __('sim.terminal.drag_resize_panels') }}"
            tabindex="0"
            data-sim-resize-edge="lead"
        ></div>

        <div class="sim-dash-work sim-dash-terminal-center">
            <div class="sim-dash-chartCol">
            <div class="sim-dash-chartCard" aria-label="Chart">
                <div class="sim-chart-hero">
                    <div class="sim-chart-hero__main">
                        <p class="sim-chart-hero__eyebrow">{{ __('sim.terminal.portfolio_value') }}</p>
                        <p id="current-value" class="sim-chart-hero__value">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
                        <p id="current-value-meta" class="sim-chart-hero__meta"></p>
                    </div>
                    <div class="sim-chart-hero__side">
                        <p class="sim-chart-hero__eyebrow">{{ __('sim.terminal.total_return') }}</p>
                        <p id="sim-hero-return-pct" class="sim-chart-hero__pct">—</p>
                        <p class="sim-chart-hero__eyebrow">{{ __('sim.terminal.step_change') }}</p>
                        <p id="sim-hero-step-delta" class="sim-chart-hero__sub">—</p>
                    </div>
                </div>
                <div class="sim-chart-range" id="sim-chart-range" role="toolbar" aria-label="{{ __('Investment growth over time') }}">
                    <button type="button" class="sim-chart-range__btn" data-range="all">{{ __('sim.terminal.chart_range_all') }}</button>
                    <button type="button" class="sim-chart-range__btn" data-range="12m">{{ __('sim.terminal.chart_range_12m') }}</button>
                    <button type="button" class="sim-chart-range__btn" data-range="6m">{{ __('sim.terminal.chart_range_6m') }}</button>
                    <button type="button" class="sim-chart-range__btn" data-range="3m">{{ __('sim.terminal.chart_range_3m') }}</button>
                    <button type="button" class="sim-chart-range__btn is-active" data-range="1m">{{ __('sim.terminal.chart_range_1m') }}</button>
                    <button type="button" id="sim-chart-reset-zoom" class="sim-chart-range__btn sim-chart-range__btn--ghost">{{ __('sim.terminal.reset_zoom') }}</button>
                </div>
                <h2 class="sim-dash-chartTitle sim-dash-chartTitle--compact">{{ __('Investment growth over time') }}</h2>
                <div class="sim-run-chartWrap">
                    <canvas id="sim-chart" aria-label="Simulation chart"></canvas>
                </div>
            </div>
            </div>
        </div>

        <div
            class="sim-dash-resize sim-dash-resize--rail"
            role="separator"
            aria-orientation="vertical"
            aria-label="{{ __('sim.terminal.drag_resize_panels') }}"
            tabindex="0"
            data-sim-resize-edge="rail"
        ></div>

        <aside class="sim-dash-rail" aria-label="{{ __('sim.terminal.rail_summary') }}">
            <div class="sim-rail-card">
                <h3 class="sim-rail-card__title">{{ __('sim.terminal.rail_summary') }}</h3>
                <div class="sim-rail-kpiGrid">
                    <div class="sim-rail-kpi">
                        <p class="sim-kpiLabel">{{ __('Total Contributed') }}</p>
                        <p id="total-contributed" class="sim-kpiValue">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
                        <p id="total-contributed-meta" class="sim-kpiMeta"></p>
                    </div>
                    <div class="sim-rail-kpi">
                        <p class="sim-kpiLabel">{{ __('Total Gain') }}</p>
                        <p id="total-gain" class="sim-kpiValue" style="color: var(--c-primary);">€0.00</p>
                        <p id="total-gain-meta" class="sim-kpiMeta"></p>
                    </div>
                    <div class="sim-rail-kpi">
                        <p class="sim-kpiLabel">{{ __('Real Value (Inflation Adj.)') }}</p>
                        <p id="real-value" class="sim-kpiValue" style="color: var(--c-secondary);">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
                        <p id="real-value-meta" class="sim-kpiMeta"></p>
                    </div>
                    <div class="sim-rail-kpi">
                        <p class="sim-kpiLabel">{{ __('Max Drawdown') }}</p>
                        <p id="drawdown" class="sim-kpiValue" style="color:#ef4444;">0%</p>
                        <p id="drawdown-meta" class="sim-kpiMeta"></p>
                    </div>
                    <div class="sim-rail-kpi">
                        <p class="sim-kpiLabel">{{ __('Projected CAGR') }}</p>
                        <p id="cagr" class="sim-kpiValue">0%</p>
                        <p id="cagr-meta" class="sim-kpiMeta"></p>
                    </div>
                </div>
            </div>
            <div
                class="sim-rail-card"
                id="sim-rail-allocation-card"
                @if($defaultRunnerMode === 'classic') hidden @endif
                aria-hidden="{{ $defaultRunnerMode === 'playground' ? 'false' : 'true' }}"
            >
                <h3 class="sim-rail-card__title">{{ __('sim.terminal.rail_allocation') }}</h3>
                <div class="sim-rail-donutWrap">
                    <canvas id="sim-allocation-chart" height="160" aria-label="{{ __('sim.terminal.rail_allocation') }}"></canvas>
                </div>
            </div>
            <div class="sim-rail-card">
                <h3 class="sim-rail-card__title">{{ __('sim.terminal.rail_performance') }}</h3>
                <dl class="sim-rail-perf">
                    <div><dt>{{ __('sim.terminal.best_step') }}</dt><dd id="sim-perf-best">—</dd></div>
                    <div><dt>{{ __('sim.terminal.worst_step') }}</dt><dd id="sim-perf-worst">—</dd></div>
                    <div><dt>{{ __('sim.terminal.sharpe') }}</dt><dd id="sim-perf-sharpe">—</dd></div>
                    <div><dt>{{ __('sim.terminal.total_trades') }}</dt><dd id="sim-perf-trades">0</dd></div>
                    <div><dt>{{ __('sim.terminal.win_rate') }}</dt><dd id="sim-perf-winrate">—</dd></div>
                </dl>
            </div>
        </aside>
    </div>

    <div class="sim-dash-terminal__footer">
    <details class="sim-accordion" open>
        <summary aria-label="{{ __('Market Events & Teaching Moments') }}">
            <span class="sim-accordion__summary-left">
                <span class="sim-accordion__title">{{ __('Market Events & Teaching Moments') }}</span>
                @include('simulations.partials.section-help', [
                    'tooltip' => __('sim.tooltip.market_events'),
                    'label' => __('sim.help.market_events'),
                ])
            </span>
            <span class="sim-accordion__sub">{{ __('Highlights as you run') }}</span>
        </summary>
        <div class="sim-accordionBody">
            <ul id="event-log" style="margin:0; padding-left:18px; display:grid; gap:8px; font-size:14px; color:var(--c-on-surface-2);"></ul>
        </div>
    </details>

    <details class="sim-accordion">
        <summary aria-label="{{ __('Simulation Parameters') }}">
            <span class="sim-accordion__summary-left">
                <span class="sim-accordion__title">{{ __('Simulation Parameters') }}</span>
                @include('simulations.partials.section-help', [
                    'tooltip' => __('sim.tooltip.simulation_parameters'),
                    'label' => __('sim.help.simulation_parameters'),
                ])
            </span>
            <span class="sim-accordion__sub">{{ __('What you assumed') }}</span>
        </summary>
        <div class="sim-accordionBody">
            <div
                style="
                    display:grid;
                    grid-template-columns: repeat(6, minmax(0, 1fr));
                    gap:10px;
                "
            >
                <div style="padding:10px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:13px;">{{ __('Initial Investment') }}</span>
                    <p style="margin:4px 0 0; font-size:16px; font-weight:800;">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
                </div>
                <div style="padding:10px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:13px;">{{ __('Monthly Contribution') }}</span>
                    <p style="margin:4px 0 0; font-size:16px; font-weight:800;">€{{ number_format($simulation->settings['monthlyContribution'], 2) }}</p>
                </div>
                <div style="padding:10px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:13px;">{{ __('Annual Growth Rate') }}</span>
                    <p style="margin:4px 0 0; font-size:16px; font-weight:800;">{{ number_format($simulation->settings['growthRate'] * 100, 2) }}%</p>
                </div>
                <div style="padding:10px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:13px;">{{ __('Inflation Rate') }}</span>
                    <p style="margin:4px 0 0; font-size:16px; font-weight:800;">{{ number_format($simulation->settings['inflationRate'] * 100, 2) }}%</p>
                </div>
                <div style="padding:10px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:13px;">{{ __('Risk Appetite') }}</span>
                    <p style="margin:4px 0 0; font-size:16px; font-weight:800;">{{ number_format($simulation->settings['riskAppetite'] * 100, 0) }}%</p>
                </div>
                <div style="padding:10px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:13px;">{{ __('Market Influence') }}</span>
                    <p style="margin:4px 0 0; font-size:16px; font-weight:800;">{{ number_format($simulation->settings['marketInfluence'] * 100, 0) }}%</p>
                </div>
            </div>
        </div>
    </details>
    </div>
</section>

<script type="application/json" id="simulation-runner-config">@json($simulationRunnerConfig)</script>
@include('components.tutorial', ['currentPage' => 'show'])
@include('components.currency-script')
@endsection

@push('scripts')
    @vite(['resources/js/simulation-runner.js'])
@endpush
