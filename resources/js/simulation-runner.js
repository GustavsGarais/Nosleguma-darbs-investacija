import Chart from 'chart.js/auto';

function asNumber(value, fallback = 0) {
    if (typeof value === 'string') {
        value = value.replace(/[^0-9.\-]/g, '');
    }
    const num = Number(value);
    return Number.isFinite(num) ? num : fallback;
}

/** Stable fingerprint when simulation settings change (invalidates saved runner state). */
function runnerSettingsFingerprint(settingsObj) {
    const r = (x) => Math.round(Number(x) * 1e9) / 1e9;
    const s = settingsObj;
    return [
        r(s.initialInvestment),
        r(s.monthlyContribution),
        r(s.growthRate),
        r(s.inflationRate),
        r(s.riskAppetite),
        r(s.marketInfluence),
        Math.round(Number(s.investors) || 1),
    ].join('|');
}

function gaussianRandom() {
    let u = 0;
    let v = 0;
    while (u === 0) u = Math.random();
    while (v === 0) v = Math.random();
    return Math.sqrt(-2.0 * Math.log(u)) * Math.cos(2.0 * Math.PI * v);
}

/** Fat tails + typical month clustering */
function realisticReturn(monthlyBase, volatility) {
    const roll = Math.random();
    if (roll < 0.03) {
        return -0.15 - Math.random() * 0.2;
    }
    if (roll < 0.06) {
        return 0.08 + Math.random() * 0.1;
    }
    return monthlyBase + gaussianRandom() * volatility;
}

/**
 * Other market participants: AR(1) sentiment plus random waves of buying/selling.
 * Feeds into monthly returns so the path is not a one-way grind upward.
 */
function evolveCrowdSentiment(prev, settings, lastMonthlyReturn) {
    const investors = Math.max(1, Math.floor(Number(settings?.investors) || 1));
    // 1 investor => ~0, 100 investors => ~1 (log-scaled so 10->0.5, 100->1)
    const investorFactor = Math.max(0, Math.min(1, Math.log10(investors) / 2));
    const market = Math.max(0, Math.min(1, Number(settings?.marketInfluence) || 0));
    const risk = Math.max(0, Math.min(1, Number(settings?.riskAppetite) || 0));

    // With more investors + higher market influence, sentiment moves are larger and more frequent.
    const amp = 0.004 + investorFactor * market * (0.006 + 0.006 * risk);
    const persistence = 0.9 - investorFactor * market * 0.12;

    let s = persistence * prev + gaussianRandom() * amp;

    // Occasional waves (panic / euphoria), scaled by market influence.
    const waveBase = 0.055 + market * 0.08;
    if (Math.random() < waveBase) {
        const panic = 0.008 + Math.random() * (0.025 + 0.03 * market);
        s -= panic * (0.6 + 0.8 * investorFactor);
    }
    if (Math.random() < waveBase * 0.75) {
        const hype = 0.005 + Math.random() * (0.014 + 0.018 * market);
        s += hype * (0.6 + 0.8 * investorFactor);
    }

    // Mean reversion pressure that strengthens with more participants:
    // after a strong up month -> profit taking, after a strong down month -> dip buying.
    const mr = Math.tanh((Number(lastMonthlyReturn) || 0) * 18);
    s -= mr * (0.002 + 0.006 * investorFactor) * (0.35 + 0.65 * market);

    return Math.max(-0.18, Math.min(0.14, s));
}

const overlayPlugin = {
    id: 'simOverlays',
    afterDatasetsDraw(chart) {
        const { ctx, chartArea } = chart;
        if (!chartArea) return;
        const xScale = chart.scales.x;
        if (!xScale) return;

        const pm = chart.$pauseMonth;
        if (pm != null && pm > 0) {
            const x = xScale.getPixelForValue(pm);
            if (x >= chartArea.left && x <= chartArea.right) {
                ctx.save();
                ctx.beginPath();
                ctx.strokeStyle = 'rgba(148, 163, 184, 0.9)';
                ctx.lineWidth = 2;
                ctx.setLineDash([6, 4]);
                ctx.moveTo(x, chartArea.top);
                ctx.lineTo(x, chartArea.bottom);
                ctx.stroke();
                ctx.restore();
            }
        }

        const crashes = chart.$crashMonths || [];
        ctx.save();
        ctx.setLineDash([]);
        for (const m of crashes) {
            const x = xScale.getPixelForValue(m);
            if (x >= chartArea.left && x <= chartArea.right) {
                ctx.fillStyle = 'rgba(239, 68, 68, 0.12)';
                ctx.fillRect(x - 3, chartArea.top, 6, chartArea.bottom - chartArea.top);
            }
        }
        ctx.restore();
    },
};

Chart.register(overlayPlugin);

