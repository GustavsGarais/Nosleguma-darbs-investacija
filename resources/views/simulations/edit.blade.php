@extends('layouts.dashboard')

@section('title', 'Edit Simulation')

@section('dashboard_content')
<section class="auth-card" aria-label="Edit Simulation">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
        <h1 style="margin:0;">Edit Simulation</h1>
        <a class="btn btn-outline" href="{{ route('simulations.show', $simulation) }}">Back</a>
    </div>

    @if ($errors->any())
        <div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li style="color: var(--c-on-surface);">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('simulations.update', $simulation) }}" style="display:grid; gap:12px;">
        @csrf
        @method('PUT')

        <label style="display:grid; gap:6px;">
            <span>Name</span>
            <input type="text" name="name" value="{{ old('name', $simulation->name) }}" required class="footer-email-input" />
        </label>

        @php($s = $simulation->settings)
        <div style="display:grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap:12px;">
            <label style="display:grid; gap:6px;">
                <span>Initial Investment</span>
                <input type="number" step="0.01" name="initial_investment" value="{{ old('initial_investment', $s['initialInvestment'] ?? 1000) }}" required class="footer-email-input" />
            </label>
            <label style="display:grid; gap:6px;">
                <span>Monthly Contribution</span>
                <input type="number" step="0.01" name="monthly_contribution" value="{{ old('monthly_contribution', $s['monthlyContribution'] ?? 100) }}" required class="footer-email-input" />
            </label>
            <label style="display:grid; gap:6px;">
                <span>Growth Rate (0-1 annual)</span>
                <input type="number" step="0.001" min="0" max="1" name="growth_rate" value="{{ old('growth_rate', $s['growthRate'] ?? 0.07) }}" required class="footer-email-input" />
            </label>
            <label style="display:grid; gap:6px;">
                <span>Risk Appetite (0-1)</span>
                <input type="number" step="0.01" min="0" max="1" name="risk_appetite" value="{{ old('risk_appetite', $s['riskAppetite'] ?? 0.5) }}" required class="footer-email-input" />
            </label>
            <label style="display:grid; gap:6px;">
                <span>Market Influence (0-1)</span>
                <input type="number" step="0.01" min="0" max="1" name="market_influence" value="{{ old('market_influence', $s['marketInfluence'] ?? 0.5) }}" required class="footer-email-input" />
            </label>
            <label style="display:grid; gap:6px;">
                <span>Inflation Rate (0-1 annual)</span>
                <input type="number" step="0.001" min="0" max="1" name="inflation_rate" value="{{ old('inflation_rate', $s['inflationRate'] ?? 0.02) }}" required class="footer-email-input" />
            </label>
            <label style="display:grid; gap:6px;">
                <span>Investors (count)</span>
                <input type="number" step="1" min="1" name="investors" value="{{ old('investors', $s['investors'] ?? 1) }}" required class="footer-email-input" />
            </label>
        </div>

        <div style="display:flex; gap:12px;">
            <a href="{{ route('simulations.show', $simulation) }}" class="btn btn-outline">Cancel</a>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</section>
@endsection


