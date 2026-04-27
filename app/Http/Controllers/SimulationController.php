<?php

namespace App\Http\Controllers;

use App\Models\Simulation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SimulationController extends Controller
{
    use AuthorizesRequests;

    private function percentToDecimal(float|int|string $value, float $fallback = 0.0): float
    {
        if (is_string($value)) {
            $value = preg_replace('/[^0-9.\-]/', '', $value);
        }
        $num = is_numeric($value) ? (float) $value : $fallback;
        $num = max(0.0, min(100.0, $num));

        return $num / 100.0;
    }

    public function index(): View
    {
        $simulations = auth()->user()->simulations()->latest()->paginate(10);

        return view('simulations.index', compact('simulations'));
    }

    public function create(): View
    {
        return view('simulations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:30',
            'initial_investment' => 'required|numeric|min:0',
            'monthly_contribution' => 'required|numeric|min:0',
            // UI uses percent (0-100). Stored as decimal (0-1).
            'growth_rate' => 'required|numeric|min:0|max:100',
            'risk_appetite' => 'required|numeric|min:0|max:100',
            'market_influence' => 'required|numeric|min:0|max:100',
            'inflation_rate' => 'required|numeric|min:0|max:100',
            'investors' => 'required|integer|min:1',
            'simulation_mode' => 'nullable|string|in:classic,playground',
        ]);

        $mode = ($validated['simulation_mode'] ?? 'classic') === 'playground' ? 'playground' : 'classic';

        auth()->user()->simulations()->create([
            'name' => $validated['name'],
            'settings' => [
                'initialInvestment' => $validated['initial_investment'],
                'monthlyContribution' => $validated['monthly_contribution'],
                'growthRate' => $this->percentToDecimal($validated['growth_rate'], 7.0),
                'riskAppetite' => $this->percentToDecimal($validated['risk_appetite'], 50.0),
                'marketInfluence' => $this->percentToDecimal($validated['market_influence'], 50.0),
                'inflationRate' => $this->percentToDecimal($validated['inflation_rate'], 2.0),
                'investors' => $validated['investors'],
                'defaultRunnerMode' => $mode,
            ],
        ]);

        return redirect()->route('simulations.index')
            ->with('success', __('Simulation created successfully!'));
    }

    public function show(Simulation $simulation): View
    {
        $this->authorize('view', $simulation);

        return view('simulations.show', compact('simulation'));
    }

    public function edit(Simulation $simulation): View
    {
        $this->authorize('update', $simulation);

        return view('simulations.edit', compact('simulation'));
    }

    public function update(Request $request, Simulation $simulation): RedirectResponse
    {
        $this->authorize('update', $simulation);

        $validated = $request->validate([
            'name' => 'required|string|max:30',
            'initial_investment' => 'required|numeric|min:0',
            'monthly_contribution' => 'required|numeric|min:0',
            // UI uses percent (0-100). Stored as decimal (0-1).
            'growth_rate' => 'required|numeric|min:0|max:100',
            'risk_appetite' => 'required|numeric|min:0|max:100',
            'market_influence' => 'required|numeric|min:0|max:100',
            'inflation_rate' => 'required|numeric|min:0|max:100',
            'investors' => 'required|integer|min:1',
            'simulation_mode' => 'nullable|string|in:classic,playground',
        ]);

        $mode = ($validated['simulation_mode'] ?? 'classic') === 'playground' ? 'playground' : 'classic';

        $simulation->update([
            'name' => $validated['name'],
            'settings' => [
                'initialInvestment' => $validated['initial_investment'],
                'monthlyContribution' => $validated['monthly_contribution'],
                'growthRate' => $this->percentToDecimal($validated['growth_rate'], 7.0),
                'riskAppetite' => $this->percentToDecimal($validated['risk_appetite'], 50.0),
                'marketInfluence' => $this->percentToDecimal($validated['market_influence'], 50.0),
                'inflationRate' => $this->percentToDecimal($validated['inflation_rate'], 2.0),
                'investors' => $validated['investors'],
                'defaultRunnerMode' => $mode,
            ],
        ]);

        return redirect()->route('simulations.show', $simulation)
            ->with('success', __('Simulation updated successfully!'));
    }

    public function destroy(Simulation $simulation): RedirectResponse
    {
        $this->authorize('delete', $simulation);

        $simulation->delete();

        return redirect()->route('simulations.index')
            ->with('success', __('Simulation deleted successfully!'));
    }

    public function snapshot(Request $request, Simulation $simulation)
    {
        $this->authorize('update', $simulation);

        $validated = $request->validate([
            'month' => 'required|integer|min:0',
            'value' => 'required|numeric|min:0',
            'real_value' => 'required|numeric|min:0',
            'contributions' => 'required|numeric|min:0',
            'total_gain' => 'required|numeric',
            'currency' => 'nullable|string|in:EUR,USD,GBP,JPY',
            // Client sends one entry per step. In the daily timestep build, a 20y max horizon is 7300 days.
            'history' => 'nullable|array|max:7301',
            'history.*.month' => 'required|integer|min:0|max:7300',
            'history.*.value' => 'required|numeric',
            'history.*.inflationAdjusted' => 'required|numeric',
            'history.*.contributions' => 'required|numeric',
        ]);

        $data = $simulation->data ?? [];

        if (! empty($validated['history'])) {
            $data['history'] = array_map(static function (array $row) {
                return [
                    'month' => (int) $row['month'],
                    'value' => round((float) $row['value'], 2),
                    'inflationAdjusted' => round((float) $row['inflationAdjusted'], 2),
                    'contributions' => round((float) $row['contributions'], 2),
                ];
            }, $validated['history']);
        }

        $data['snapshot'] = [
            'month' => $validated['month'],
            'value' => round($validated['value'], 2),
            'real_value' => round($validated['real_value'], 2),
            'contributions' => round($validated['contributions'], 2),
            'total_gain' => round($validated['total_gain'], 2),
            'currency' => $validated['currency'] ?? 'EUR',
            'captured_at' => now()->toIso8601String(),
        ];

        $simulation->update(['data' => $data]);

        return response()->json([
            'success' => true,
            'snapshot' => $data['snapshot'],
        ]);
    }

    /**
     * Persist interactive runner UI state (mode, control values, simulation path) per simulation.
     */
    public function runnerState(Request $request, Simulation $simulation)
    {
        $this->authorize('update', $simulation);

        $validated = $request->validate([
            'v' => 'required|integer|in:1',
            'settingsFingerprint' => 'required|string|max:512',
            'mode' => 'required|string|in:classic,playground',
            'months' => 'required|integer|min:1|max:7300',
            'speed' => 'required|numeric|min:0.1|max:10',
            'activePresetKey' => 'required|string|max:32',
            'secondaryScenario' => 'required|string|in:none,compare,sor',
            'compareExtra' => 'nullable|numeric|min:0|max:1000000',
            'playgroundCustomAmount' => 'nullable|numeric|min:0|max:1000000',
            'simulationData' => 'required|array|min:1|max:7302',
            'simulationData.*' => 'array',
            'simulationDataCompare' => 'nullable|array|max:7302',
            'simulationDataCompare.*' => 'array',
            'simulationDataSor' => 'nullable|array|max:7302',
            'simulationDataSor.*' => 'array',
            'sharedSmoothedReturns' => 'nullable|array|max:7300',
            'sharedSmoothedReturns.*' => 'numeric',
            'sharedSmoothedReturnsReversed' => 'nullable|array|max:7300',
            'sharedSmoothedReturnsReversed.*' => 'numeric',
            'crashMonths' => 'nullable|array|max:200',
            'crashMonths.*' => 'integer|min:0|max:7300',
            'peakValue' => 'required|numeric',
            'maxDrawdown' => 'required|numeric',
            'lastMonthlyReturn' => 'required|numeric',
            'crowdSentiment' => 'required|numeric',
            'currentMonth' => 'required|integer|min:0|max:7300',
        ]);

        $data = $simulation->data ?? [];
        $data['runner'] = $validated;
        $simulation->update(['data' => $data]);

        return response()->json(['success' => true]);
    }
}