function initFromConfig(config) {
    const { snapshotUrl, runnerStateUrl, csrfToken, settings: rawSettings, i18n, savedRunner } = config;

    const chartCanvas = document.getElementById('sim-chart');
    const btnRun = document.getElementById('btn-run');
    const btnStep = document.getElementById('btn-step');
    const btnReset = document.getElementById('btn-reset');
    const btnSave = document.getElementById('btn-save');
    const monthsInput = document.getElementById('months-input');
    const speedInput = document.getElementById('speed-input');
    const presetSelect = document.getElementById('preset-select');
    const secondarySelect = document.getElementById('secondary-scenario');
    const compareExtraWrap = document.getElementById('compare-extra-wrap');
    const compareExtraInput = document.getElementById('compare-extra-monthly');
    const classicSecondaryWrap = document.getElementById('classic-secondary-wrap');
    const playgroundPanel = document.getElementById('playground-panel');
    const playgroundHelpEl = document.getElementById('playground-help');
    const modeClassicRadio = document.getElementById('mode-classic');
    const modePlaygroundRadio = document.getElementById('mode-playground');
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

    const meta = {
        current: document.getElementById('current-value-meta'),
        contributed: document.getElementById('total-contributed-meta'),
        gain: document.getElementById('total-gain-meta'),
        real: document.getElementById('real-value-meta'),
        drawdown: document.getElementById('drawdown-meta'),
        cagr: document.getElementById('cagr-meta'),
    };

    if (!chartCanvas || !btnRun || !btnReset || !monthsInput || !speedInput || !presetSelect) {
        console.warn('Simulation controls are missing from the DOM. Skipping initialization.');
        return;
    }

    const chartCtx = chartCanvas.getContext('2d');
    if (!chartCtx || typeof Chart === 'undefined') {
        console.warn('Chart.js is not available, unable to render simulation chart.');
        return;
    }

    // Keep canvas CSS size tied to wrapper; avoids “zoom/crop” when the drawing buffer resizes
    // but the element's CSS box doesn't (common with flex + animated side panels).
    chartCanvas.style.display = 'block';
    chartCanvas.style.width = '100%';
    chartCanvas.style.height = '100%';

    function setRunPauseButtonState(running) {
        const runLabel = btnRun.getAttribute('data-label-run') || i18n.run || 'Run';
        const pauseLabel = btnRun.getAttribute('data-label-pause') || i18n.pause || 'Pause';
        const runIcon = btnRun.getAttribute('data-icon-run') || '▶';
        const pauseIcon = btnRun.getAttribute('data-icon-pause') || '⏸';
        btnRun.setAttribute('aria-pressed', running ? 'true' : 'false');
        btnRun.classList.toggle('btn-primary', !running);
        btnRun.classList.toggle('btn-secondary', running);
        btnRun.innerHTML = `${running ? pauseIcon : runIcon} ${running ? pauseLabel : runLabel}`;
    }

    const settings = {
        initialInvestment: asNumber(rawSettings.initialInvestment, 0),
        monthlyContribution: asNumber(rawSettings.monthlyContribution, 0),
        growthRate: asNumber(rawSettings.growthRate, 0.05),
        inflationRate: asNumber(rawSettings.inflationRate, 0.02),
        riskAppetite: Math.min(1, Math.max(0, asNumber(rawSettings.riskAppetite, 0.5))),
        marketInfluence: Math.min(1, Math.max(0, asNumber(rawSettings.marketInfluence, 0.5))),
        investors: Math.max(1, Math.floor(asNumber(rawSettings.investors, 1))),
    };

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
            if (parsed.timestamp && (Date.now() - parsed.timestamp) < 24 * 60 * 60 * 1000) {
                return { ...defaultRates, ...parsed.rates };
            }
        } catch (e) {
            /* ignore */
        }
        return { ...defaultRates };
    }

    let currencyRates = loadCachedRates();

    const currencySymbols = {
        EUR: '\u20AC',
        USD: '$',
        GBP: '\u00A3',
        JPY: '\u00A5',
    };

    function getPreferredCurrency() {
        const server = window.__NOS_SERVER_CURRENCY__;
        if (server && currencyRates[server]) {
            return server;
        }
        try {
            const stored = localStorage.getItem('nosleguma-currency-preference');
            if (stored && currencyRates[stored]) {
                return stored;
            }
        } catch (e) {
            /* ignore */
        }
        return 'EUR';
    }

    let activeCurrency = getPreferredCurrency();

    function convertAmount(euroAmount) {
        const rate = currencyRates[activeCurrency] ?? 1;
        return euroAmount * rate;
    }

    function formatConverted(amount) {
        const symbol = currencySymbols[activeCurrency] ?? '\u20AC';
        const sign = amount < 0 ? '-' : '';
        const formatted = Math.abs(amount).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
        return `${sign}${symbol}${formatted}`;
    }

    function formatCurrency(euroAmount) {
        return formatConverted(convertAmount(euroAmount));
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
            shockChance: 0,
            shockImpact: () => 0,
            recoveryBias: 0.0008,
            lesson: i18n.balancedLesson,
        },
        growth: {
            label: i18n.growthLabel,
            expectedAnnual: Math.max(settings.growthRate + 0.02, settings.growthRate),
            monthlyVolatility: 0.03,
            shockChance: 0,
            shockImpact: () => 0,
            recoveryBias: 0.0025,
            lesson: i18n.growthLesson,
        },
        defensive: {
            label: i18n.defensiveLabel,
            expectedAnnual: Math.max(settings.growthRate - 0.02, 0.02),
            monthlyVolatility: 0.012,
            shockChance: 0,
            shockImpact: () => 0,
            recoveryBias: 0.0004,
            lesson: i18n.defensiveLesson,
        },
        volatile: {
            label: i18n.volatileLabel,
            expectedAnnual: settings.growthRate,
            monthlyVolatility: 0.045,
            shockChance: 0,
            shockImpact: () => 0,
            recoveryBias: 0.0012,
            lesson: i18n.volatileLesson,
        },
        shock: {
            label: i18n.shockLabel,
            expectedAnnual: Math.max(settings.growthRate - 0.01, 0.03),
            monthlyVolatility: 0.035,
            shockChance: 0.015,
            shockImpact: () => -0.25 - Math.random() * 0.2,
            recoveryBias: 0.008,
            lesson: i18n.shockLesson,
        },
    };

    // Daily timestep: annual rates -> daily rates (365-day year).
    const baseDailyReturnRate = Math.pow(1 + settings.growthRate, 1 / 365) - 1;
    const dailyInflationRate = Math.pow(1 + settings.inflationRate, 1 / 365) - 1;
    const volatilityInfluence = (settings.riskAppetite + settings.marketInfluence) / 2;
    const investorFactor = Math.max(0, Math.min(1, Math.log10(settings.investors) / 2));

    const annualFeeRate = 0.002;
    const dailyFeeRate = annualFeeRate / 365;

    // User input is "monthly contribution". With a daily timestep, we spread it across days.
    // This keeps yearly contribution roughly consistent without adding the full amount every day.
    const dailyContribution = settings.monthlyContribution / 30;

    let isRunning = false;
    let currentMonth = 0; // now interpreted as "day"
    let simulationData = [];
    let simulationDataCompare = [];
    let simulationDataSor = [];
    let intervalId = null;
    let peakValue = settings.initialInvestment;
    let maxDrawdown = 0;
    let activePresetKey = 'balanced';
    let eventLog = [];
    let lastMonthlyReturn = baseDailyReturnRate;

    let sharedSmoothedReturns = null;
    let sharedSmoothedReturnsReversed = null;

    let secondaryScenario = secondarySelect?.value || 'none';
    let isPlaygroundMode = false;
    let crowdSentiment = 0;

    function readPlaygroundModeFromUi() {
        return Boolean(modePlaygroundRadio?.checked);
    }

    let chartResizeSettleGen = 0;

    /** Re-run resize on every animation frame until the host width is stable (covers ~280ms flyout transitions). */
    function runChartResizeSettlingLoop(durationMs = 540) {
        chartResizeSettleGen += 1;
        const gen = chartResizeSettleGen;
        const start = performance.now();
        let lastW = -1;
        let stableFrames = 0;
        const tick = () => {
            if (gen !== chartResizeSettleGen) return;
            forceChartResize();
            const sz = readChartHostSize();
            const w = sz ? sz.width : 0;
            if (w > 0 && w === lastW) {
                stableFrames += 1;
            } else {
                stableFrames = 0;
                lastW = w;
            }
            const elapsed = performance.now() - start;
            if (elapsed < durationMs || stableFrames < 3) {
                requestAnimationFrame(tick);
            }
        };
        requestAnimationFrame(tick);
    }

    /** Flyouts + flex use CSS width transitions; one-shot resize often reads a stale box — settle until layout stops moving. */
    function scheduleChartResizeAfterLayout() {
        runChartResizeSettlingLoop(540);
    }

    function scheduleChartSyncAfterFlyoutLayout() {
        scheduleChartResizeAfterLayout();
    }

    function syncModeUi() {
        isPlaygroundMode = readPlaygroundModeFromUi();
        if (classicSecondaryWrap) {
            classicSecondaryWrap.style.display = isPlaygroundMode ? 'none' : '';
        }
        if (playgroundPanel) {
            playgroundPanel.hidden = !isPlaygroundMode;
            playgroundPanel.setAttribute('aria-hidden', isPlaygroundMode ? 'false' : 'true');
        }
        if (playgroundHelpEl) {
            playgroundHelpEl.textContent = i18n.playgroundHelp || '';
        }
        const playgroundNextStepEl = document.getElementById('playground-next-step');
        if (playgroundNextStepEl) {
            playgroundNextStepEl.textContent = i18n.playgroundNextStep || '';
        }
        if (isPlaygroundMode && secondarySelect && secondarySelect.value !== 'none') {
            secondarySelect.value = 'none';
            secondaryScenario = 'none';
            invalidatePath();
        }
        chart.data.datasets[5].hidden = !isPlaygroundMode;
        syncSecondaryUi();
        chart.update('none');
        scheduleChartResizeAfterLayout();
    }

    const primaryColor =
        getComputedStyle(document.documentElement).getPropertyValue('--c-primary').trim() || '#07a05a';
    const secondaryColor =
        getComputedStyle(document.documentElement).getPropertyValue('--c-secondary').trim() || '#d98e12';
    const themeColors = () => {
        const cs = getComputedStyle(document.documentElement);
        return {
            on: cs.getPropertyValue('--c-on-surface').trim() || '#0f172a',
            on2: cs.getPropertyValue('--c-on-surface-2').trim() || '#374151',
            border: cs.getPropertyValue('--c-border').trim() || '#e5e7eb',
            surface: cs.getPropertyValue('--c-surface').trim() || '#ffffff',
            primary: cs.getPropertyValue('--c-primary').trim() || '#07a05a',
        };
    };
    const contribColor = '#94a3b8';
    const compareColor = '#6366f1';

    const chart = new Chart(chartCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: i18n.chartContributed,
                    data: [],
                    borderColor: contribColor,
                    borderWidth: 2,
                    borderDash: [8, 4],
                    fill: false,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    order: 3,
                },
                {
                    label: i18n.chartReal,
                    data: [],
                    borderColor: secondaryColor,
                    backgroundColor: secondaryColor + '18',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    fill: false,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 5,
                    order: 2,
                },
                {
                    label: i18n.chartNominal,
                    data: [],
                    borderColor: primaryColor,
                    backgroundColor: primaryColor + '2a',
                    borderWidth: 3,
                    fill: 1,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    order: 1,
                    segment: {
                        borderColor: (ctx) => {
                            if (ctx.p0.skip || ctx.p1.skip) {
                                return undefined;
                            }
                            const y0 = ctx.p0.parsed.y;
                            const y1 = ctx.p1.parsed.y;
                            if (y0 == null || y1 == null) {
                                return undefined;
                            }
                            const root = document.documentElement;
                            const cs = getComputedStyle(root);
                            const up =
                                cs.getPropertyValue('--sim-chart-trend-up').trim() ||
                                cs.getPropertyValue('--c-primary').trim() ||
                                '#07a05a';
                            const down = cs.getPropertyValue('--sim-chart-trend-down').trim() || '#dc2626';
                            return y1 >= y0 ? up : down;
                        },
                    },
                },
                {
                    label: i18n.chartCompare,
                    data: [],
                    borderColor: compareColor,
                    borderWidth: 2,
                    borderDash: [2, 3],
                    fill: false,
                    tension: 0.35,
                    pointRadius: 0,
                    hidden: true,
                    order: 0,
                },
                {
                    label: i18n.chartSor,
                    data: [],
                    borderColor: '#0ea5e9',
                    borderWidth: 2,
                    borderDash: [1, 2],
                    fill: false,
                    tension: 0.35,
                    pointRadius: 0,
                    hidden: true,
                    order: 0,
                },
                {
                    label: i18n.chartTotalPL,
                    data: [],
                    borderColor: '#f97316',
                    borderWidth: 2,
                    borderDash: [4, 3],
                    fill: false,
                    tension: 0.35,
                    pointRadius: 0,
                    hidden: true,
                    order: 0,
                },
            ],
        },
        options: {
            // Manual sizing only: Chart's built-in ResizeObserver on the canvas parent races with flex + CSS transitions.
            responsive: false,
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
                        padding: 14,
                        filter(legendItem) {
                            if (isPlaygroundMode) {
                                if (legendItem.datasetIndex === 3 || legendItem.datasetIndex === 4) {
                                    return false;
                                }
                                if (legendItem.datasetIndex === 5) {
                                    return true;
                                }
                                return legendItem.datasetIndex <= 2;
                            }
                            if (legendItem.datasetIndex === 5) {
                                return false;
                            }
                            if (legendItem.datasetIndex === 3 && secondaryScenario !== 'compare') {
                                return false;
                            }
                            if (legendItem.datasetIndex === 4 && secondaryScenario !== 'sor') {
                                return false;
                            }
                            return true;
                        },
                        font: { size: 11, weight: '600' },
                    },
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.82)',
                    padding: 12,
                    callbacks: {
                        label(ctx) {
                            const label = ctx.dataset.label ? ctx.dataset.label + ': ' : '';
                            return label + formatConverted(ctx.parsed.y);
                        },
                    },
                },
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: i18n.xAxisDay || 'Day',
                        font: { size: 13, weight: '600' },
                    },
                    grid: { display: false },
                    ticks: {},
                },
                y: {
                    beginAtZero: false,
                    title: {
                        display: true,
                        text: (i18n.yAxisValue || 'Value (:currency)').replaceAll(':currency', activeCurrency),
                        font: { size: 13, weight: '600' },
                    },
                    ticks: {
                        callback(value) {
                            return formatConverted(value);
                        },
                    },
                    grid: { color: 'rgba(0, 0, 0, 0.06)' },
                },
            },
            animation: { duration: 0 },
        },
    });

    function readChartHostSize() {
        const wrap = document.querySelector('.sim-run-chartWrap');
        if (!wrap) return null;
        let width = Math.max(0, Math.floor(wrap.clientWidth));
        let height = Math.max(0, Math.floor(wrap.clientHeight));
        if (height < 2) {
            height = Math.max(0, Math.floor(wrap.getBoundingClientRect().height));
        }
        if (width < 2 || height < 2) return null;
        return { width, height };
    }

    /** Pin chart pixel size to `.sim-run-chartWrap` (required while `responsive: false`). */
    function forceChartResize() {
        chart.stop();
        const size = readChartHostSize();
        if (size) {
            chart.resize(size.width, size.height);
        } else {
            chart.resize();
        }
        chart.update('none');
    }

    function applyChartTheme() {
        const c = themeColors();
        const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
        const tickColor = c.on;
        const titleColor = c.on2;
        const gridColor = isDark
            ? 'rgba(255, 255, 255, 0.08)'
            : 'rgba(15, 23, 42, 0.08)';

        chart.options.plugins.legend.labels.color = titleColor;
        chart.options.scales.x.ticks.color = tickColor;
        chart.options.scales.y.ticks.color = tickColor;
        chart.options.scales.x.title.color = titleColor;
        chart.options.scales.y.title.color = titleColor;
        chart.options.scales.y.grid.color = gridColor;

        // Tooltip: keep readable on both themes.
        chart.options.plugins.tooltip.backgroundColor = isDark
            ? 'rgba(17, 24, 39, 0.92)'
            : 'rgba(15, 23, 42, 0.92)';

        const p = c.primary;
        chart.data.datasets[2].borderColor = p;
        chart.data.datasets[2].backgroundColor = `${p}2a`;

        chart.update('none');
    }

    forceChartResize();
    applyChartTheme();

    // React to theme changes (light/dark toggle).
    if (typeof MutationObserver !== 'undefined') {
        const mo = new MutationObserver((mutations) => {
            for (const m of mutations) {
                if (m.type === 'attributes' && m.attributeName === 'data-theme') {
                    applyChartTheme();
                }
            }
        });
        mo.observe(document.documentElement, { attributes: true });
    }

    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => forceChartResize(), 100);
    });

    const dashBody = document.querySelector('.sim-dash-body');
    const dashLead = document.getElementById('sim-dash-lead');
    const btnToggleControls = document.getElementById('btn-toggle-controls');
    const leadFlyoutEl = document.querySelector('.sim-controls-flyout--lead');
    const chartColEl = document.querySelector('.sim-dash-chartCol');

    const roDebounce = (fn) => {
        let t = null;
        return () => {
            if (t) clearTimeout(t);
            t = window.setTimeout(() => {
                t = null;
                fn();
            }, 32);
        };
    };

    /** Coalesce ResizeObserver storms during CSS width transitions; flyouts need the trailing sync too. */
    const roOnFlexLayout = roDebounce(scheduleChartSyncAfterFlyoutLayout);

    if (dashBody && typeof ResizeObserver !== 'undefined') {
        const ro = new ResizeObserver(roOnFlexLayout);
        ro.observe(dashBody);
    }

    const dashWork = document.querySelector('.sim-dash-work');
    if (dashWork && typeof ResizeObserver !== 'undefined') {
        const roWork = new ResizeObserver(roOnFlexLayout);
        roWork.observe(dashWork);
    }

    // Flyouts animate width; lead vs chart column split can change without the viewport changing.
    if (dashLead && typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(roOnFlexLayout).observe(dashLead);
    }
    if (chartColEl && typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(roOnFlexLayout).observe(chartColEl);
    }
    for (const el of [leadFlyoutEl, playgroundPanel].filter(Boolean)) {
        if (typeof ResizeObserver === 'undefined') break;
        new ResizeObserver(roOnFlexLayout).observe(el);
    }

    const chartWrap = document.querySelector('.sim-run-chartWrap');
    if (chartWrap && typeof ResizeObserver !== 'undefined') {
        const chartRo = new ResizeObserver(roOnFlexLayout);
        chartRo.observe(chartWrap);
    }

    // Moving the pointer onto the chart usually means a flyout just collapsed — settle size immediately.
    dashWork?.addEventListener('pointerenter', () => scheduleChartResizeAfterLayout());

    btnToggleControls?.addEventListener('click', () => {
        if (!dashBody || !dashLead) return;
        dashBody.classList.toggle('sim-dash-body--controls-hidden');
        const expanded = !dashBody.classList.contains('sim-dash-body--controls-hidden');
        btnToggleControls.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        const titleHide = btnToggleControls.getAttribute('data-title-hide') || '';
        const titleShow = btnToggleControls.getAttribute('data-title-show') || '';
        btnToggleControls.title = expanded ? titleHide : titleShow;
        if (expanded) {
            dashLead.removeAttribute('inert');
        } else {
            dashLead.setAttribute('inert', '');
        }
        scheduleChartResizeAfterLayout();
    });

    /** Flyouts open/close in CSS — transitionend / leaving focus catches collapse after width animation. */
    (function bindChartSyncToFlyoutLayout() {
        const layoutRoots = [dashLead, leadFlyoutEl, playgroundPanel].filter(Boolean);
        const flyoutSurfaces = [leadFlyoutEl, playgroundPanel].filter(Boolean);
        const chartAffectingTransition = (prop) =>
            prop === 'width' ||
            prop === 'min-width' ||
            prop === 'max-width' ||
            prop === 'flex-basis';

        const onTransitionEnd = (ev) => {
            if (!chartAffectingTransition(ev.propertyName)) return;
            scheduleChartSyncAfterFlyoutLayout();
        };
        const onPointerLeaveFlyout = () => scheduleChartSyncAfterFlyoutLayout();
        const onFocusOut = (ev) => {
            const root = ev.currentTarget;
            const next = ev.relatedTarget;
            if (next && root.contains(next)) return;
            scheduleChartSyncAfterFlyoutLayout();
        };

        for (const el of layoutRoots) {
            el.addEventListener('transitionend', onTransitionEnd);
            el.addEventListener('focusout', onFocusOut);
        }
        for (const el of flyoutSurfaces) {
            el.addEventListener('mouseleave', onPointerLeaveFlyout);
        }
    })();

    function syncSecondaryUi() {
        if (compareExtraWrap) {
            compareExtraWrap.style.display =
                !isPlaygroundMode && secondaryScenario === 'compare' ? 'grid' : 'none';
        }
        if (isPlaygroundMode) {
            chart.data.datasets[3].hidden = true;
            chart.data.datasets[4].hidden = true;
        } else {
            chart.data.datasets[3].hidden = secondaryScenario !== 'compare';
            chart.data.datasets[4].hidden = secondaryScenario !== 'sor';
        }
        chart.update('none');
    }

    function invalidatePath() {
        sharedSmoothedReturns = null;
        sharedSmoothedReturnsReversed = null;
    }

    function buildSmoothedPath(length) {
        const arr = [];
        let last = baseDailyReturnRate;
        let pathCrowd = 0;
        const preset = presetConfigs[activePresetKey] ?? presetConfigs.balanced;

        for (let step = 1; step <= length; step++) {
            const targetDaily = Math.pow(1 + preset.expectedAnnual, 1 / 365) - 1;
            const cycleBias = Math.sin((step / 365) * Math.PI * 2) * 0.0006;
            const dailyBase = targetDaily + cycleBias + preset.recoveryBias / 30;
            const vol = preset.monthlyVolatility * (1 + volatilityInfluence * 0.6 + investorFactor * 0.25);
            pathCrowd = evolveCrowdSentiment(pathCrowd, settings, last);
            let r = realisticReturn(dailyBase, vol) + pathCrowd * (0.55 + settings.marketInfluence * 0.35);
            if (activePresetKey === 'shock' && Math.random() < preset.shockChance) {
                const extra = preset.shockImpact();
                r += extra;
            }
            r = Math.max(-0.85, Math.min(r, 0.25));
            const smoothed = 0.55 * last + 0.45 * r;
            last = smoothed;
            arr.push(smoothed);
        }
        return arr;
    }

    function ensureSharedPath(maxMonths) {
        if (secondaryScenario === 'none') {
            invalidatePath();
            return;
        }
        if (sharedSmoothedReturns && sharedSmoothedReturns.length >= maxMonths) {
            return;
        }
        sharedSmoothedReturns = buildSmoothedPath(maxMonths);
        sharedSmoothedReturnsReversed = [...sharedSmoothedReturns].reverse();
    }

    function computeMonthlyReturnSmoothed(stepMonth) {
        const preset = presetConfigs[activePresetKey] ?? presetConfigs.balanced;
        const targetDaily = Math.pow(1 + preset.expectedAnnual, 1 / 365) - 1;
        const cycleBias = Math.sin((stepMonth / 365) * Math.PI * 2) * 0.0006;
        const dailyBase = targetDaily + cycleBias + preset.recoveryBias / 30;
        const vol = preset.monthlyVolatility * (1 + volatilityInfluence * 0.6 + investorFactor * 0.25);
        crowdSentiment = evolveCrowdSentiment(crowdSentiment, settings, lastMonthlyReturn);
        let r =
            realisticReturn(dailyBase, vol) +
            crowdSentiment * (0.55 + settings.marketInfluence * 0.35);
        if (r <= -0.12 || r >= 0.15) {
            pushEvent(i18n.fatTailEvent.replace(':pct', (r * 100).toFixed(1)));
        }
        if (crowdSentiment < -0.055 && Math.random() < 0.22) {
            pushEvent(
                i18n.crowdSelling.replace(':pct', Math.abs(crowdSentiment * 100).toFixed(1)),
            );
        } else if (crowdSentiment > 0.045 && Math.random() < 0.18) {
            pushEvent(i18n.crowdBuying.replace(':pct', (crowdSentiment * 100).toFixed(1)));
        }
        if (activePresetKey === 'shock' && Math.random() < preset.shockChance) {
            const extra = preset.shockImpact();
            r += extra * (0.65 + settings.marketInfluence * 0.7);
            pushEvent(
                i18n.marketShock.replace(':pct', (extra * 100).toFixed(1)).replace(':label', preset.label),
            );
        }
        r = Math.max(-0.85, Math.min(r, 0.25));
        const smoothed = 0.55 * lastMonthlyReturn + 0.45 * r;
        lastMonthlyReturn = smoothed;
        return smoothed;
    }

    function marketReturnForStep(stepMonth) {
        if (sharedSmoothedReturns && stepMonth > 0 && stepMonth <= sharedSmoothedReturns.length) {
            const mr = sharedSmoothedReturns[stepMonth - 1];
            lastMonthlyReturn = mr;
            return mr;
        }
        return computeMonthlyReturnSmoothed(stepMonth);
    }

    function marketReturnForStepSor(stepMonth) {
        if (sharedSmoothedReturnsReversed && stepMonth > 0 && stepMonth <= sharedSmoothedReturnsReversed.length) {
            return sharedSmoothedReturnsReversed[stepMonth - 1];
        }
        return marketReturnForStep(stepMonth);
    }

    function applyPortfolioStep(lastEntry, marketReturn, monthlyContribution) {
        const nextMonth = lastEntry.month + 1; // "day"
        const valueAfterGrowth = lastEntry.value * (1 + marketReturn);
        const valueAfterFees = valueAfterGrowth * (1 - dailyFeeRate);
        const contributions = lastEntry.contributions + monthlyContribution;
        const newValue = Math.max(0, valueAfterFees + monthlyContribution);
        const inflationAdjusted = newValue / Math.pow(1 + dailyInflationRate, nextMonth);
        return {
            month: nextMonth,
            value: newValue,
            inflationAdjusted,
            contributions,
            interestEarned: valueAfterFees - lastEntry.value,
        };
    }

    function updateCurrencyLabels() {
        chart.data.datasets[0].label = `${i18n.chartContributed} (${activeCurrency})`;
        chart.data.datasets[1].label = `${i18n.chartReal} (${activeCurrency})`;
        chart.data.datasets[2].label = isPlaygroundMode
            ? `${i18n.chartNetWorth} (${activeCurrency})`
            : `${i18n.chartNominal} (${activeCurrency})`;
        chart.data.datasets[3].label = `${i18n.chartCompare} (${activeCurrency})`;
        chart.data.datasets[4].label = `${i18n.chartSor} (${activeCurrency})`;
        chart.data.datasets[5].label = `${i18n.chartTotalPL} (${activeCurrency})`;
        if (chart.options?.scales?.y?.title) {
            chart.options.scales.y.title.text = (i18n.yAxisValue || 'Value (:currency)').replaceAll(
                ':currency',
                activeCurrency,
            );
        }
    }

    function lifetimeContributedOf(row) {
        return row.lifetimeContributed ?? row.contributions;
    }

    function playgroundRow(
        month,
        price,
        units,
        walletCash,
        costBasis,
        lifetimeContributed,
        realizedGainTotal,
    ) {
        const marketValue = units * price;
        const netWorth = walletCash + marketValue;
        const inflationAdjusted = netWorth / Math.pow(1 + dailyInflationRate, month);
        return {
            month,
            price,
            units,
            walletCash,
            costBasis,
            lifetimeContributed,
            realizedGainTotal,
            marketValue,
            value: netWorth,
            contributions: lifetimeContributed,
            inflationAdjusted,
            interestEarned: 0,
        };
    }

    function replaceLastPlaygroundRow(row) {
        if (!simulationData.length) return;
        simulationData[simulationData.length - 1] = row;
    }

    function playgroundBuy(amount) {
        const B = Number(amount);
        if (!Number.isFinite(B) || B <= 0) return;
        const last = simulationData[simulationData.length - 1];
        const P = last.price;
        const addUnits = B / P;
        const newUnits = last.units + addUnits;
        const newCost = last.costBasis + B;
        const newLifetime = last.lifetimeContributed + B;
        const row = playgroundRow(
            last.month,
            P,
            newUnits,
            last.walletCash,
            newCost,
            newLifetime,
            last.realizedGainTotal,
        );
        replaceLastPlaygroundRow(row);
        peakValue = Math.max(peakValue, row.value);
        const msg = (i18n.playgroundBought || 'Added :amount').replace(':amount', formatCurrency(B));
        pushEvent(msg);
        rebuildChartData('none');
        updateSummary();
    }

    function playgroundSell(fraction) {
        const f = Math.min(1, Math.max(0, fraction));
        const last = simulationData[simulationData.length - 1];
        if (!last.units || last.units <= 0 || f <= 0) return;
        const sellUnits = last.units * f;
        const P = last.price;
        const proceeds = sellUnits * P;
        const costRemoved = last.costBasis * f;
        const tradeGain = proceeds - costRemoved;
        const newUnits = last.units - sellUnits;
        const newCost = last.costBasis - costRemoved;
        const newWallet = last.walletCash + proceeds;
        const newRealized = last.realizedGainTotal + tradeGain;
        const row = playgroundRow(
            last.month,
            P,
            newUnits,
            newWallet,
            newCost,
            last.lifetimeContributed,
            newRealized,
        );
        replaceLastPlaygroundRow(row);
        const drawdown = (row.value - peakValue) / (peakValue || 1);
        maxDrawdown = Math.min(maxDrawdown, drawdown);
        const pct = Math.round(f * 100);
        const msg = (i18n.playgroundSold || '')
            .replace(':pct', String(pct))
            .replace(':gain', formatCurrency(tradeGain));
        pushEvent(msg);
        rebuildChartData('none');
        updateSummary();
    }

    function playgroundAdvanceMonth() {
        const last = simulationData[simulationData.length - 1];
        const stepMonth = last.month + 1;
        const marketReturn = marketReturnForStep(stepMonth);
        if (marketReturn < -0.12) {
            crashMonths.add(stepMonth);
        }
        const newPrice = last.price * (1 + marketReturn) * (1 - dailyFeeRate);
        const marketValue = last.units * newPrice;
        const netWorth = last.walletCash + marketValue;
        simulationData.push(
            playgroundRow(
                stepMonth,
                newPrice,
                last.units,
                last.walletCash,
                last.costBasis,
                last.lifetimeContributed,
                last.realizedGainTotal,
            ),
        );
        const next = simulationData[simulationData.length - 1];
        next.interestEarned = marketValue - last.units * last.price;
        currentMonth = stepMonth;

        if (next.value > peakValue) {
            peakValue = next.value;
            pushEvent(i18n.newHigh.replace(':month', String(next.month)));
        }
        const dd = (next.value - peakValue) / (peakValue || 1);
        maxDrawdown = Math.min(maxDrawdown, dd);
        if (dd < -0.1 && dd.toFixed(2) === maxDrawdown.toFixed(2)) {
            pushEvent(i18n.drawdownCoaching.replace(':pct', Math.abs(dd * 100).toFixed(1)));
        }
    }

    function updatePlaygroundTradingDesk() {
        const cashEl = document.getElementById('playground-hud-cash');
        const investedEl = document.getElementById('playground-hud-invested');
        const unrealEl = document.getElementById('playground-hud-unrealized');
        const realizedEl = document.getElementById('playground-realized');

        if (!isPlaygroundMode) {
            if (cashEl) cashEl.textContent = '—';
            if (investedEl) investedEl.textContent = '—';
            if (unrealEl) {
                unrealEl.textContent = '—';
                unrealEl.classList.remove(
                    'sim-playground-hud__value--up',
                    'sim-playground-hud__value--down',
                );
            }
            if (realizedEl) realizedEl.textContent = '';
            return;
        }

        const last = simulationData[simulationData.length - 1];
        if (!last || typeof last.walletCash !== 'number') return;

        const marketVal = last.units * last.price;
        const unreal = marketVal - last.costBasis;

        if (cashEl) cashEl.textContent = formatCurrency(last.walletCash);
        if (investedEl) investedEl.textContent = formatCurrency(marketVal);
        if (unrealEl) {
            unrealEl.textContent = formatCurrency(unreal);
            unrealEl.classList.toggle('sim-playground-hud__value--up', unreal > 0);
            unrealEl.classList.toggle('sim-playground-hud__value--down', unreal < 0);
        }
        if (realizedEl) {
            const label = i18n.playgroundRealizedLabel || 'Realized P/L';
            realizedEl.textContent = `${label}: ${formatCurrency(last.realizedGainTotal)}`;
        }
    }

    let crashMonths = new Set();

    function seedInitialState() {
        crowdSentiment = 0;
        lastMonthlyReturn = baseDailyReturnRate;
        currentMonth = 0;
        peakValue = settings.initialInvestment;
        maxDrawdown = 0;
        eventLog = [];
        crashMonths = new Set();
        isPlaygroundMode = readPlaygroundModeFromUi();
        if (isPlaygroundMode) {
            const p0 = 100;
            const inv = settings.initialInvestment;
            const u0 = inv / p0;
            simulationData = [
                playgroundRow(0, p0, u0, 0, inv, inv, 0),
            ];
            simulationDataCompare = [];
            simulationDataSor = [];
        } else {
            simulationData = [
                {
                    month: 0,
                    value: settings.initialInvestment,
                    inflationAdjusted: settings.initialInvestment,
                    contributions: settings.initialInvestment,
                    interestEarned: 0,
                },
            ];
            simulationDataCompare = [{ ...simulationData[0] }];
            simulationDataSor = [{ ...simulationData[0] }];
        }
        invalidatePath();

        rebuildChartData('resize');
        updateSummary();
        updateLearningNote();
        updateRiskTip();
        renderEvents();
        chart.$pauseMonth = null;
        chart.$crashMonths = [];
        syncModeUi();
        statusDisplay.textContent = i18n.ready;
        statusDisplay.style.background =
            'color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%)';
        schedulePersistRunnerState();
    }

    const cloneStateRows = (arr) => (Array.isArray(arr) ? arr.map((r) => ({ ...r })) : []);

    let runnerPersistTimer = null;
    let suppressRunnerPersistUntil = 0;

    function buildRunnerPayload() {
        const pgc = document.getElementById('playground-custom-amount');
        return {
            v: 1,
            settingsFingerprint: runnerSettingsFingerprint(settings),
            mode: isPlaygroundMode ? 'playground' : 'classic',
            months: parseInt(monthsInput.value, 10) || 120,
            speed: parseFloat(speedInput.value) || 0.25,
            activePresetKey,
            secondaryScenario,
            compareExtra: Math.max(0, parseFloat(compareExtraInput?.value) || 0),
            playgroundCustomAmount: Math.max(0, parseFloat(pgc?.value) || 0),
            simulationData: cloneStateRows(simulationData),
            simulationDataCompare: cloneStateRows(simulationDataCompare),
            simulationDataSor: cloneStateRows(simulationDataSor),
            sharedSmoothedReturns: sharedSmoothedReturns ? [...sharedSmoothedReturns] : null,
            sharedSmoothedReturnsReversed: sharedSmoothedReturnsReversed
                ? [...sharedSmoothedReturnsReversed]
                : null,
            crashMonths: Array.from(crashMonths),
            peakValue,
            maxDrawdown,
            lastMonthlyReturn,
            crowdSentiment,
            currentMonth,
        };
    }

    function schedulePersistRunnerState() {
        if (!runnerStateUrl || !csrfToken || typeof window.fetch !== 'function') return;
        if (Date.now() < suppressRunnerPersistUntil) return;
        clearTimeout(runnerPersistTimer);
        runnerPersistTimer = setTimeout(() => {
            runnerPersistTimer = null;
            const body = buildRunnerPayload();
            fetch(runnerStateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body),
            }).catch(() => {});
        }, 700);
    }

    function validateSavedRunner(saved) {
        if (!saved || saved.v !== 1) return false;
        if (saved.settingsFingerprint !== runnerSettingsFingerprint(settings)) return false;
        const wantPlay = saved.mode === 'playground';
        const rows = saved.simulationData;
        if (!Array.isArray(rows) || rows.length === 0) return false;
        const first = rows[0];
        if (wantPlay) {
            if (typeof first.units !== 'number' || typeof first.price !== 'number') return false;
        } else if (first.units != null) {
            return false;
        }
        if (wantPlay && saved.secondaryScenario !== 'none') return false;
        if (!wantPlay && saved.secondaryScenario === 'compare') {
            const c = saved.simulationDataCompare;
            if (!Array.isArray(c) || c.length !== rows.length) return false;
        }
        if (!wantPlay && saved.secondaryScenario === 'sor') {
            const sor = saved.simulationDataSor;
            if (!Array.isArray(sor) || sor.length !== rows.length) return false;
        }
        const last = rows[rows.length - 1];
        if (typeof last?.month !== 'number') return false;
        if (typeof saved.currentMonth === 'number' && saved.currentMonth !== last.month) return false;
        if (!presetConfigs[saved.activePresetKey]) return false;
        return true;
    }

    function applySavedRunner(saved) {
        suppressRunnerPersistUntil = Date.now() + 1000;
        const wantPlay = saved.mode === 'playground';
        if (wantPlay) {
            if (modePlaygroundRadio) modePlaygroundRadio.checked = true;
            if (modeClassicRadio) modeClassicRadio.checked = false;
        } else {
            if (modeClassicRadio) modeClassicRadio.checked = true;
            if (modePlaygroundRadio) modePlaygroundRadio.checked = false;
        }
        isPlaygroundMode = wantPlay;
        monthsInput.value = String(Math.min(600, Math.max(1, saved.months)));
        speedInput.value = String(Math.max(0.1, Math.min(10, saved.speed)));
        if (presetSelect && presetConfigs[saved.activePresetKey]) {
            presetSelect.value = saved.activePresetKey;
            activePresetKey = saved.activePresetKey;
        }
        if (secondarySelect) {
            secondarySelect.value = saved.secondaryScenario;
            secondaryScenario = saved.secondaryScenario;
        }
        if (compareExtraInput != null) {
            compareExtraInput.value = String(Math.max(0, parseFloat(saved.compareExtra) || 0));
        }
        const pgc = document.getElementById('playground-custom-amount');
        if (pgc != null && saved.playgroundCustomAmount != null) {
            pgc.value = String(Math.max(0, saved.playgroundCustomAmount));
        }
        simulationData = cloneStateRows(saved.simulationData);
        simulationDataCompare = cloneStateRows(saved.simulationDataCompare);
        simulationDataSor = cloneStateRows(saved.simulationDataSor);
        sharedSmoothedReturns = Array.isArray(saved.sharedSmoothedReturns)
            ? [...saved.sharedSmoothedReturns]
            : null;
        sharedSmoothedReturnsReversed = Array.isArray(saved.sharedSmoothedReturnsReversed)
            ? [...saved.sharedSmoothedReturnsReversed]
            : null;
        crashMonths = new Set(Array.isArray(saved.crashMonths) ? saved.crashMonths : []);
        peakValue = asNumber(saved.peakValue, settings.initialInvestment);
        maxDrawdown = asNumber(saved.maxDrawdown, 0);
        lastMonthlyReturn = asNumber(saved.lastMonthlyReturn, baseDailyReturnRate);
        crowdSentiment = asNumber(saved.crowdSentiment, 0);
        currentMonth = simulationData[simulationData.length - 1].month;
        syncModeUi();
        syncSecondaryUi();
        rebuildChartData('resize');
        updateSummary();
        updateLearningNote();
        updateRiskTip();
        renderEvents();
        chart.$pauseMonth = currentMonth > 0 ? currentMonth : null;
        chart.$crashMonths = Array.from(crashMonths).sort((a, b) => a - b);
        const maxM = parseInt(monthsInput.value, 10) || 120;
        if (currentMonth >= maxM) {
            statusDisplay.textContent = i18n.complete;
            statusDisplay.style.background =
                'color-mix(in srgb, var(--c-primary) 30%, var(--c-surface))';
        } else if (currentMonth > 0) {
            statusDisplay.textContent = i18n.paused;
            statusDisplay.style.background =
                'color-mix(in srgb, var(--c-secondary) 20%, var(--c-surface))';
        } else {
            statusDisplay.textContent = i18n.ready;
            statusDisplay.style.background =
                'color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%)';
        }
        return true;
    }

    function calculateNextMonth() {
        const lastEntry = simulationData[simulationData.length - 1];
        const stepMonth = lastEntry.month + 1;
        const maxM = parseInt(monthsInput.value, 10) || 120;
        ensureSharedPath(maxM);

        const marketReturn = marketReturnForStep(stepMonth);
        if (marketReturn < -0.12) {
            crashMonths.add(stepMonth);
        }

        const nextEntry = applyPortfolioStep(lastEntry, marketReturn, dailyContribution);
        simulationData.push(nextEntry);
        currentMonth = nextEntry.month;

        if (secondaryScenario === 'compare') {
            const lastC = simulationDataCompare[simulationDataCompare.length - 1];
            const extra = Math.max(0, parseFloat(compareExtraInput?.value) || 0);
            const mc = dailyContribution + extra / 30;
            simulationDataCompare.push(applyPortfolioStep(lastC, marketReturn, mc));
        }

        if (secondaryScenario === 'sor') {
            const lastS = simulationDataSor[simulationDataSor.length - 1];
            const mrSor = marketReturnForStepSor(stepMonth);
            simulationDataSor.push(applyPortfolioStep(lastS, mrSor, dailyContribution));
        }

        if (nextEntry.value > peakValue) {
            peakValue = nextEntry.value;
            pushEvent(i18n.newHigh.replace(':month', String(nextEntry.month)));
        }

        const drawdown = (nextEntry.value - peakValue) / peakValue;
        maxDrawdown = Math.min(maxDrawdown, drawdown);
        if (drawdown < -0.1 && drawdown.toFixed(2) === maxDrawdown.toFixed(2)) {
            pushEvent(i18n.drawdownCoaching.replace(':pct', Math.abs(drawdown * 100).toFixed(1)));
        }
    }

    function rebuildChartData(animation = 'none') {
        chart.data.labels = simulationData.map((entry) => entry.month);
        if (isPlaygroundMode) {
            chart.data.datasets[0].data = simulationData.map((entry) =>
                convertAmount(lifetimeContributedOf(entry)),
            );
            chart.data.datasets[1].data = simulationData.map((entry) =>
                convertAmount(entry.inflationAdjusted),
            );
            chart.data.datasets[2].data = simulationData.map((entry) => convertAmount(entry.value));
            chart.data.datasets[3].data = [];
            chart.data.datasets[4].data = [];
            chart.data.datasets[5].data = simulationData.map((entry) =>
                convertAmount(entry.value - lifetimeContributedOf(entry)),
            );
        } else {
            chart.data.datasets[0].data = simulationData.map((entry) => convertAmount(entry.contributions));
            chart.data.datasets[1].data = simulationData.map((entry) => convertAmount(entry.inflationAdjusted));
            chart.data.datasets[2].data = simulationData.map((entry) => convertAmount(entry.value));
            chart.data.datasets[5].data = [];

            if (secondaryScenario === 'compare' && simulationDataCompare.length === simulationData.length) {
                chart.data.datasets[3].data = simulationDataCompare.map((entry) => convertAmount(entry.value));
            } else {
                chart.data.datasets[3].data = [];
            }

            if (secondaryScenario === 'sor' && simulationDataSor.length === simulationData.length) {
                chart.data.datasets[4].data = simulationDataSor.map((entry) => convertAmount(entry.value));
            } else {
                chart.data.datasets[4].data = [];
            }
        }

        chart.$crashMonths = Array.from(crashMonths).sort((a, b) => a - b);
        updateCurrencyLabels();
        chart.update(animation);
    }

    function deltaArrowMarkup(diff) {
        const isUp = diff >= 0;
        const arrow = isUp ? '\u2191' : '\u2193';
        const cls = isUp ? 'sim-deltaArrow sim-deltaArrow--up' : 'sim-deltaArrow sim-deltaArrow--down';
        return `<span class="${cls}" aria-hidden="true">${arrow}</span>`;
    }

    function formatDeltaLine(cur, prev, vsBase) {
        if (prev == null || prev === cur) return '';
        const diff = cur - prev;
        const pct = prev !== 0 ? (diff / Math.abs(prev)) * 100 : 0;
        const sign = diff >= 0 ? '+' : '-';
        const main = `${sign}${formatConverted(convertAmount(Math.abs(diff)))} ${i18n.mom} (${sign}${Math.abs(pct).toFixed(1)}%)`;
        let html = `${main} ${deltaArrowMarkup(diff)}`;
        if (vsBase != null && vsBase !== 0) {
            const vsPct = ((cur - vsBase) / vsBase) * 100;
            html += ` \u00B7 ${i18n.vsContributed} ${vsPct >= 0 ? '+' : ''}${vsPct.toFixed(1)}%`;
        }
        return html;
    }

    function updateSummary() {
        const data = simulationData[simulationData.length - 1] || simulationData[0];
        const prev = simulationData.length > 1 ? simulationData[simulationData.length - 2] : null;
        const contribBase = lifetimeContributedOf(data);
        const totalGain = data.value - contribBase;
        const years = Math.max(data.month, 1) / 365;
        const cagr = Math.pow(data.value / Math.max(contribBase, 1e-6), 1 / years) - 1;

        currentValueEl.textContent = formatCurrency(data.value);
        totalContributedEl.textContent = formatCurrency(data.contributions);
        totalGainEl.textContent = formatCurrency(totalGain);
        totalGainEl.style.color = totalGain >= 0 ? primaryColor : '#ef4444';
        realValueEl.textContent = formatCurrency(data.inflationAdjusted);
        drawdownEl.textContent = `${(maxDrawdown * 100).toFixed(1)}%`;
        cagrEl.textContent = `${(cagr * 100).toFixed(2)}%`;

        if (meta.current && prev) {
            meta.current.innerHTML = formatDeltaLine(data.value, prev.value, null);
            meta.current.className = 'sim-kpiMeta';
        } else if (meta.current) {
            meta.current.textContent = '';
        }

        if (meta.contributed && prev) {
            const d = data.contributions - prev.contributions;
            meta.contributed.textContent =
                d > 0 ? `+${formatConverted(convertAmount(d))} ${i18n.thisMonth}` : '';
        } else if (meta.contributed) meta.contributed.textContent = '';

        if (meta.gain) {
            const gainPctVsContrib = contribBase > 0 ? (totalGain / contribBase) * 100 : 0;
            if (contribBase > 0) {
                const body = `${totalGain >= 0 ? '+' : '-'}${formatConverted(Math.abs(convertAmount(totalGain)))} (${gainPctVsContrib >= 0 ? '+' : ''}${gainPctVsContrib.toFixed(1)}% ${i18n.vsContributed})`;
                meta.gain.innerHTML = `${body} ${deltaArrowMarkup(totalGain)}`;
            } else {
                meta.gain.textContent = '';
            }
            meta.gain.className = 'sim-kpiMeta';
        }

        if (meta.real && prev) {
            meta.real.innerHTML = formatDeltaLine(data.inflationAdjusted, prev.inflationAdjusted, null);
            meta.real.className = 'sim-kpiMeta';
        } else if (meta.real) {
            meta.real.textContent = '';
        }

        if (meta.drawdown) {
            meta.drawdown.textContent =
                maxDrawdown < 0
                    ? `${i18n.fromPeak} ${(maxDrawdown * 100).toFixed(1)}%`
                    : '';
        }

        if (meta.cagr) {
            meta.cagr.textContent = `${i18n.onContributed} \u00B7 ${(cagr * 100).toFixed(2)}% ${i18n.cagr}`;
        }

        updatePlaygroundTradingDesk();
        schedulePersistRunnerState();
    }

    function updateLearningNote() {
        if (learningNoteEl) {
            if (isPlaygroundMode) {
                learningNoteEl.textContent = i18n.playgroundLesson || i18n.stayInvested;
            } else {
                const preset = presetConfigs[activePresetKey] ?? presetConfigs.balanced;
                learningNoteEl.textContent = preset.lesson || i18n.stayInvested;
            }
        }
    }

    function updateRiskTip() {
        if (!riskTipEl) return;
        const risk = settings.riskAppetite;
        const market = settings.marketInfluence;
        const inflation = settings.inflationRate;
        const investors = settings.investors;
        const tips = [
            risk > 0.6 ? i18n.riskHigh : null,
            market > 0.6 ? i18n.riskMarket : null,
            investors >= 25 ? (i18n.investorsHigh || null) : null,
            inflation > 0.03 ? i18n.inflHigh : i18n.inflMod,
        ].filter(Boolean);
        let extra = '';
        if (secondaryScenario === 'compare') {
            extra = ` ${i18n.compareExplainer}`;
        } else if (secondaryScenario === 'sor') {
            extra = ` ${i18n.sorExplainer}`;
        }
        riskTipEl.textContent = (tips.join(' ') || i18n.riskDefault) + extra;
    }

    function pushEvent(text) {
        eventLog.unshift({ text, time: new Date() });
        eventLog = eventLog.slice(0, 8);
        renderEvents();
    }

    function renderEvents() {
        if (!eventLogEl) return;
        if (!eventLog.length) {
            eventLogEl.innerHTML = `<li>${i18n.noEvents}</li>`;
            return;
        }
        eventLogEl.innerHTML = eventLog.map((ev) => `<li>${ev.text}</li>`).join('');
    }

    function startSimulation() {
        // Single button toggles run/pause.
        if (isRunning) {
            pauseSimulation();
            return;
        }

        isPlaygroundMode = readPlaygroundModeFromUi();
        const maxMonths = parseInt(monthsInput.value, 10);
        ensureSharedPath(maxMonths);

        isRunning = true;
        setRunPauseButtonState(true);

        const stepSeconds = Math.max(0.1, parseFloat(speedInput.value) || 0.25);
        const speed = stepSeconds * 1000;

        statusDisplay.textContent = i18n.running;
        statusDisplay.style.background = 'color-mix(in srgb, var(--c-primary) 20%, var(--c-surface))';
        chart.$pauseMonth = null;

        intervalId = setInterval(() => {
            if (currentMonth >= maxMonths) {
                pauseSimulation();
                statusDisplay.textContent = i18n.complete;
                statusDisplay.style.background =
                    'color-mix(in srgb, var(--c-primary) 30%, var(--c-surface))';
                return;
            }

            if (isPlaygroundMode) {
                playgroundAdvanceMonth();
            } else {
                calculateNextMonth();
            }
            rebuildChartData('none');
            updateSummary();
        statusDisplay.textContent = i18n.month
                .replace(':current', String(currentMonth))
                .replace(':total', String(maxMonths));
        }, speed);
    }

    function stepOnce() {
        pauseSimulation();
        isPlaygroundMode = readPlaygroundModeFromUi();
        const maxMonths = parseInt(monthsInput.value, 10) || 120;
        ensureSharedPath(maxMonths);
        if (currentMonth >= maxMonths) {
            statusDisplay.textContent = i18n.complete;
            statusDisplay.style.background =
                'color-mix(in srgb, var(--c-primary) 30%, var(--c-surface))';
            return;
        }
        if (isPlaygroundMode) {
            playgroundAdvanceMonth();
        } else {
            calculateNextMonth();
        }
        rebuildChartData('none');
        updateSummary();
        statusDisplay.textContent = i18n.month
            .replace(':current', String(currentMonth))
            .replace(':total', String(maxMonths));
    }

    function pauseSimulation() {
        isRunning = false;
        setRunPauseButtonState(false);

        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }

        statusDisplay.textContent = i18n.paused;
        statusDisplay.style.background =
            'color-mix(in srgb, var(--c-secondary) 20%, var(--c-surface))';
        const last = simulationData[simulationData.length - 1];
        chart.$pauseMonth = last ? last.month : null;
        rebuildChartData('none');
    }

    function resetSimulation() {
        pauseSimulation();
        seedInitialState();
    }

    function sendSnapshot() {
        if (!snapshotUrl || !csrfToken || !simulationData.length || typeof window.fetch !== 'function') {
            return;
        }
        const latestEntry = simulationData[simulationData.length - 1];
        if (!latestEntry) return;
        const contrib = lifetimeContributedOf(latestEntry);
        const totalGain = latestEntry.value - contrib;
        updateSaveStatus(i18n.saving, 'var(--c-on-surface)');

        const history = simulationData.map((row) => ({
            month: row.month,
            value: row.value,
            inflationAdjusted: row.inflationAdjusted,
            contributions: lifetimeContributedOf(row),
        }));

        fetch(snapshotUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                month: latestEntry.month,
                value: latestEntry.value,
                real_value: latestEntry.inflationAdjusted,
                contributions: contrib,
                total_gain: totalGain,
                currency: activeCurrency,
                history,
            }),
        })
            .then((response) => {
                if (!response.ok) throw new Error('Snapshot save failed');
                return response.json();
            })
            .then(() => {
                updateSaveStatus(
                    i18n.savedAt.replace(':time', new Date().toLocaleTimeString()),
                    'var(--c-primary)',
                );
            })
            .catch((error) => {
                console.warn('Unable to save simulation snapshot', error);
                updateSaveStatus(i18n.saveFailed, '#ef4444');
            });
    }

    function handleCurrencyPreferenceChange() {
        currencyRates = loadCachedRates();
        const next = getPreferredCurrency();
        if (next !== activeCurrency) {
            activeCurrency = next;
            rebuildChartData();
            updateSummary();
        }
    }

    const restored = savedRunner && validateSavedRunner(savedRunner) && applySavedRunner(savedRunner);
    if (!restored) {
        seedInitialState();
    }
    syncSecondaryUi();
    setRunPauseButtonState(Boolean(isRunning));

    btnRun.addEventListener('click', startSimulation);
    btnStep.addEventListener('click', stepOnce);
    btnReset.addEventListener('click', resetSimulation);
    btnSave?.addEventListener('click', () => {
        sendSnapshot();
    });

    presetSelect.addEventListener('change', (e) => {
        activePresetKey = e.target.value;
        crowdSentiment = 0;
        invalidatePath();
        updateLearningNote();
        updateRiskTip();
        if (!isRunning) {
            const label = presetConfigs[activePresetKey]?.label ?? i18n.balancedLabel;
            statusDisplay.textContent = i18n.presetLabel.replace(':label', label);
        }
        schedulePersistRunnerState();
    });

    secondarySelect?.addEventListener('change', () => {
        secondaryScenario = secondarySelect.value;
        invalidatePath();
        syncSecondaryUi();
        updateRiskTip();
        seedInitialState();
    });

    compareExtraInput?.addEventListener('change', () => {
        if (secondaryScenario === 'compare') {
            invalidatePath();
            seedInitialState();
        }
    });

    modeClassicRadio?.addEventListener('change', () => {
        if (modeClassicRadio.checked) {
            pauseSimulation();
            seedInitialState();
        }
    });
    modePlaygroundRadio?.addEventListener('change', () => {
        if (modePlaygroundRadio.checked) {
            pauseSimulation();
            seedInitialState();
        }
    });

    document.querySelectorAll('.pg-buy').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!readPlaygroundModeFromUi()) return;
            isPlaygroundMode = true;
            playgroundBuy(btn.getAttribute('data-amount'));
        });
    });
    document.getElementById('playground-custom-buy')?.addEventListener('click', () => {
        if (!readPlaygroundModeFromUi()) return;
        isPlaygroundMode = true;
        const inp = document.getElementById('playground-custom-amount');
        playgroundBuy(inp?.value);
    });
    document.querySelectorAll('.pg-sell').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (!readPlaygroundModeFromUi()) return;
            isPlaygroundMode = true;
            playgroundSell(parseFloat(btn.getAttribute('data-fraction') || '0'));
        });
    });

    monthsInput?.addEventListener('change', () => {
        invalidatePath();
        schedulePersistRunnerState();
    });

    speedInput?.addEventListener('change', () => {
        schedulePersistRunnerState();
    });

    document.getElementById('playground-custom-amount')?.addEventListener('change', () => {
        schedulePersistRunnerState();
    });

    window.addEventListener('storage', (event) => {
        if (event.key === 'nosleguma-currency-preference') {
            handleCurrencyPreferenceChange();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const cfgEl = document.getElementById('simulation-runner-config');
    if (!cfgEl) return;
    let config;
    try {
        config = JSON.parse(cfgEl.textContent);
    } catch (e) {
        console.warn('Invalid simulation-runner-config JSON', e);
        return;
    }
    initFromConfig(config);
});
