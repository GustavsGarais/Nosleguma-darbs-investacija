@extends('layouts.dashboard')

@section('title', 'Simulations')

@section('dashboard_content')
<section class="auth-card" aria-label="Simulations">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
        <h1 style="margin:0;">Your Simulations</h1>
        <a href="{{ route('simulations.create') }}" class="btn btn-primary">New Simulation</a>
    </div>

    @if(session('success'))
        <div role="status" style="margin-top:12px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
            {{ session('success') }}
        </div>
    @endif

    @if($simulations->count())
        <div style="overflow:auto; margin-top:16px;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">Name</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">Latest Value</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">Last Updated</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">Created</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($simulations as $simulation)
                        @php
                            $snapshot = $simulation->data['snapshot'] ?? null;
                            $lastValue = $snapshot['value'] ?? ($simulation->settings['initialInvestment'] ?? 0);
                            $capturedAt = $snapshot['captured_at'] ?? null;
                            $updatedText = $capturedAt
                                ? \Illuminate\Support\Carbon::parse($capturedAt)->diffForHumans()
                                : 'Not saved yet';
                        @endphp
                        <tr>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border);">
                                <a href="{{ route('simulations.show', $simulation) }}">{{ $simulation->name }}</a>
                            </td>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border);">
                                <span class="currency-value" data-currency-value="{{ $lastValue }}">{{ '€'.number_format($lastValue, 2) }}</span>
                            </td>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border);">{{ $updatedText }}</td>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border);">{{ $simulation->created_at->diffForHumans() }}</td>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border); display:flex; gap:8px;">
                                <a class="btn btn-secondary btn-sm" href="{{ route('simulations.edit', $simulation) }}">Edit</a>
                                <form method="POST" action="{{ route('simulations.destroy', $simulation) }}" onsubmit="return confirm('Delete this simulation?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top:12px;">
            {{ $simulations->links() }}
        </div>
    @else
        <p style="margin-top:16px;">No simulations yet. <a href="{{ route('simulations.create') }}">Create your first simulation</a>.</p>
    @endif
</section>
@endsection

@include('components.currency-script')


