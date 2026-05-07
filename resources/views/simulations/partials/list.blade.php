<section class="auth-card sim-dash-list" aria-label="{{ __('Simulations') }}">
    <div class="sim-dash-list__toolbar" style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <h2 class="sim-dash-list__title" style="margin:0;">{{ __('Your Simulations') }}</h2>
        <a href="{{ route('simulations.create') }}" class="btn btn-primary btn-lg">{{ __('New Simulation') }}</a>
    </div>

    @if(session('success'))
        <div role="status" style="margin-top:12px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
            {{ session('success') }}
        </div>
    @endif

    @if($simulations->count())
        <div class="sim-cardGrid" role="list" aria-label="{{ __('Your Simulations') }}" style="margin-top:16px;">
            @foreach($simulations as $sim)
                @php
                    $snapshot = $sim->data['snapshot'] ?? null;
                    $runner = $sim->data['runner'] ?? null;

                    $initial = (float) ($sim->settings['initialInvestment'] ?? 0);
                    $lastValue = (float) ($snapshot['value'] ?? $initial);
                    $contrib = (float) ($snapshot['contributions'] ?? $initial);
                    $gain = (float) ($snapshot['total_gain'] ?? ($lastValue - $contrib));

                    // Runner stores progress as "month". Older saved runs used a daily timestep and
                    // stored days in these fields; detect and convert for display.
                    $currentMonth = (int) ($snapshot['month'] ?? 0);
                    $totalMonths = (int) ($runner['months'] ?? 360);
                    if ($totalMonths < 1) $totalMonths = 1;

                    if ($totalMonths > 600 || $currentMonth > 600) {
                        $currentMonth = (int) round($currentMonth / 30);
                        $totalMonths = (int) max(1, round($totalMonths / 30));
                    }

                    if ($currentMonth < 0) $currentMonth = 0;
                    if ($currentMonth > $totalMonths) $currentMonth = $totalMonths;

                    $progress = $totalMonths > 0 ? ($currentMonth / $totalMonths) : 0;
                    $progressPct = (int) round($progress * 100);

                    $gainPct = $contrib > 0 ? ($gain / $contrib) * 100 : 0;
                    $gainPctText = ($gainPct >= 0 ? '+' : '') . number_format($gainPct, 1) . '%';
                @endphp

                <article class="sim-card" role="listitem" aria-label="{{ $sim->name }}">
                    <div class="sim-card__topbar" aria-hidden="true"></div>

                    <div class="sim-card__body">
                        <a href="{{ route('simulations.show', $sim) }}" class="sim-card__title">
                            {{ $sim->name }}
                        </a>

                        <div class="sim-card__valueRow">
                            <span class="currency-value sim-card__value" data-currency-value="{{ $lastValue }}">{{ number_format($lastValue, 2, '.', ' ') }}</span>
                        </div>

                        <div class="sim-card__gain {{ $gain >= 0 ? 'is-up' : 'is-down' }}">
                            <span class="sim-card__gainSign" aria-hidden="true">{{ $gain >= 0 ? '+' : '−' }}</span>
                            <span class="currency-value" data-currency-value="{{ abs($gain) }}">{{ number_format(abs($gain), 2, '.', ' ') }}</span>
                        </div>

                        <div class="sim-card__meta">
                            <span class="sim-card__metaLabel">{{ __('Month') }}</span>
                            <span class="sim-card__metaValue">{{ $currentMonth }} / {{ $totalMonths }}</span>
                        </div>

                        <div class="sim-card__progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $progressPct }}" aria-label="{{ __('Month') }}">
                            <div class="sim-card__progressFill" style="width: {{ $progressPct }}%"></div>
                        </div>

                        <div class="sim-card__pct {{ $gainPct >= 0 ? 'is-up' : 'is-down' }}">
                            {{ $gainPctText }}
                        </div>
                    </div>

                    <div class="sim-card__actions">
                        <a class="btn btn-primary btn-sm sim-card__btn" href="{{ route('simulations.show', $sim) }}">{{ __('Run') }}</a>
                        <a class="btn btn-outline btn-sm sim-card__btn" href="{{ route('simulations.edit', $sim) }}">{{ __('Edit') }}</a>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <p class="sim-dash-empty">{{ __('No simulations yet.') }} <a href="{{ route('simulations.create') }}" class="sim-name-link">{{ __('Create your first simulation') }}</a>.</p>
    @endif
</section>
