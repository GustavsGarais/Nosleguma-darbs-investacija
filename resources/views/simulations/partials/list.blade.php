<section class="auth-card" aria-label="Simulations" style="margin-top:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
        <h2 style="margin:0;">{{ __('Your Simulations') }}</h2>
        <a href="{{ route('simulations.create') }}" class="btn btn-primary btn-lg">{{ __('New Simulation') }}</a>
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
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">{{ __('Name') }}</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">{{ __('Latest Value') }}</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">{{ __('Last Updated') }}</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">{{ __('Created') }}</th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($simulations as $sim)
                        @php
                            $snapshot = $sim->data['snapshot'] ?? null;
                            $lastValue = $snapshot['value'] ?? ($sim->settings['initialInvestment'] ?? 0);
                            $capturedAt = $snapshot['captured_at'] ?? null;
                            $updatedText = $capturedAt
                                ? \Illuminate\Support\Carbon::parse($capturedAt)->diffForHumans()
                                : 'Not saved yet';
                        @endphp
                        <tr>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border);">
                                <a href="{{ route('simulations.show', $sim) }}" class="sim-name-link">{{ $sim->name }}</a>
                            </td>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border);">
                                <span class="currency-value" data-currency-value="{{ $lastValue }}">{{ '€'.number_format($lastValue, 2) }}</span>
                            </td>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border);">{{ $updatedText }}</td>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border);">{{ $sim->created_at->diffForHumans() }}</td>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border); display:flex; gap:8px;">
                                <a class="btn btn-primary btn-sm" href="{{ route('simulations.edit', $sim) }}">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('simulations.destroy', $sim) }}" onsubmit="return confirm('{{ __('Delete this simulation?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline btn-sm">{{ __('Delete') }}</button>
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
        <p style="margin-top:16px;">{{ __('No simulations yet.') }} <a href="{{ route('simulations.create') }}" class="sim-name-link">{{ __('Create your first simulation') }}</a>.</p>
    @endif
</section>
