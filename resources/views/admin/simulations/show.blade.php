@extends('layouts.dashboard')

@section('title', __('Simulation Details'))

@section('dashboard_content')
<section class="auth-card" aria-label="{{ __('Simulation Details') }}">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <h1 style="margin:0;">{{ __('Simulation Details') }}</h1>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('admin.simulations.index') }}" class="btn btn-outline">{{ __('Back to Simulations') }}</a>
            <a href="{{ route('admin.users.show', $simulation->user) }}" class="btn btn-secondary">{{ __('View User') }}</a>
        </div>
    </div>

    <!-- Simulation Information -->
    <div style="margin-top:24px; display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:16px;">
        <div class="auth-card">
            <h2 style="margin:0 0 16px; font-size:18px;">{{ __('Simulation Information') }}</h2>
            <div style="display:grid; gap:12px;">
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">Name</p>
                    <p style="margin:0; font-weight:600;">{{ $simulation->name }}</p>
                </div>
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Owner') }}</p>
                    <p style="margin:0;">
                        <a href="{{ route('admin.users.show', $simulation->user) }}" style="font-weight:600; color:var(--c-primary);">{{ $simulation->user->name }}</a>
                    </p>
                    <p style="margin:4px 0 0; font-size:12px; color:var(--c-on-surface-2);">{{ $simulation->user->email }}</p>
                </div>
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">Created</p>
                    <p style="margin:0; font-weight:600;">{{ $simulation->created_at->format('F d, Y') }}</p>
                    <p style="margin:4px 0 0; font-size:12px; color:var(--c-on-surface-2);">{{ $simulation->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Last Updated') }}</p>
                    <p style="margin:0; font-weight:600;">{{ $simulation->updated_at->format('F d, Y') }}</p>
                    <p style="margin:4px 0 0; font-size:12px; color:var(--c-on-surface-2);">{{ $simulation->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        <div class="auth-card">
            <h2 style="margin:0 0 16px; font-size:18px;">{{ __('Current Status') }}</h2>
            <div style="display:grid; gap:12px;">
                @php
                    $snapshot = $simulation->data['snapshot'] ?? null;
                    $lastValue = $snapshot['value'] ?? ($simulation->settings['initialInvestment'] ?? 0);
                    $initialInvestment = $simulation->settings['initialInvestment'] ?? 0;
                    $gain = $lastValue - $initialInvestment;
                    $gainPercent = $initialInvestment > 0 ? (($gain / $initialInvestment) * 100) : 0;
                @endphp
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Initial Investment') }}</p>
                    <p style="margin:0; font-size:20px; font-weight:700;">
                        <span class="currency-value" data-currency-value="{{ $initialInvestment }}">{{ '€'.number_format($initialInvestment, 2) }}</span>
                    </p>
                </div>
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Latest Value') }}</p>
                    <p style="margin:0; font-size:20px; font-weight:700;">
                        <span class="currency-value" data-currency-value="{{ $lastValue }}">{{ '€'.number_format($lastValue, 2) }}</span>
                    </p>
                </div>
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Total Gain/Loss') }}</p>
                    <p style="margin:0; font-size:18px; font-weight:700; color:{{ $gain >= 0 ? '#10b981' : '#ef4444' }};">
                        {{ $gain >= 0 ? '+' : '' }}<span class="currency-value" data-currency-value="{{ $gain }}">{{ '€'.number_format($gain, 2) }}</span>
                        ({{ $gain >= 0 ? '+' : '' }}{{ number_format($gainPercent, 2) }}%)
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Simulation Settings -->
    <div class="auth-card" style="margin-top:24px;">
        <h2 style="margin:0 0 16px; font-size:18px;">{{ __('Simulation Settings') }}</h2>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
            @php
                $settings = $simulation->settings ?? [];
            @endphp
            <div>
                <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Initial Investment') }}</p>
                <p style="margin:0; font-weight:600;">
                    <span class="currency-value" data-currency-value="{{ $settings['initialInvestment'] ?? 0 }}">{{ '€'.number_format($settings['initialInvestment'] ?? 0, 2) }}</span>
                </p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Monthly Contribution') }}</p>
                <p style="margin:0; font-weight:600;">
                    <span class="currency-value" data-currency-value="{{ $settings['monthlyContribution'] ?? 0 }}">{{ '€'.number_format($settings['monthlyContribution'] ?? 0, 2) }}</span>
                </p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Growth Rate') }}</p>
                <p style="margin:0; font-weight:600;">{{ number_format(($settings['growthRate'] ?? 0) * 100, 2) }}%</p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Risk Appetite') }}</p>
                <p style="margin:0; font-weight:600;">{{ number_format(($settings['riskAppetite'] ?? 0) * 100, 2) }}%</p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Market Influence') }}</p>
                <p style="margin:0; font-weight:600;">{{ number_format(($settings['marketInfluence'] ?? 0) * 100, 2) }}%</p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Inflation Rate') }}</p>
                <p style="margin:0; font-weight:600;">{{ number_format(($settings['inflationRate'] ?? 0) * 100, 2) }}%</p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Investors') }}</p>
                <p style="margin:0; font-weight:600;">{{ $settings['investors'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Snapshot Data -->
    @if($snapshot)
    <div class="auth-card" style="margin-top:24px;">
        <h2 style="margin:0 0 16px; font-size:18px;">{{ __('Latest Snapshot') }}</h2>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px;">
            <div>
                <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Month') }}</p>
                <p style="margin:0; font-weight:600;">{{ $snapshot['month'] ?? __('N/A') }}</p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Value') }}</p>
                <p style="margin:0; font-weight:600;">
                    <span class="currency-value" data-currency-value="{{ $snapshot['value'] ?? 0 }}">{{ '€'.number_format($snapshot['value'] ?? 0, 2) }}</span>
                </p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Real Value') }}</p>
                <p style="margin:0; font-weight:600;">
                    <span class="currency-value" data-currency-value="{{ $snapshot['real_value'] ?? 0 }}">{{ '€'.number_format($snapshot['real_value'] ?? 0, 2) }}</span>
                </p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Contributions') }}</p>
                <p style="margin:0; font-weight:600;">
                    <span class="currency-value" data-currency-value="{{ $snapshot['contributions'] ?? 0 }}">{{ '€'.number_format($snapshot['contributions'] ?? 0, 2) }}</span>
                </p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Total Gain') }}</p>
                <p style="margin:0; font-weight:600;">
                    <span class="currency-value" data-currency-value="{{ $snapshot['total_gain'] ?? 0 }}">{{ '€'.number_format($snapshot['total_gain'] ?? 0, 2) }}</span>
                </p>
            </div>
            <div>
                <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">{{ __('Captured At') }}</p>
                <p style="margin:0; font-weight:600;">{{ isset($snapshot['captured_at']) ? \Illuminate\Support\Carbon::parse($snapshot['captured_at'])->format('M d, Y H:i') : __('N/A') }}</p>
            </div>
        </div>
    </div>
    @endif
</section>
@endsection

@include('components.currency-script')

