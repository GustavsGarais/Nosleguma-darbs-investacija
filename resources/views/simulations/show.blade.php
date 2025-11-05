@extends('layouts.dashboard')

@section('title', $simulation->name)

@section('dashboard_content')
<section class="auth-card" aria-label="Simulation details">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
        <h1 style="margin:0;">{{ $simulation->name }}</h1>
        <div style="display:flex; gap:8px;">
            <a class="btn btn-secondary" href="{{ route('simulations.edit', $simulation) }}">Edit</a>
            <form method="POST" action="{{ route('simulations.destroy', $simulation) }}" onsubmit="return confirm('Delete this simulation?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline">Delete</button>
            </form>
            <a class="btn btn-outline" href="{{ route('simulations.index') }}">Back</a>
        </div>
    </div>

    <div style="margin-top:16px; display:grid; gap:16px;">
        <section class="auth-card" aria-label="Run controls">
            <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
                <label style="display:flex; align-items:center; gap:6px;">
                    <span>Months</span>
                    <input id="months-input" type="number" min="12" max="600" step="12" value="120" class="footer-email-input" style="width:100px;" />
                </label>
                <button id="btn-run" class="btn btn-primary">Run</button>
                <button id="btn-pause" class="btn btn-secondary" disabled>Pause</button>
                <button id="btn-toggle-graph" class="btn btn-outline" aria-pressed="true">Hide Graph</button>
            </div>
        </section>

        <section class="auth-card" aria-label="Graph" id="graph-section" style="padding:16px;">
            <canvas id="sim-chart" height="220" aria-label="Simulation chart"></canvas>
        </section>

        <section>
            <h2 style="margin:0 0 6px;">Results (JSON)</h2>
            <pre id="results-json" style="margin:0; white-space:pre-wrap;">{{ $simulation->data ? json_encode($simulation->data, JSON_PRETTY_PRINT) : '' }}</pre>
        </section>

        <section>
            <h2 style="margin:0 0 6px;">Settings</h2>
            <pre style="margin:0; white-space:pre-wrap;">{{ json_encode($simulation->settings, JSON_PRETTY_PRINT) }}</pre>
        </section>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
(function(){
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const chartCtx = document.getElementById('sim-chart').getContext('2d');
    const resultsPre = document.getElementById('results-json');
    const btnRun = document.getElementById('btn-run');
    const btnPause = document.getElementById('btn-pause');
    const btnToggleGraph = document.getElementById('btn-toggle-graph');
    const graphSection = document.getElementById('graph-section');
    const monthsInput = document.getElementById('months-input');

    let animationTimer = null;
    let lastResults = @json($simulation->data ?? []);

    const baseDataset = {
        label: 'Portfolio Value',
        data: [],
        fill: false,
        borderColor: getComputedStyle(document.documentElement).getPropertyValue('--c-primary') || '#3b82f6',
        tension: 0.25,
    };

    const chart = new Chart(chartCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [ JSON.parse(JSON.stringify(baseDataset)) ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { title: { display: true, text: 'Month' } },
                y: { title: { display: true, text: 'Value' } }
            },
            plugins: { legend: { display: true } }
        }
    });

    function setButtonsRunning(running) {
        btnRun.disabled = running;
        btnPause.disabled = !running;
    }

    function renderStatic(results) {
        chart.data.labels = results.map(r => r.month);
        chart.data.datasets[0].data = results.map(r => r.value);
        chart.update();
    }

    function animateResults(results) {
        clearInterval(animationTimer);
        chart.data.labels = [];
        chart.data.datasets[0].data = [];
        chart.update();

        let i = 0;
        setButtonsRunning(true);
        animationTimer = setInterval(() => {
            if (i >= results.length) {
                clearInterval(animationTimer);
                setButtonsRunning(false);
                return;
            }
            chart.data.labels.push(results[i].month);
            chart.data.datasets[0].data.push(results[i].value);
            chart.update();
            i++;
        }, 30);
    }

    async function runSimulation() {
        try {
            const months = parseInt(monthsInput.value || '120', 10);
            const res = await fetch("{{ route('simulations.run', $simulation) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ months })
            });
            const payload = await res.json();
            if (!payload.success) throw new Error('Run failed');
            lastResults = payload.data || [];
            resultsPre.textContent = JSON.stringify(lastResults, null, 2);
            animateResults(lastResults);
        } catch (e) {
            console.error(e);
            alert('Failed to run simulation.');
            setButtonsRunning(false);
        }
    }

    btnRun.addEventListener('click', runSimulation);
    btnPause.addEventListener('click', function() {
        clearInterval(animationTimer);
        setButtonsRunning(false);
    });
    btnToggleGraph.addEventListener('click', function() {
        const hidden = graphSection.style.display === 'none';
        graphSection.style.display = hidden ? '' : 'none';
        this.textContent = hidden ? 'Hide Graph' : 'Show Graph';
        this.setAttribute('aria-pressed', hidden ? 'true' : 'false');
        if (hidden && lastResults && lastResults.length) {
            renderStatic(lastResults);
        }
    });

    // Initialize chart from existing data if present
    if (lastResults && lastResults.length) {
        renderStatic(lastResults);
    }
})();
</script>
@endpush