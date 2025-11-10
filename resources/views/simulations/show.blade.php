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
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:12px; margin-bottom:16px;">
                <label style="display:grid; gap:6px;">
                    <span style="font-weight:600;">Duration (months)</span>
                    <input id="months-input" type="number" min="12" max="600" step="12" value="120" class="footer-email-input" />
                </label>
                <label style="display:grid; gap:6px;">
                    <span style="font-weight:600;">Speed (ms per step)</span>
                    <input id="speed-input" type="number" min="10" max="500" step="10" value="50" class="footer-email-input" />
                </label>
                <div style="display:grid; gap:6px;">
                    <span style="font-weight:600;">Status</span>
                    <div id="status-display" style="padding:10px 12px; border-radius:8px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%); border:1px solid var(--c-border);">
                        Ready
                    </div>
                </div>
            </div>
            <div style="display:flex; flex-wrap:wrap; gap:8px;">
                <button id="btn-run" class="btn btn-primary">▶ Run Simulation</button>
                <button id="btn-pause" class="btn btn-secondary" disabled>⏸ Pause</button>
                <button id="btn-reset" class="btn btn-outline">🔄 Reset</button>
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
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
(function(){
    // Settings from Laravel
    const settings = @json($simulation->settings);
    
    // DOM elements
    const chartCtx = document.getElementById('sim-chart').getContext('2d');
    const btnRun = document.getElementById('btn-run');
    const btnPause = document.getElementById('btn-pause');
    const btnReset = document.getElementById('btn-reset');
    const monthsInput = document.getElementById('months-input');
    const speedInput = document.getElementById('speed-input');
    const statusDisplay = document.getElementById('status-display');
    const currentValueEl = document.getElementById('current-value');
    const totalContributedEl = document.getElementById('total-contributed');
    const totalGainEl = document.getElementById('total-gain');
    const realValueEl = document.getElementById('real-value');

    // Simulation state
    let isRunning = false;
    let currentMonth = 0;
    let simulationData = [];
    let intervalId = null;

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
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += '€' + context.parsed.y.toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                            return label;
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
                        text: 'Value (€)',
                        font: {
                            size: 14,
                            weight: '600'
                        }
                    },
                    ticks: {
                        callback: function(value) {
                            return '€' + value.toLocaleString();
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

    // Calculate next month's values
    function calculateNextMonth() {
        let currentValue;
        
        if (simulationData.length === 0) {
            // First month: start with initial investment
            currentValue = settings.initialInvestment;
        } else {
            // Get last month's value
            const lastValue = simulationData[simulationData.length - 1].value;
            // Add monthly contribution
            currentValue = lastValue + settings.monthlyContribution;
        }

        // Apply market volatility
        const randomness = (Math.random() * 2 - 1); // -1 to 1
        const riskImpact = randomness * settings.riskAppetite * settings.marketInfluence;
        const monthlyReturnRate = settings.growthRate / 12;
        const adjustedReturn = monthlyReturnRate + riskImpact;
        
        // Calculate interest earned this month
        const interestEarned = currentValue * adjustedReturn;
        currentValue = Math.max(0, currentValue + interestEarned);

        // Calculate inflation-adjusted value
        const monthlyInflationRate = settings.inflationRate / 12;
        const inflationAdjusted = currentValue / Math.pow(1 + monthlyInflationRate, currentMonth + 1);

        // Calculate total contributions so far
        const totalContributions = settings.initialInvestment + (currentMonth * settings.monthlyContribution);

        simulationData.push({
            month: currentMonth + 1,
            value: currentValue,
            inflationAdjusted: inflationAdjusted,
            contributions: totalContributions,
            interestEarned: interestEarned
        });
    }

    // Update chart with latest data
    function updateChart() {
        const data = simulationData[simulationData.length - 1];
        chart.data.labels.push(data.month);
        chart.data.datasets[0].data.push(data.value);
        chart.data.datasets[1].data.push(data.inflationAdjusted);
        chart.update('none');
    }

    // Update summary displays
    function updateSummary() {
        const data = simulationData[simulationData.length - 1];
        const totalGain = data.value - data.contributions;

        currentValueEl.textContent = '€' + data.value.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        totalContributedEl.textContent = '€' + data.contributions.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        totalGainEl.textContent = '€' + totalGain.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        totalGainEl.style.color = totalGain >= 0 ? primaryColor : '#ef4444';
        
        realValueEl.textContent = '€' + data.inflationAdjusted.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Start simulation
    function startSimulation() {
        if (isRunning) return;
        
        isRunning = true;
        btnRun.disabled = true;
        btnPause.disabled = false;
        
        const maxMonths = parseInt(monthsInput.value);
        const speed = parseInt(speedInput.value);
        
        statusDisplay.textContent = 'Running...';
        statusDisplay.style.background = 'color-mix(in srgb, var(--c-primary) 20%, var(--c-surface))';
        
        intervalId = setInterval(() => {
            if (currentMonth >= maxMonths) {
                pauseSimulation();
                statusDisplay.textContent = 'Complete';
                statusDisplay.style.background = 'color-mix(in srgb, var(--c-primary) 30%, var(--c-surface))';
                return;
            }
            
            calculateNextMonth();
            updateChart();
            updateSummary();
            currentMonth++;
            statusDisplay.textContent = `Month ${currentMonth} / ${maxMonths}`;
        }, speed);
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
    }

    // Reset simulation
    function resetSimulation() {
        pauseSimulation();
        
        currentMonth = 0;
        simulationData = [];
        
        chart.data.labels = [];
        chart.data.datasets[0].data = [];
        chart.data.datasets[1].data = [];
        chart.update();
        
        const initialValue = settings.initialInvestment;
        currentValueEl.textContent = '€' + initialValue.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        totalContributedEl.textContent = '€' + initialValue.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        totalGainEl.textContent = '€0.00';
        totalGainEl.style.color = '';
        realValueEl.textContent = '€' + initialValue.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        
        statusDisplay.textContent = 'Ready';
        statusDisplay.style.background = 'color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%)';
    }

    // Event listeners
    btnRun.addEventListener('click', startSimulation);
    btnPause.addEventListener('click', pauseSimulation);
    btnReset.addEventListener('click', resetSimulation);
})();
</script>
@endpush