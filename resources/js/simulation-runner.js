import Chart from 'chart.js/auto';

function asNumber(value, fallback = 0) {
    if (typeof value === 'string') {
        value = value.replace(/[^0-9.\-]/g, '');
    }
    const num = Number(value);
    return Number.isFinite(num) ? num : fallback;
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
    const { snapshotUrl, csrfToken, settings: rawSettings, i18n } = config;

    const chartCanvas = document.getElementById('sim-chart');
    const btnRun = document.getElementById('btn-run');
    const btnStep = document.getElementById('btn-step');
    const btnPause = document.getElementById('btn-pause');
    const btnReset = document.getElementById('btn-reset');
    const btnSave = document.getElementById('btn-save');
    const monthsInput = document.getElementById('months-input');
    const speedInput = document.getElementById('speed-input');
    const presetSelect = document.getElementById('preset-select');
    const secondarySelect = document.getElementById('secondary-scenario');
    const compareExtraWrap = document.getElementById('compare-extra-wrap');
    const compareExtraInput = document.getElementById('compare-extra-monthly');
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

    if (!chartCanvas || !btnRun || !btnPause || !btnReset || !monthsInput || !speedInput || !presetSelect) {
        console.warn('Simulation controls are missing from the DOM. Skipping initialization.');
        return;
    }

    const chartCtx = chartCanvas.getContext('2d');
    if (!chartCtx || typeof Chart === 'undefined') {
        console.warn('Chart.js is not available, unable to render simulation chart.');
        return;
    }

    const settings = {
        initialInvestment: asNumber(rawSettings.initialInvestment, 0),
        monthlyContribution: asNumber(rawSettings.monthlyContribution, 0),
        growthRate: asNumber(rawSettings.growthRate, 0.05),
        inflationRate: asNumber(rawSettings.inflationRate, 0.02),
        riskAppetite: Math.min(1, Math.max(0, asNumber(rawSettings.riskAppetite, 0.5))),
        marketInfluence: Math.min(1, Math.max(0, asNumber(rawSettings.marketInfluence, 0.5))),
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
            recoveryBias: 0.003,
            lesson: i18n.balancedLesson,
        },
        growth: {
            label: i18n.growthLabel,
            expectedAnnual: Math.max(settings.growthRate + 0.02, settings.growthRate),
            monthlyVolatility: 0.03,
            shockChance: 0,
            shockImpact: () => 0,
            recoveryBias: 0.006,
            lesson: i18n.growthLesson,
        },
        defensive: {
            label: i18n.defensiveLabel,
            expectedAnnual: Math.max(settings.growthRate - 0.02, 0.02),
            monthlyVolatility: 0.012,
            shockChance: 0,
            shockImpact: () => 0,
            recoveryBias: 0.002,
            lesson: i18n.defensiveLesson,
        },
        volatile: {
            label: i18n.volatileLabel,
            expectedAnnual: settings.growthRate,
            monthlyVolatility: 0.045,
            shockChance: 0,
            shockImpact: () => 0,
            recoveryBias: 0.004,
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

    const baseMonthlyReturnRate = Math.pow(1 + settings.growthRate, 1 / 12) - 1;
    const monthlyInflationRate = Math.pow(1 + settings.inflationRate, 1 / 12) - 1;
    const volatilityInfluence = (settings.riskAppetite + settings.marketInfluence) / 2;

    const annualFeeRate = 0.002;
    const monthlyFeeRate = annualFeeRate / 12;

    let isRunning = false;
    let currentMonth = 0;
    let simulationData = [];
    let simulationDataCompare = [];
    let simulationDataSor = [];
    let intervalId = null;
    let peakValue = settings.initialInvestment;
    let maxDrawdown = 0;
    let activePresetKey = 'balanced';
    let eventLog = [];
    let lastMonthlyReturn = baseMonthlyReturnRate;

    let sharedSmoothedReturns = null;
    let sharedSmoothedReturnsReversed = null;

    let secondaryScenario = secondarySelect?.value || 'none';

    const primaryColor =
        getComputedStyle(document.documentElement).getPropertyValue('--c-primary').trim() || '#07a05a';
    const secondaryColor =
        getComputedStyle(document.documentElement).getPropertyValue('--c-secondary').trim() || '#d98e12';
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
            ],
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
                        padding: 14,
                        filter(legendItem) {
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
                        text: 'Month',
                        font: { size: 13, weight: '600' },
                    },
                    grid: { display: false },
                },
                y: {
                    title: {
                        display: true,
                        text: `Value (${activeCurrency})`,
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

    let resizeTimeout;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => chart.resize(), 100);
    });

    function syncSecondaryUi() {
        if (compareExtraWrap) {
            compareExtraWrap.style.display = secondaryScenario === 'compare' ? 'grid' : 'none';
        }
        chart.data.datasets[3].hidden = secondaryScenario !== 'compare';
        chart.data.datasets[4].hidden = secondaryScenario !== 'sor';
        chart.update('none');
    }

    function invalidatePath() {
        sharedSmoothedReturns = null;
        sharedSmoothedReturnsReversed = null;
    }

    function buildSmoothedPath(length) {
        const arr = [];
        let last = baseMonthlyReturnRate;
        const preset = presetConfigs[activePresetKey] ?? presetConfigs.balanced;

        for (let step = 1; step <= length; step++) {
            const targetMonthly = Math.pow(1 + preset.expectedAnnual, 1 / 12) - 1;
            const cycleBias = Math.sin((step / 84) * Math.PI  * 2) * 0.003;
            const monthlyBase = targetMonthly + cycleBias + preset.recoveryBias;
            const vol = preset.monthlyVolatility * (1 + volatilityInfluence * 0.6);
            let r = realisticReturn(monthlyBase, vol);
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
        const targetMonthly = Math.pow(1 + preset.expectedAnnual, 1 / 12) - 1;
        const cycleBias = Math.sin((stepMonth / 84) * Math.PI * 2) * 0.003;
        const monthlyBase = targetMonthly + cycleBias + preset.recoveryBias;
        const vol = preset.monthlyVolatility * (1 + volatilityInfluence * 0.6);
        let r = realisticReturn(monthlyBase, vol);
        if (r <= -0.12 || r >= 0.15) {
            pushEvent(i18n.fatTailEvent.replace(':pct', (r * 100).toFixed(1)));
        }
        if (activePresetKey === 'shock' && Math.random() < preset.shockChance) {
            const extra = preset.shockImpact();
            r += extra;
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
        const nextMonth = lastEntry.month + 1;
        const valueAfterGrowth = lastEntry.value * (1 + marketReturn);
        const valueAfterFees = valueAfterGrowth * (1 - monthlyFeeRate);
        const contributions = lastEntry.contributions + monthlyContribution;
        const newValue = Math.max(0, valueAfterFees + monthlyContribution);
        const inflationAdjusted = newValue / Math.pow(1 + monthlyInflationRate, nextMonth);
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
        chart.data.datasets[2].label = `${i18n.chartNominal} (${activeCurrency})`;
        chart.data.datasets[3].label = `${i18n.chartCompare} (${activeCurrency})`;
        chart.data.datasets[4].label = `${i18n.chartSor} (${activeCurrency})`;
        if (chart.options?.scales?.y?.title) {
            chart.options.scales.y.title.text = `Value (${activeCurrency})`;
        }
    }

    let crashMonths = new Set();

    function seedInitialState() {
        lastMonthlyReturn = baseMonthlyReturnRate;
        currentMonth = 0;
        peakValue = settings.initialInvestment;
        maxDrawdown = 0;
        eventLog = [];
        crashMonths = new Set();
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
        invalidatePath();

        rebuildChartData('resize');
        updateSummary();
        updateLearningNote();
        updateRiskTip();
        renderEvents();
        chart.$pauseMonth = null;
        chart.$crashMonths = [];
        statusDisplay.textContent = i18n.ready;
        statusDisplay.style.background =
            'color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%)';
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

        const nextEntry = applyPortfolioStep(lastEntry, marketReturn, settings.monthlyContribution);
        simulationData.push(nextEntry);
        currentMonth = nextEntry.month;

        if (secondaryScenario === 'compare') {
            const lastC = simulationDataCompare[simulationDataCompare.length - 1];
            const extra = Math.max(0, parseFloat(compareExtraInput?.value) || 0);
            const mc = settings.monthlyContribution + extra;
            simulationDataCompare.push(applyPortfolioStep(lastC, marketReturn, mc));
        }

        if (secondaryScenario === 'sor') {
            const lastS = simulationDataSor[simulationDataSor.length - 1];
            const mrSor = marketReturnForStepSor(stepMonth);
            simulationDataSor.push(applyPortfolioStep(lastS, mrSor, settings.monthlyContribution));
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
        chart.data.datasets[0].data = simulationData.map((entry) => convertAmount(entry.contributions));
        chart.data.datasets[1].data = simulationData.map((entry) => convertAmount(entry.inflationAdjusted));
        chart.data.datasets[2].data = simulationData.map((entry) => convertAmount(entry.value));

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

        chart.$crashMonths = Array.from(crashMonths).sort((a, b) => a - b);
        updateCurrencyLabels();
        chart.update(animation);
    }

    function formatDeltaLine(cur, prev, vsBase) {
        if (prev == null || prev === cur) return '';
        const diff = cur - prev;
        const pct = prev !== 0 ? (diff / Math.abs(prev)) * 100 : 0;
        const arrow = diff >= 0 ? '\u2191' : '\u2193';
        const sign = diff >= 0 ? '+' : '-';
        const main = `${sign}${formatConverted(convertAmount(Math.abs(diff)))} ${i18n.mom} (${sign}${Math.abs(pct).toFixed(1)}%) ${arrow}`;
        if (vsBase != null && vsBase !== 0) {
            const vsPct = ((cur - vsBase) / vsBase) * 100;
            return `${main} \u00B7 ${i18n.vsContributed} ${vsPct >= 0 ? '+' : ''}${vsPct.toFixed(1)}%`;
        }
        return main;
    }

    function updateSummary() {
        const data = simulationData[simulationData.length - 1] || simulationData[0];
        const prev = simulationData.length > 1 ? simulationData[simulationData.length - 2] : null;
        const totalGain = data.value - data.contributions;
        const years = Math.max(data.month, 1) / 12;
        const cagr = Math.pow(data.value / Math.max(data.contributions, 1e-6), 1 / years) - 1;

        currentValueEl.textContent = formatCurrency(data.value);
        totalContributedEl.textContent = formatCurrency(data.contributions);
        totalGainEl.textContent = formatCurrency(totalGain);
        totalGainEl.style.color = totalGain >= 0 ? primaryColor : '#ef4444';
        realValueEl.textContent = formatCurrency(data.inflationAdjusted);
        drawdownEl.textContent = `${(maxDrawdown * 100).toFixed(1)}%`;
        cagrEl.textContent = `${(cagr * 100).toFixed(2)}%`;

        if (meta.current && prev) {
            meta.current.textContent = formatDeltaLine(data.value, prev.value, null);
            meta.current.className =
                'sim-kpiMeta' + (data.value >= prev.value ? ' sim-kpiMeta--up' : ' sim-kpiMeta--down');
        } else if (meta.current) meta.current.textContent = '';

        if (meta.contributed && prev) {
            const d = data.contributions - prev.contributions;
            meta.contributed.textContent =
                d > 0 ? `+${formatConverted(convertAmount(d))} ${i18n.thisMonth}` : '';
        } else if (meta.contributed) meta.contributed.textContent = '';

        if (meta.gain) {
            const gainPctVsContrib = data.contributions > 0 ? (totalGain / data.contributions) * 100 : 0;
            meta.gain.textContent =
                data.contributions > 0
                    ? `${totalGain >= 0 ? '+' : '-'}${formatConverted(Math.abs(convertAmount(totalGain)))} (${gainPctVsContrib >= 0 ? '+' : ''}${gainPctVsContrib.toFixed(1)}% ${i18n.vsContributed})`
                    : '';
            meta.gain.className = 'sim-kpiMeta' + (totalGain >= 0 ? ' sim-kpiMeta--up' : ' sim-kpiMeta--down');
        }

        if (meta.real && prev) {
            meta.real.textContent = formatDeltaLine(data.inflationAdjusted, prev.inflationAdjusted, null);
            meta.real.className =
                'sim-kpiMeta' +
                (data.inflationAdjusted >= prev.inflationAdjusted
                    ? ' sim-kpiMeta--up'
                    : ' sim-kpiMeta--down');
        } else if (meta.real) meta.real.textContent = '';

        if (meta.drawdown) {
            meta.drawdown.textContent =
                maxDrawdown < 0
                    ? `${i18n.fromPeak} ${(maxDrawdown * 100).toFixed(1)}%`
                    : '';
        }

        if (meta.cagr) {
            meta.cagr.textContent = `${i18n.onContributed} \u00B7 ${(cagr * 100).toFixed(2)}% ${i18n.cagr}`;
        }
    }

    function updateLearningNote() {
        const preset = presetConfigs[activePresetKey] ?? presetConfigs.balanced;
        if (learningNoteEl) {
            learningNoteEl.textContent = preset.lesson || i18n.stayInvested;
        }
    }

    function updateRiskTip() {
        if (!riskTipEl) return;
        const risk = settings.riskAppetite;
        const market = settings.marketInfluence;
        const inflation = settings.inflationRate;
        const tips = [
            risk > 0.6 ? i18n.riskHigh : null,
            market > 0.6 ? i18n.riskMarket : null,
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
        if (isRunning) return;

        const maxMonths = parseInt(monthsInput.value, 10);
        ensureSharedPath(maxMonths);

        isRunning = true;
        btnRun.disabled = true;
        btnPause.disabled = false;

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
        const maxMonths = parseInt(monthsInput.value, 10) || 120;
        ensureSharedPath(maxMonths);
        if (currentMonth >= maxMonths) {
            statusDisplay.textContent = i18n.complete;
            statusDisplay.style.background =
                'color-mix(in srgb, var(--c-primary) 30%, var(--c-surface))';
            return;
        }
        calculateNextMonth();
        rebuildChartData('none');
        updateSummary();
        statusDisplay.textContent = i18n.month
            .replace(':current', String(currentMonth))
            .replace(':total', String(maxMonths));
    }

    function pauseSimulation() {
        isRunning = false;
        btnRun.disabled = false;
        btnPause.disabled = true;

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
        const totalGain = latestEntry.value - latestEntry.contributions;
        updateSaveStatus(i18n.saving, 'var(--c-on-surface)');

        const history = simulationData.map((row) => ({
            month: row.month,
            value: row.value,
            inflationAdjusted: row.inflationAdjusted,
            contributions: row.contributions,
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
                contributions: latestEntry.contributions,
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

    seedInitialState();
    syncSecondaryUi();

    btnRun.addEventListener('click', startSimulation);
    btnStep.addEventListener('click', stepOnce);
    btnPause.addEventListener('click', pauseSimulation);
    btnReset.addEventListener('click', resetSimulation);
    btnSave?.addEventListener('click', () => {
        sendSnapshot();
    });

    presetSelect.addEventListener('change', (e) => {
        activePresetKey = e.target.value;
        invalidatePath();
        updateLearningNote();
        updateRiskTip();
        if (!isRunning) {
            const label = presetConfigs[activePresetKey]?.label ?? i18n.balancedLabel;
            statusDisplay.textContent = i18n.presetLabel.replace(':label', label);
        }
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

    monthsInput?.addEventListener('change', () => {
        invalidatePath();
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
