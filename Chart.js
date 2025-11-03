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
                    <h3 class="text-xl font-bold text-white mb-4">Simulation Controls</h3>
                    
                    <div class="flex gap-4 mb-4">
                        <button id="runBtn" 
                                class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                            ▶ Run Simulation
                        </button>
                        <button id="pauseBtn" disabled
                                class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-6 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                            ⏸ Pause
                        </button>
                        <button id="resetBtn"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded">
                            🔄 Reset
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="months" class="block text-white font-semibold mb-2">
                                Duration (months)
                            </label>
                            <input type="number" id="months" min="1" max="600" value="120"
                                   class="w-full bg-white/20 text-white rounded px-4 py-2 border border-white/30">
                        </div>
                        <div>
                            <label for="speed" class="block text-white font-semibold mb-2">
                                Speed (ms)
                            </label>
                            <input type="number" id="speed" min="50" max="2000" step="50" value="100"
                                   class="w-full bg-white/20 text-white rounded px-4 py-2 border border-white/30">
                        </div>
                        <div>
                            <label class="block text-white font-semibold mb-2">Status</label>
                            <p id="status" class="text-white py-2">Ready</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                    <h4 class="text-gray-300 text-sm mb-1">Current Value</h4>
                    <p id="currentValue" class="text-white text-2xl font-bold">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                    <h4 class="text-gray-300 text-sm mb-1">Total Contributed</h4>
                    <p id="totalContributed" class="text-white text-2xl font-bold">€0.00</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                    <h4 class="text-gray-300 text-sm mb-1">Total Gain</h4>
                    <p id="totalGain" class="text-white text-2xl font-bold">€0.00</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                    <h4 class="text-gray-300 text-sm mb-1">Real Value</h4>
                    <p id="realValue" class="text-white text-2xl font-bold">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</p>
                </div>
            </div>

            <!-- Chart -->
            <div class="bg-white/10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <canvas id="simulationChart"></canvas>
                </div>
            </div>

            <!-- Settings Display -->
            <div class="bg-white/10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-white mb-4">Simulation Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-white">
                        <div>
                            <span class="text-gray-300">Initial Investment:</span>
                            <span class="font-semibold ml-2">€{{ number_format($simulation->settings['initialInvestment'], 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-300">Monthly Contribution:</span>
                            <span class="font-semibold ml-2">€{{ number_format($simulation->settings['monthlyContribution'], 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-300">Growth Rate:</span>
                            <span class="font-semibold ml-2">{{ ($simulation->settings['growthRate'] * 100) }}%</span>
                        </div>
                        <div>
                            <span class="text-gray-300">Inflation Rate:</span>
                            <span class="font-semibold ml-2">{{ ($simulation->settings['inflationRate'] * 100) }}%</span>
                        </div>
                        <div>
                            <span class="text-gray-300">Risk Appetite:</span>
                            <span class="font-semibold ml-2">{{ $simulation->settings['riskAppetite'] }}</span>
                        </div>
                        <div>
                            <span class="text-gray-300">Market Influence:</span>
                            <span class="font-semibold ml-2">{{ $simulation->settings['marketInfluence'] }}</span>
                        </div>
                        <div>
                            <span class="text-gray-300">Number of Investors:</span>
                            <span class="font-semibold ml-2">{{ $simulation->settings['investors'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Simulation settings from Laravel
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
                    legend: {
                        labels: {
                            color: 'white'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            color: 'white',
                            callback: function(value) {
                                return '€' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    },
                    x: {
                        ticks: {
                            color: 'white'
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                }
            }
        });

        // Simulation state
        let isRunning = false;
        let currentMonth = 0;
        let simulationData = [];
        let intervalId = null;

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

        // Run simulation
        runBtn.addEventListener('click', () => {
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