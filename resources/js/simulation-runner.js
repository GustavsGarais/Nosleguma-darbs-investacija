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
function evolveCrowdSentiment(prev) {
    let s = 0.87 * prev + gaussianRandom() * 0.005;
    if (Math.random() < 0.092) {
        s -= 0.014 + Math.random() * 0.038;
    }
    if (Math.random() < 0.064) {
        s += 0.007 + Math.random() * 0.019;
    }
    return Math.max(-0.12, Math.min(0.09, s));
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
    const btnPause = document.getElementById('btn-pause');
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
    let isPlaygroundMode = false;
    let crowdSentiment = 0;

    function readPlaygroundModeFromUi() {
        return Boolean(modePlaygroundRadio?.checked);
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
    }

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
                        text: 'Month',
                        font: { size: 13, weight: '600' },
                    },
                    grid: { display: false },
                },
                y: {
                    beginAtZero: false,
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

    const dashBody = document.querySelector('.sim-dash-body');
    const dashLead = document.getElementById('sim-dash-lead');
    const btnToggleControls = document.getElementById('btn-toggle-controls');
    if (dashBody && typeof ResizeObserver !== 'undefined') {
        const ro = new ResizeObserver(() => {
            requestAnimationFrame(() => chart.resize());
        });
        ro.observe(dashBody);
    }

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
        requestAnimationFrame(() => chart.resize());
    });

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
        let last = baseMonthlyReturnRate;
        let pathCrowd = 0;
        const preset = presetConfigs[activePresetKey] ?? presetConfigs.balanced;

        for (let step = 1; step <= length; step++) {
            const targetMonthly = Math.pow(1 + preset.expectedAnnual, 1 / 12) - 1;
            const cycleBias = Math.sin((step / 84) * Math.PI  * 2) * 0.003;
            const monthlyBase = targetMonthly + cycleBias + preset.recoveryBias;
            const vol = preset.monthlyVolatility * (1 + volatilityInfluence * 0.6);
            pathCrowd = evolveCrowdSentiment(pathCrowd);
            let r = realisticReturn(monthlyBase, vol) + pathCrowd * 0.62;
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
        crowdSentiment = evolveCrowdSentiment(crowdSentiment);
        let r = realisticReturn(monthlyBase, vol) + crowdSentiment * 0.62;
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
        chart.data.datasets[2].label = isPlaygroundMode
            ? `${i18n.chartNetWorth} (${activeCurrency})`
            : `${i18n.chartNominal} (${activeCurrency})`;
        chart.data.datasets[3].label = `${i18n.chartCompare} (${activeCurrency})`;
        chart.data.datasets[4].label = `${i18n.chartSor} (${activeCurrency})`;
        chart.data.datasets[5].label = `${i18n.chartTotalPL} (${activeCurrency})`;
        if (chart.options?.scales?.y?.title) {
            chart.options.scales.y.title.text = `Value (${activeCurrency})`;
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
        const inflationAdjusted = netWorth / Math.pow(1 + monthlyInflationRate, month);
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
        const newPrice = last.price * (1 + marketReturn) * (1 - monthlyFeeRate);
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
        lastMonthlyReturn = baseMonthlyReturnRate;
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
        lastMonthlyReturn = asNumber(saved.lastMonthlyReturn, baseMonthlyReturnRate);
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
        const contribBase = lifetimeContributedOf(data);
        const totalGain = data.value - contribBase;
        const years = Math.max(data.month, 1) / 12;
        const cagr = Math.pow(data.value / Math.max(contribBase, 1e-6), 1 / years) - 1;

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
            const gainPctVsContrib = contribBase > 0 ? (totalGain / contribBase) * 100 : 0;
            meta.gain.textContent =
                contribBase > 0
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

        isPlaygroundMode = readPlaygroundModeFromUi();
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

    btnRun.addEventListener('click', startSimulation);
    btnStep.addEventListener('click', stepOnce);
    btnPause.addEventListener('click', pauseSimulation);
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
