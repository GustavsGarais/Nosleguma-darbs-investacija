<section class="auth-card" aria-label="{{ __('Simulations') }}">
    <div class="sim-dash-list__toolbar sim-dash-list__head">
        <h2 class="sim-dash-list__title">{{ __('Your Simulations') }}</h2>
        <a href="{{ route('simulations.create') }}" class="btn btn-primary btn-lg">{{ __('New Simulation') }}</a>
    </div>

    @if(session('success'))
        <div role="status" class="sim-dash-flash">
            {{ session('success') }}
        </div>
    @endif

    @if($simulations->count())
        <div class="sim-cardGrid" style="margin-top: 18px;">
            @foreach($simulations as $sim)
                @php
                    $snapshot = $sim->data['snapshot'] ?? null;
                    $initial = (float) ($sim->settings['initialInvestment'] ?? 0);
                    $lastValue = (float) ($snapshot['value'] ?? $initial);
                    $capturedAt = $snapshot['captured_at'] ?? null;
                    $hasSavedRun = is_array($snapshot)
                        && filled($capturedAt)
                        && array_key_exists('month', $snapshot);
                    $updatedText = $hasSavedRun
                        ? \Illuminate\Support\Carbon::parse($capturedAt)->diffForHumans()
                        : __('Not saved yet');
                    $delta = $lastValue - $initial;
                    $pctChange = $initial > 0 ? (($lastValue - $initial) / $initial) * 100 : 0.0;
                    $pctRounded = round($pctChange, 1);
                    $gainUp = $pctChange >= 0;
                    $gainClass = $gainUp ? 'is-up' : 'is-down';
                    $gainSign = $gainUp ? '+' : '';
                    $horizonMonths = $hasSavedRun
                        ? max(1, (int) ($snapshot['horizon_months'] ?? 120))
                        : 120;
                    $savedDay = $hasSavedRun ? (int) $snapshot['month'] : 0;
                    $timeBarPct = ($hasSavedRun && $horizonMonths > 0)
                        ? (int) max(0, min(100, (int) round(($savedDay / $horizonMonths) * 100)))
                        : 0;
                @endphp
                <article class="sim-card">
                    <div class="sim-card__topbar" aria-hidden="true"></div>
                    <div class="sim-card__body">
                        <a href="{{ route('simulations.show', $sim) }}" class="sim-card__title">{{ $sim->name }}</a>
                        <div class="sim-card__valueRow">
                            {{-- currency-value: NosCurrencyFormatter adds the symbol; no separate € prefix --}}
                            <span class="sim-card__value currency-value" data-currency-value="{{ $lastValue }}">{{ number_format($lastValue, 2) }}</span>
                        </div>
                        <div class="sim-card__meta">
                            <span>{{ __('Run progress') }}</span>
                            <span class="sim-card__metaValue">
                                @if($hasSavedRun)
                                    {{ __('Day :current / :total', ['current' => $savedDay, 'total' => $horizonMonths]) }}
                                @else
                                    {{ __('Not saved yet') }}
                                @endif
                            </span>
                        </div>
                        <div class="sim-card__progress" role="presentation" aria-hidden="true">
                            <div class="sim-card__progressFill" style="width: {{ $timeBarPct }}%;"></div>
                        </div>
                        @if($initial > 0)
                            <div class="sim-card__gain {{ $gainClass }}">
                                <span class="sim-card__gainSign" aria-hidden="true">{{ $gainSign }}</span>{{ number_format(abs($pctRounded), 1) }}%
                                <span class="sim-card__gainVs"> {{ __('vs initial') }}</span>
                                <span class="sr-only"> — {{ __('Gain / loss') }} {{ $gainSign }}{{ number_format($delta, 2) }} €</span>
                            </div>
                            <div class="sim-card__meta">
                                <span>{{ __('Gain / loss') }}</span>
                                <span class="sim-card__metaValue {{ $gainClass }}">{{ $gainSign }}{{ number_format($delta, 2) }} €</span>
                            </div>
                        @endif
                        <div class="sim-card__meta">
                            <span>{{ __('Last Updated') }}</span>
                            <span class="sim-card__metaValue">{{ $updatedText }}</span>
                        </div>
                        <div class="sim-card__meta">
                            <span>{{ __('Created') }}</span>
                            <span class="sim-card__metaValue">{{ $sim->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="sim-card__actions">
                        <a class="btn btn-primary btn-sm sim-card__btn" href="{{ route('simulations.edit', $sim) }}">{{ __('Edit') }}</a>
                        <form class="sim-card__btn-form" method="POST" action="{{ route('simulations.destroy', $sim) }}" onsubmit="return confirm('{{ __('Delete this simulation?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-sm sim-card__btn">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="sim-dash-empty">
            {{ __('No simulations yet.') }}
            <a href="{{ route('simulations.create') }}" class="sim-name-link">{{ __('Create your first simulation') }}</a>.
        </div>
    @endif
</section>
