<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ $simulation->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('simulations.edit', $simulation) }}" 
                   class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <form method="POST" action="{{ route('simulations.destroy', $simulation) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Are you sure you want to delete this simulation?')"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Simulation Controls -->
            <div class="bg-white/10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @push('scripts')
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        // Simulation settings from Laravel (for display only)
                        const settings = @json($simulation->settings);
                        const simulationId = {{ $simulation->id }};

                        // Chart setup
                        const ctx = document.getElementById('simulationChart').getContext('2d');
                        const chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: [],
                                datasets: [
                                    {
                                        label: 'Nominal Value',
                                        data: [],
                                        borderColor: 'rgb(34, 197, 94)',
                                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                        tension: 0.3
                                    },
                                    {
                                        label: 'Real Value (Inflation Adjusted)',
                                        data: [],
                                        borderColor: 'rgb(59, 130, 246)',
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        borderDash: [5, 5],
                                        tension: 0.3
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                aspectRatio: 2,
                                plugins: {
                                    legend: { labels: { color: getComputedStyle(document.body).color || '#000' } }
                                },
                                scales: {
                                    y: { beginAtZero: false, ticks: { callback: v => '€' + Number(v).toLocaleString() } },
                                    x: { ticks: { } }
                                }
                            }
                        });

                        // State
                        let isRunning = false;
                        let animationInterval = null;
                        let animationIndex = 0;
                        let serverResults = [];

                        // Elements
                        const runBtn = document.getElementById('runBtn');
                        const pauseBtn = document.getElementById('pauseBtn');
                        const resetBtn = document.getElementById('resetBtn');
                        const monthsInput = document.getElementById('months');
                        const speedInput = document.getElementById('speed');
                        const statusEl = document.getElementById('status');
                        const currentValueEl = document.getElementById('currentValue');
                        const totalContributedEl = document.getElementById('totalContributed');
                        const totalGainEl = document.getElementById('totalGain');
                        const realValueEl = document.getElementById('realValue');

                        runBtn.addEventListener('click', async () => {
                            if (isRunning) return;
                            runBtn.disabled = true;
                            pauseBtn.disabled = false;
                            statusEl.textContent = 'Requesting simulation from server...';

                            const months = parseInt(monthsInput.value) || 120;

                            try {
                                // POST to server to compute results and persist
                                const res = await fetch(`/simulations/${simulationId}/run`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify({ months })
                                });

                                if (!res.ok) throw new Error('Server error');

                                const payload = await res.json();
                                if (!payload.success) throw new Error('Simulation failed');

                                serverResults = payload.data || [];

                                // reset chart and start animating results
                                resetChart();
                                animationIndex = 0;
                                startAnimation();
                            } catch (err) {
                                console.error(err);
                                statusEl.textContent = 'Error running simulation';
                                runBtn.disabled = false;
                                pauseBtn.disabled = true;
                            }
                        });

                        pauseBtn.addEventListener('click', () => {
                            pauseAnimation();
                        });

                        resetBtn.addEventListener('click', () => {
                            resetAll();
                        });

                        function startAnimation() {
                            if (!serverResults.length) {
                                statusEl.textContent = 'No data to animate';
                                runBtn.disabled = false;
                                pauseBtn.disabled = true;
                                return;
                            }

                            isRunning = true;
                            statusEl.textContent = 'Animating results...';
                            const speed = parseInt(speedInput.value) || 100;

                            animationInterval = setInterval(() => {
                                if (animationIndex >= serverResults.length) {
                                    pauseAnimation();
                                    statusEl.textContent = 'Complete';
                                    return;
                                }

                                const row = serverResults[animationIndex];
                                pushDataPoint(row);
                                updateSummaryFromRow(row);
                                animationIndex++;
                                statusEl.textContent = `Month ${animationIndex}/${serverResults.length}`;
                            }, speed);
                        }

                        function pauseAnimation() {
                            isRunning = false;
                            if (animationInterval) {
                                clearInterval(animationInterval);
                                animationInterval = null;
                            }
                            runBtn.disabled = false;
                            pauseBtn.disabled = true;
                            statusEl.textContent = 'Paused';
                        }

                        function resetChart() {
                            chart.data.labels = [];
                            chart.data.datasets[0].data = [];
                            chart.data.datasets[1].data = [];
                            chart.update();
                        }

                        function resetAll() {
                            pauseAnimation();
                            serverResults = [];
                            animationIndex = 0;
                            resetChart();
                            currentValueEl.textContent = '€' + Number(settings.initialInvestment).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                            totalContributedEl.textContent = '€0.00';
                            totalGainEl.textContent = '€0.00';
                            realValueEl.textContent = '€' + Number(settings.initialInvestment).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                            statusEl.textContent = 'Ready';
                            runBtn.disabled = false;
                            pauseBtn.disabled = true;
                        }

                        function pushDataPoint(row) {
                            chart.data.labels.push('Month ' + row.month);
                            chart.data.datasets[0].data.push(row.value);
                            chart.data.datasets[1].data.push(row.inflationAdjusted);

                            // keep last 100 points
                            if (chart.data.labels.length > 100) {
                                chart.data.labels.shift();
                                chart.data.datasets[0].data.shift();
                                chart.data.datasets[1].data.shift();
                            }

                            chart.update('none');
                        }

                        function updateSummaryFromRow(row) {
                            const totalContributed = Number(settings.initialInvestment) + Number(row.contributions || 0);
                            const totalGain = Number(row.value) - totalContributed;

                            currentValueEl.textContent = '€' + Number(row.value).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                            totalContributedEl.textContent = '€' + Number(totalContributed).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                            totalGainEl.textContent = '€' + Number(totalGain).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                            realValueEl.textContent = '€' + Number(row.inflationAdjusted).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                        }
                    </script>
                    @endpush
            if (!isRunning) {
                startSimulation();
            }
        });

        // Pause simulation
        pauseBtn.addEventListener('click', () => {
            if (isRunning) {
                pauseSimulation();
            }
        });

        // Reset simulation
        resetBtn.addEventListener('click', () => {
            resetSimulation();
        });

        function startSimulation() {
            isRunning = true;
            runBtn.disabled = true;
            pauseBtn.disabled = false;
            statusEl.textContent = 'Running...';

            const maxMonths = parseInt(monthsInput.value);
            const speed = parseInt(speedInput.value);

            intervalId = setInterval(() => {
                if (currentMonth >= maxMonths) {
                    pauseSimulation();
                    statusEl.textContent = 'Complete';
                    return;
                }

                calculateNextMonth();
                updateChart();
                updateSummary();
                currentMonth++;
                statusEl.textContent = `Month ${currentMonth}/${maxMonths}`;
            }, speed);
        }

        function pauseSimulation() {
            isRunning = false;
            runBtn.disabled = false;
            pauseBtn.disabled = true;
            if (intervalId) {
                clearInterval(intervalId);
                intervalId = null;
            }
            statusEl.textContent = 'Paused';
        }

        function resetSimulation() {
            pauseSimulation();
            currentMonth = 0;
            simulationData = [];
            chart.data.labels = [];
            chart.data.datasets[0].data = [];
            chart.data.datasets[1].data = [];
            chart.update();
            
            currentValueEl.textContent = '€' + settings.initialInvestment.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            totalContributedEl.textContent = '€0.00';
            totalGainEl.textContent = '€0.00';
            realValueEl.textContent = '€' + settings.initialInvestment.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            statusEl.textContent = 'Ready';
        }

        function calculateNextMonth() {
            const lastValue = simulationData.length > 0 
                ? simulationData[simulationData.length - 1].value 
                : settings.initialInvestment;

            let currentValue = lastValue + settings.monthlyContribution;

            // Apply volatility
            const randomness = (Math.random() * 2 - 1);
            const riskImpact = randomness * settings.riskAppetite * settings.marketInfluence;
            const monthlyReturnRate = settings.growthRate / 12;
            const adjustedReturn = monthlyReturnRate + riskImpact;
            
            const interestEarned = currentValue * adjustedReturn;
            currentValue = Math.max(0, currentValue + interestEarned);

            // Calculate inflation-adjusted value
            const monthlyInflationRate = settings.inflationRate / 12;
            const inflationAdjusted = currentValue / Math.pow(1 + monthlyInflationRate, currentMonth);

            simulationData.push({
                month: currentMonth,
                value: currentValue,
                inflationAdjusted: inflationAdjusted,
                contributions: (currentMonth + 1) * settings.monthlyContribution,
                interestEarned: interestEarned
            });
        }

        function updateChart() {
            const data = simulationData[simulationData.length - 1];
            chart.data.labels.push('Month ' + currentMonth);
            chart.data.datasets[0].data.push(data.value);
            chart.data.datasets[1].data.push(data.inflationAdjusted);
            
            // Keep only last 100 points for performance
            if (chart.data.labels.length > 100) {
                chart.data.labels.shift();
                chart.data.datasets[0].data.shift();
                chart.data.datasets[1].data.shift();
            }
            
            chart.update('none'); // No animation for better performance
        }

        function updateSummary() {
            const data = simulationData[simulationData.length - 1];
            const totalContributed = settings.initialInvestment + data.contributions;
            const totalGain = data.value - totalContributed;

            currentValueEl.textContent = '€' + data.value.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            totalContributedEl.textContent = '€' + totalContributed.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            totalGainEl.textContent = '€' + totalGain.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            realValueEl.textContent = '€' + data.inflationAdjusted.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    </script>
    @endpush
</x-app-layout>
