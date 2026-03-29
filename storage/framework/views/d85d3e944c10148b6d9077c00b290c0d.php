<?php
    $simulationRunnerConfig = [
        'snapshotUrl' => route('simulations.snapshot', $simulation),
        'csrfToken' => csrf_token(),
        'settings' => $simulation->settings,
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
            'thisMonth' => __('this month'),
            'vsContributed' => __('vs contributed'),
            'fromPeak' => __('from peak'),
            'onContributed' => __('on total contributed'),
            'mom' => __('MoM'),
            'cagr' => __('CAGR'),
            'compareExplainer' => __('Second line adds your extra monthly amount with the same monthly returns as the base scenario.'),
            'sorExplainer' => __('Blue line uses the same return magnitudes in reverse order — average return matches, ending wealth usually does not.'),
        ],
    ];
?>

<?php $__env->startSection('title', $simulation->name); ?>

<?php $__env->startSection('dashboard_content'); ?>
<section class="sim-run-shell" aria-label="Simulation details">
    <header class="auth-card sim-dash-header" aria-label="Simulation header" style="padding:18px 20px;">
        <div style="min-width:240px;">
            <h1 style="margin:0 0 6px;"><?php echo e($simulation->name); ?></h1>
            <p style="margin:0; color:var(--c-on-surface-2); font-size:13px;">
                <?php echo e(__('Charts show portfolio vs contributions (break-even). Enable a second scenario to compare decisions or sequence-of-returns risk.')); ?>

            </p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button id="start-tutorial" class="btn btn-secondary" type="button">📚 <?php echo e(__('Start Tutorial')); ?></button>
            <a class="btn btn-primary" href="<?php echo e(route('simulations.edit', $simulation)); ?>"><?php echo e(__('Edit')); ?></a>
            <a class="btn btn-outline" href="<?php echo e(route('simulations.index')); ?>"><?php echo e(__('Back')); ?></a>
            <form method="POST" action="<?php echo e(route('simulations.destroy', $simulation)); ?>" onsubmit="return confirm('<?php echo e(__('Delete this simulation?')); ?>');" style="display:inline;">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-outline"><?php echo e(__('Delete')); ?></button>
            </form>
        </div>
    </header>

    <div class="sim-dash-toolbar" aria-label="Simulation actions">
        <div class="sim-dash-toolbar-actions">
            <button id="btn-run" class="btn btn-primary" type="button">▶ <?php echo e(__('Run')); ?></button>
            <button id="btn-pause" class="btn btn-secondary" type="button" disabled>⏸ <?php echo e(__('Pause')); ?></button>
            <button id="btn-step" class="btn btn-secondary" type="button" title="<?php echo e(__('Advance by one month')); ?>">➜ <?php echo e(__('Step')); ?></button>
            <button id="btn-reset" class="btn btn-outline" type="button">🔄 <?php echo e(__('Reset')); ?></button>
            <button id="btn-save" class="btn btn-outline" type="button" title="<?php echo e(__('Save results and full monthly history to the server')); ?>">💾 <?php echo e(__('Save')); ?></button>
        </div>
        <div class="sim-dash-toolbar-status">
            <div id="status-display" style="padding:8px 12px; border-radius:10px; border:1px solid var(--c-border); background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); font-weight:700; font-size:13px;">
                <?php echo e(__('Ready')); ?>

            </div>
            <span id="save-status" style="font-size:13px; color:var(--c-on-surface-2); white-space:nowrap;"><?php echo e(__('Not saved yet')); ?></span>
        </div>
    </div>

    <div class="sim-dash-body">
        <div class="sim-dash-chartCard" aria-label="Chart">
            <h2 class="sim-dash-chartTitle"><?php echo e(__('Investment growth over time')); ?></h2>
            <div class="sim-run-chartWrap">
                <canvas id="sim-chart" aria-label="Simulation chart"></canvas>
            </div>
        </div>

        <aside class="sim-dash-controls" aria-label="Run controls">
            <div class="sim-dash-controlsBlock auth-card" style="padding:16px; box-shadow:none;">
                <h3><?php echo e(__('Simulation Controls')); ?></h3>
                <div style="display:grid; gap:10px;">
                    <label style="display:grid; gap:6px;">
                        <span style="font-weight:700;"><?php echo e(__('Duration (months)')); ?></span>
                        <input id="months-input" type="number" min="12" max="600" step="12" value="120" class="footer-email-input" />
                    </label>
                    <label style="display:grid; gap:6px;">
                        <span style="font-weight:700;"><?php echo e(__('Speed (seconds/step)')); ?></span>
                        <input id="speed-input" type="number" min="0.1" max="10" step="0.1" value="0.25" class="footer-email-input" />
                    </label>
                    <label style="display:grid; gap:6px;">
                        <span style="font-weight:700;"><?php echo e(__('Market Regime')); ?></span>
                        <select id="preset-select" class="footer-email-input">
                            <option value="balanced"><?php echo e(__('Balanced (default)')); ?></option>
                            <option value="growth"><?php echo e(__('Growth / Bullish')); ?></option>
                            <option value="defensive"><?php echo e(__('Defensive / Bearish')); ?></option>
                            <option value="volatile"><?php echo e(__('Choppy & volatile')); ?></option>
                            <option value="shock"><?php echo e(__('Stress test (crash + recovery)')); ?></option>
                        </select>
                    </label>
                    <label style="display:grid; gap:6px;">
                        <span style="font-weight:700;"><?php echo e(__('Second scenario')); ?></span>
                        <select id="secondary-scenario" class="footer-email-input">
                            <option value="none"><?php echo e(__('None (single path)')); ?></option>
                            <option value="compare"><?php echo e(__('Invest €100 / month more')); ?></option>
                            <option value="sor"><?php echo e(__('Sequence-of-returns (reversed)')); ?></option>
                        </select>
                    </label>
                    <label id="compare-extra-wrap" style="display:none; grid-template-columns:1fr; gap:6px;">
                        <span style="font-weight:700;"><?php echo e(__('Extra € per month vs your baseline.')); ?></span>
                        <input id="compare-extra-monthly" type="number" min="0" step="10" value="100" class="footer-email-input" />
                    </label>
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

    <section class="auth-card" aria-label="Summary" style="padding:18px 20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
            <h2 style="margin:0; font-size:1.1rem;"><?php echo e(__('Key indicators')); ?></h2>
            <span style="color:var(--c-on-surface-2); font-size:13px;"><?php echo e(__('Changes vs last month and vs total contributed')); ?></span>
        </div>
        <div class="sim-kpiGrid sim-kpis">
            <div class="sim-kpi">
                <p class="sim-kpiLabel"><?php echo e(__('Current Value')); ?></p>
                <p id="current-value" class="sim-kpiValue" style="color: var(--c-primary);">€<?php echo e(number_format($simulation->settings['initialInvestment'], 2)); ?></p>
                <p id="current-value-meta" class="sim-kpiMeta"></p>
            </div>
            <div class="sim-kpi">
                <p class="sim-kpiLabel"><?php echo e(__('Total Contributed')); ?></p>
                <p id="total-contributed" class="sim-kpiValue">€<?php echo e(number_format($simulation->settings['initialInvestment'], 2)); ?></p>
                <p id="total-contributed-meta" class="sim-kpiMeta"></p>
            </div>
            <div class="sim-kpi">
                <p class="sim-kpiLabel"><?php echo e(__('Total Gain')); ?></p>
                <p id="total-gain" class="sim-kpiValue" style="color: var(--c-primary);">€0.00</p>
                <p id="total-gain-meta" class="sim-kpiMeta"></p>
            </div>
            <div class="sim-kpi">
                <p class="sim-kpiLabel"><?php echo e(__('Real Value (Inflation Adj.)')); ?></p>
                <p id="real-value" class="sim-kpiValue" style="color: var(--c-secondary);">€<?php echo e(number_format($simulation->settings['initialInvestment'], 2)); ?></p>
                <p id="real-value-meta" class="sim-kpiMeta"></p>
            </div>
            <div class="sim-kpi">
                <p class="sim-kpiLabel"><?php echo e(__('Max Drawdown')); ?></p>
                <p id="drawdown" class="sim-kpiValue" style="color:#ef4444;">0%</p>
                <p id="drawdown-meta" class="sim-kpiMeta"></p>
            </div>
            <div class="sim-kpi">
                <p class="sim-kpiLabel"><?php echo e(__('Projected CAGR')); ?></p>
                <p id="cagr" class="sim-kpiValue">0%</p>
                <p id="cagr-meta" class="sim-kpiMeta"></p>
            </div>
        </div>
    </section>

    <details class="sim-accordion" open>
        <summary aria-label="Market Events & Teaching Moments">
            <span><?php echo e(__('Market Events & Teaching Moments')); ?></span>
            <span style="color:var(--c-on-surface-2); font-size:13px;"><?php echo e(__('Highlights as you run')); ?></span>
        </summary>
        <div class="sim-accordionBody">
            <ul id="event-log" style="margin:0; padding-left:18px; display:grid; gap:8px; font-size:14px; color:var(--c-on-surface-2);"></ul>
        </div>
    </details>

    <details class="sim-accordion">
        <summary aria-label="Settings">
            <span><?php echo e(__('Simulation Parameters')); ?></span>
            <span style="color:var(--c-on-surface-2); font-size:13px;"><?php echo e(__('What you assumed')); ?></span>
        </summary>
        <div class="sim-accordionBody">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:10px;">
                <div style="padding:12px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:13px;"><?php echo e(__('Initial Investment')); ?></span>
                    <p style="margin:4px 0 0; font-size:18px; font-weight:800;">€<?php echo e(number_format($simulation->settings['initialInvestment'], 2)); ?></p>
                </div>
                <div style="padding:12px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:13px;"><?php echo e(__('Monthly Contribution')); ?></span>
                    <p style="margin:4px 0 0; font-size:18px; font-weight:800;">€<?php echo e(number_format($simulation->settings['monthlyContribution'], 2)); ?></p>
                </div>
                <div style="padding:12px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:13px;"><?php echo e(__('Annual Growth Rate')); ?></span>
                    <p style="margin:4px 0 0; font-size:18px; font-weight:800;"><?php echo e(number_format($simulation->settings['growthRate'] * 100, 2)); ?>%</p>
                </div>
                <div style="padding:12px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:13px;"><?php echo e(__('Inflation Rate')); ?></span>
                    <p style="margin:4px 0 0; font-size:18px; font-weight:800;"><?php echo e(number_format($simulation->settings['inflationRate'] * 100, 2)); ?>%</p>
                </div>
                <div style="padding:12px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:13px;"><?php echo e(__('Risk Appetite')); ?></span>
                    <p style="margin:4px 0 0; font-size:18px; font-weight:800;"><?php echo e(number_format($simulation->settings['riskAppetite'] * 100, 0)); ?>%</p>
                </div>
                <div style="padding:12px; border-radius:12px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                    <span style="color: var(--c-on-surface-2); font-size:13px;"><?php echo e(__('Market Influence')); ?></span>
                    <p style="margin:4px 0 0; font-size:18px; font-weight:800;"><?php echo e(number_format($simulation->settings['marketInfluence'] * 100, 0)); ?>%</p>
                </div>
            </div>
        </div>
    </details>
</section>

<script type="application/json" id="simulation-runner-config"><?php echo json_encode($simulationRunnerConfig, 15, 512) ?></script>
<?php echo $__env->make('components.tutorial', ['currentPage' => 'show'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('components.currency-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/simulation-runner.js']); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/simulations/show.blade.php ENDPATH**/ ?>