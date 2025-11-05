<?php

namespace App\Http\Controllers;

use App\Models\Simulation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SimulationController extends Controller
{
    use AuthorizesRequests;
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
            'name' => 'required|string|max:255',
            'initial_investment' => 'required|numeric|min:0',
            'monthly_contribution' => 'required|numeric|min:0',
            'growth_rate' => 'required|numeric|min:0|max:1',
            'risk_appetite' => 'required|numeric|min:0|max:1',
            'market_influence' => 'required|numeric|min:0|max:1',
            'inflation_rate' => 'required|numeric|min:0|max:1',
            'investors' => 'required|integer|min:1'
        ]);

        auth()->user()->simulations()->create([
            'name' => $validated['name'],
            'settings' => [
                'initialInvestment' => $validated['initial_investment'],
                'monthlyContribution' => $validated['monthly_contribution'],
                'growthRate' => $validated['growth_rate'],
                'riskAppetite' => $validated['risk_appetite'],
                'marketInfluence' => $validated['market_influence'],
                'inflationRate' => $validated['inflation_rate'],
                'investors' => $validated['investors']
            ]
        ]);

        return redirect()->route('simulations.index')
            ->with('success', 'Simulation created successfully!');
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
            'name' => 'required|string|max:255',
            'initial_investment' => 'required|numeric|min:0',
            'monthly_contribution' => 'required|numeric|min:0',
            'growth_rate' => 'required|numeric|min:0|max:1',
            'risk_appetite' => 'required|numeric|min:0|max:1',
            'market_influence' => 'required|numeric|min:0|max:1',
            'inflation_rate' => 'required|numeric|min:0|max:1',
            'investors' => 'required|integer|min:1'
        ]);

        $simulation->update([
            'name' => $validated['name'],
            'settings' => [
                'initialInvestment' => $validated['initial_investment'],
                'monthlyContribution' => $validated['monthly_contribution'],
                'growthRate' => $validated['growth_rate'],
                'riskAppetite' => $validated['risk_appetite'],
                'marketInfluence' => $validated['market_influence'],
                'inflationRate' => $validated['inflation_rate'],
                'investors' => $validated['investors']
            ]
        ]);

        return redirect()->route('simulations.show', $simulation)
            ->with('success', 'Simulation updated successfully!');
    }

    public function destroy(Simulation $simulation): RedirectResponse
    {
        $this->authorize('delete', $simulation);
        
        $simulation->delete();

        return redirect()->route('simulations.index')
            ->with('success', 'Simulation deleted successfully!');
    }

    public function run(Request $request, Simulation $simulation)
    {
        $this->authorize('update', $simulation);

        $months = $request->input('months', 120);
        $settings = $simulation->settings;
        
        $results = $this->calculateSimulation($settings, $months);
        
        $simulation->update(['data' => $results]);

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    private function calculateSimulation(array $settings, int $months): array
    {
        $currentValue = $settings['initialInvestment'];
        $monthlyContribution = $settings['monthlyContribution'];
        $annualReturn = $settings['growthRate'];
        $annualInflation = $settings['inflationRate'];
        $monthlyReturnRate = $annualReturn / 12;
        $monthlyInflationRate = $annualInflation / 12;
        
        $results = [];

        for ($month = 0; $month < $months; $month++) {
            // Add monthly contribution
            $currentValue += $monthlyContribution;

            // Apply volatility based on risk and market influence
            $randomness = (mt_rand(-1000, 1000) / 1000); // -1 to 1
            $riskImpact = $randomness * $settings['riskAppetite'] * $settings['marketInfluence'];
            $adjustedReturn = $monthlyReturnRate + $riskImpact;
            
            // Calculate interest
            $interestEarned = $currentValue * $adjustedReturn;
            $currentValue = max(0, $currentValue + $interestEarned);

            // Calculate inflation-adjusted value
            $inflationAdjusted = $currentValue / pow(1 + $monthlyInflationRate, $month);

            // Store result
            $results[] = [
                'month' => $month,
                'value' => round($currentValue, 2),
                'inflationAdjusted' => round($inflationAdjusted, 2),
                'contributions' => ($month + 1) * $monthlyContribution,
                'interestEarned' => round($interestEarned, 2)
            ];
        }

        return $results;
    }
}