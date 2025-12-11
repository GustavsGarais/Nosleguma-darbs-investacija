@extends('layouts.dashboard')

@section('title', __('Dashboard'))

@section('dashboard_content')
<div class="dashboard">
    <section aria-label="Welcome" class="auth-card" style="margin-top:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
            <div>
                <h1 style="margin:0 0 8px;">{{ __('Welcome :name!', ['name' => auth()->user()->name]) }}</h1>
                <p style="margin:0; color: var(--c-on-surface);">{{ __("You're signed in. Your data is loaded from the database.") }}</p>
            </div>
            @if(auth()->user()->tutorial_completed)
                <button id="start-tutorial" class="btn btn-outline" type="button">📚 {{ __('Start Tutorial') }}</button>
            @endif
        </div>
    </section>

    @if(isset($simulations) && $simulations->count())
    <section aria-label="Your simulations" class="auth-card" style="margin-top:24px;">
        <h2 style="margin:0 0 12px;">{{ __('Your recent simulations') }}</h2>
        <ul style="margin:0; padding:0; list-style:none; display:grid; gap:12px;">
            @foreach($simulations as $simulation)
                @php
                    $snapshot = $simulation->data['snapshot'] ?? null;
                    $lastValue = $snapshot['value'] ?? ($simulation->settings['initialInvestment'] ?? 0);
                    $capturedAt = $snapshot['captured_at'] ?? null;
                    $updatedText = $capturedAt
                        ? __('Updated :time', ['time' => \Illuminate\Support\Carbon::parse($capturedAt)->diffForHumans()])
                        : __('Not saved yet');
                @endphp
                <li style="border:1px solid var(--c-border); border-radius:12px; padding:12px 16px; display:flex; flex-wrap:wrap; justify-content:space-between; gap:12px; align-items:center;">
                    <div>
                        <a href="{{ route('simulations.show', $simulation) }}" style="font-weight:600;">{{ $simulation->name ?? 'Simulation #'.$simulation->id }}</a>
                        <p style="margin:4px 0 0; color:var(--c-on-surface-2); font-size:13px;">{{ $updatedText }}</p>
                    </div>
                    <div style="text-align:right;">
                        <span class="currency-value" data-currency-value="{{ $lastValue }}">{{ '€'.number_format($lastValue, 2) }}</span>
                        <p style="margin:4px 0 0; color:var(--c-on-surface-2); font-size:13px;">{{ __('Latest value') }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>
    @else
    <section aria-label="Get started" class="auth-card" style="margin-top:24px;">
        <p style="margin:0;">{{ __('No simulations yet.') }} <a href="{{ route('simulations.create') }}">{{ __('Create your first simulation') }}</a>.</p>
    </section>
    @endif
</div>
@endsection

@include('components.currency-script')
@include('components.tutorial', ['currentPage' => 'dashboard'])


