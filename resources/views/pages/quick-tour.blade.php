@extends('layouts.app')

@section('title', 'Quick Tour Simulation')

@section('content')
<section class="hero" style="padding:48px 0; display:flex; justify-content:center;">
    <div class="hero-content" style="width:min(1100px, 100% - 32px); margin:0 auto; display:grid; gap:24px;">
        <header style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <p style="margin:0; text-transform:uppercase; letter-spacing:0.08em; font-size:12px; color:var(--c-on-surface-2);">Guided demo</p>
                <h1 style="margin:4px 0 8px;">Quick Tour Simulation (read-only template)</h1>
                <p style="margin:0; color:var(--c-on-surface-2); max-width:720px;">
                    Explore a pre-made investment scenario. You can tweak the numeric parameters and watch how the portfolio behaves. The name and speed are fixed so you focus on the money dynamics.
                </p>
            </div>
            <div style="display:flex; gap:12px; align-items:center;">
                <div style="font-size:13px; color:var(--c-on-surface-2);">
                    Speed fixed at <strong>0.50s / step</strong>
                </div>
                <a class="btn btn-outline" href="{{ route('simulations.create') }}">Create your own</a>
            </div>
        </header>

        <div class="auth-card" style="padding:20px; display:grid; gap:20px;">
            <div style="display:flex; flex-wrap:wrap; gap:12px; align-items:center; justify-content:space-between;">
                <div>
                    <p style="margin:0; text-transform:uppercase; letter-spacing:0.06em; font-size:12px; color:var(--c-on-surface-2);">Scenario</p>
                    <h2 style="margin:4px 0 0;">Starter Balanced Portfolio</h2>
                    <p style="margin:4px 0 0; color:var(--c-on-surface-2); font-size:13px;">Name is locked in this tour. Adjust values to see impact.</p>
                </div>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button id="qt-run" class="btn btn-primary">▶ Run</button>
                    <button id="qt-step" class="btn btn-secondary">➜ Step</button>
                    <button id="qt-pause" class="btn btn-secondary" disabled>⏸ Pause</button>
                    <button id="qt-reset" class="btn btn-outline">🔄 Reset</button>
                    <div id="qt-status" style="padding:8px 12px; border:1px solid var(--c-border); border-radius:10px; background:color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">Ready</div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px;">
                <label style="display:grid; gap:6px;">
                    <span>Duration (months)</span>
                    <input id="qt-months" type="number" min="12" max="600" step="12" value="120" class="footer-email-input" />
                </label>
                <label style="display:grid; gap:6px;">
                    <span>Speed (seconds/step)</span>
                    <input id="qt-speed" type="number" value="0.50" class="footer-email-input" disabled />
                </label>
                <label style="display:grid; gap:6px;">
                    <span>Initial Investment (€)</span>
                    <input id="qt-initial" type="number" step="0.01" value="5000" class="footer-email-input" />
                </label>
                <label style="display:grid; gap:6px;">
                    <span>Monthly Contribution (€)</span>
                    <input id="qt-monthly" type="number" step="0.01" value="250" class="footer-email-input" />
                </label>
                <label style="display:grid; gap:6px;">
                    <span>Growth Rate (annual, 0-1)</span>
                    <input id="qt-growth" type="number" step="0.001" min="0" max="1" value="0.07" class="footer-email-input" />
                </label>
                <label style="display:grid; gap:6px;">
                    <span>Risk Appetite (0-1)</span>
                    <input id="qt-risk" type="number" step="0.01" min="0" max="1" value="0.5" class="footer-email-input" />
                </label>
                <label style="display:grid; gap:6px;">
                    <span>Market Influence (0-1)</span>
                    <input id="qt-market" type="number" step="0.01" min="0" max="1" value="0.5" class="footer-email-input" />
                </label>
                <label style="display:grid; gap:6px;">
                    <span>Inflation Rate (annual, 0-1)</span>
                    <input id="qt-inflation" type="number" step="0.001" min="0" max="1" value="0.02" class="footer-email-input" />
                </label>
            </div>

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:12px;">
                <div class="auth-card" style="padding:14px;">
                    <p style="margin:0; font-size:12px; color:var(--c-on-surface-2); text-transform:uppercase;">Current Value</p>
                    <p id="qt-current" style="margin:4px 0 0; font-size:22px; font-weight:700; color:var(--c-primary);">€0.00</p>
                </div>
                <div class="auth-card" style="padding:14px;">
                    <p style="margin:0; font-size:12px; color:var(--c-on-surface-2); text-transform:uppercase;">Contributed</p>
                    <p id="qt-contrib" style="margin:4px 0 0; font-size:22px; font-weight:700;">€0.00</p>
                </div>
                <div class="auth-card" style="padding:14px;">
                    <p style="margin:0; font-size:12px; color:var(--c-on-surface-2); text-transform:uppercase;">Gain</p>
                    <p id="qt-gain" style="margin:4px 0 0; font-size:22px; font-weight:700;">€0.00</p>
                </div>
                <div class="auth-card" style="padding:14px;">
                    <p style="margin:0; font-size:12px; color:var(--c-on-surface-2); text-transform:uppercase;">Real (inflation adj.)</p>
                    <p id="qt-real" style="margin:4px 0 0; font-size:22px; font-weight:700;">€0.00</p>
                </div>
            </div>

            <div class="auth-card" style="padding:16px;">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
                    <h3 style="margin:0;">Simulation preview</h3>
                    <p style="margin:0; color:var(--c-on-surface-2); font-size:13px;">Speed is fixed to 0.50s per step. Names are locked for this demo.</p>
                </div>
                <div style="position:relative; height:380px; margin-top:12px;">
                    <canvas id="qt-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const chartCanvas = document.getElementById('qt-chart');
    const btnRun = document.getElementById('qt-run');
    const btnStep = document.getElementById('qt-step');
    const btnPause = document.getElementById('qt-pause');
    const btnReset = document.getElementById('qt-reset');
    const statusDisplay = document.getElementById('qt-status');

    const monthsInput = document.getElementById('qt-months');
    const speedInput = document.getElementById('qt-speed'); // disabled
    const initialInput = document.getElementById('qt-initial');
    const monthlyInput = document.getElementById('qt-monthly');
    const growthInput = document.getElementById('qt-growth');
    const riskInput = document.getElementById('qt-risk');
    const marketInput = document.getElementById('qt-market');
    const inflationInput = document.getElementById('qt-inflation');

    const currentEl = document.getElementById('qt-current');
    const contribEl = document.getElementById('qt-contrib');
    const gainEl = document.getElementById('qt-gain');
    const realEl = document.getElementById('qt-real');

    if (!chartCanvas || typeof Chart === 'undefined') {
        console.warn('Chart.js not available');
        return;
    }

    const asNumber = (val, fallback = 0) => {
        const num = Number(val);
        return Number.isFinite(num) ? num : fallback;
    };

    let isRunning = false;
    let currentMonth = 0;
    let simulationData = [];
    let intervalId = null;
    let settings = loadSettings();

    const primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--c-primary').trim() || '#07a05a';
    const secondaryColor = getComputedStyle(document.documentElement).getPropertyValue('--c-secondary').trim() || '#d98e12';

    const chart = new Chart(chartCanvas.getContext('2d'), {
        type: 'line',
        data: { labels: [], datasets: [
            { label: 'Nominal', data: [], borderColor: primaryColor, backgroundColor: primaryColor + '20', borderWidth: 3, fill: true, tension: 0.35, pointRadius: 0 },
            { label: 'Real (inflation adj.)', data: [], borderColor: secondaryColor, backgroundColor: secondaryColor + '15', borderWidth: 2, borderDash: [5,5], fill: false, tension: 0.35, pointRadius: 0 }
        ]},
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            scales: {
                x: { title: { display: true, text: 'Month' }, grid: { display: false } },
                y: { title: { display: true, text: 'Value (€)' },
                    ticks: { callback: (v) => '€' + Number(v).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}) }
                }
            },
            animation: { duration: 0 }
        }
    });

    function loadSettings() {
        return {
            months: Math.max(12, asNumber(monthsInput.value, 120)),
            speedMs: 500, // fixed 0.50s
            initialInvestment: asNumber(initialInput.value, 5000),
            monthlyContribution: asNumber(monthlyInput.value, 250),
            growthRate: Math.min(1, Math.max(0, asNumber(growthInput.value, 0.07))),
            riskAppetite: Math.min(1, Math.max(0, asNumber(riskInput.value, 0.5))),
            marketInfluence: Math.min(1, Math.max(0, asNumber(marketInput.value, 0.5))),
            inflationRate: Math.min(1, Math.max(0, asNumber(inflationInput.value, 0.02))),
        };
    }

    function gaussian() {
        let u = 0, v = 0;
        while (u === 0) u = Math.random();
        while (v === 0) v = Math.random();
        return Math.sqrt(-2.0 * Math.log(u)) * Math.cos(2.0 * Math.PI * v);
    }

    function seedInitial() {
        settings = loadSettings();
        currentMonth = 0;
        simulationData = [{
            month: 0,
            value: settings.initialInvestment,
            real: settings.initialInvestment,
            contributions: settings.initialInvestment,
            interest: 0
        }];
        rebuildChart();
        updateSummary();
        statusDisplay.textContent = 'Ready';
        statusDisplay.style.background = 'color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%)';
    }

    function nextMonthlyReturn() {
        const baseMonthly = Math.pow(1 + settings.growthRate, 1/12) - 1;
        const vol = (settings.riskAppetite + settings.marketInfluence) / 2;
        const noise = gaussian() * (0.03 + vol * 0.04);
        const shock = Math.random() < 0.05 ? -0.12 : 0;
        return Math.max(-0.3, Math.min(baseMonthly + noise + shock, 0.12));
    }

    function stepOnce() {
        const last = simulationData[simulationData.length - 1];
        const nextMonth = last.month + 1;
        const monthlyReturn = nextMonthlyReturn();
        const grown = last.value * (1 + monthlyReturn);
        const newValue = Math.max(0, grown + settings.monthlyContribution);
        const contributions = last.contributions + settings.monthlyContribution;
        const monthlyInfl = Math.pow(1 + settings.inflationRate, 1/12) - 1;
        const real = newValue / Math.pow(1 + monthlyInfl, nextMonth);

        simulationData.push({
            month: nextMonth,
            value: newValue,
            real,
            contributions,
            interest: grown - last.value
        });
        currentMonth = nextMonth;
    }

    function rebuildChart() {
        chart.data.labels = simulationData.map(d => d.month);
        chart.data.datasets[0].data = simulationData.map(d => d.value);
        chart.data.datasets[1].data = simulationData.map(d => d.real);
        chart.update();
    }

    function updateSummary() {
        const last = simulationData[simulationData.length - 1];
        const gain = last.value - last.contributions;
        currentEl.textContent = formatCurrency(last.value);
        contribEl.textContent = formatCurrency(last.contributions);
        gainEl.textContent = formatCurrency(gain);
        gainEl.style.color = gain >= 0 ? primaryColor : '#ef4444';
        realEl.textContent = formatCurrency(last.real);
    }

    function formatCurrency(v) {
        return '€' + Number(v).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function startRun() {
        if (isRunning) return;
        settings = loadSettings();
        isRunning = true;
        btnRun.disabled = true;
        btnPause.disabled = false;
        statusDisplay.textContent = 'Running...';
        statusDisplay.style.background = 'color-mix(in srgb, var(--c-primary) 20%, var(--c-surface))';

        intervalId = setInterval(() => {
            if (currentMonth >= settings.months) {
                stopRun(true);
                return;
            }
            stepOnce();
            rebuildChart();
            updateSummary();
            statusDisplay.textContent = `Month ${currentMonth} / ${settings.months}`;
        }, settings.speedMs);
    }

    function stopRun(markComplete = false) {
        isRunning = false;
        btnRun.disabled = false;
        btnPause.disabled = true;
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
        statusDisplay.textContent = markComplete ? 'Complete' : 'Paused';
        statusDisplay.style.background = markComplete
            ? 'color-mix(in srgb, var(--c-primary) 30%, var(--c-surface))'
            : 'color-mix(in srgb, var(--c-secondary) 20%, var(--c-surface))';
    }

    function singleStep() {
        stopRun();
        if (currentMonth >= settings.months) {
            statusDisplay.textContent = 'Complete';
            statusDisplay.style.background = 'color-mix(in srgb, var(--c-primary) 30%, var(--c-surface))';
            return;
        }
        settings = loadSettings();
        stepOnce();
        rebuildChart();
        updateSummary();
        statusDisplay.textContent = `Month ${currentMonth} / ${settings.months}`;
    }

    function resetSim() {
        stopRun();
        seedInitial();
    }

    [initialInput, monthlyInput, growthInput, riskInput, marketInput, inflationInput, monthsInput].forEach(inp => {
        inp.addEventListener('change', () => {
            settings = loadSettings();
            seedInitial();
        });
    });

    btnRun.addEventListener('click', startRun);
    btnPause.addEventListener('click', () => stopRun(false));
    btnStep.addEventListener('click', singleStep);
    btnReset.addEventListener('click', resetSim);

    seedInitial();
});
</script>
@endsection

