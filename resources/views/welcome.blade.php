@extends('layouts.app')

@section('title', __('Welcome'))

@php
    $heroVisualSlides = [
        [
            'chart' => 'images/hero-chart.svg',
            'planning' => 'images/hero-planning.svg',
            'label' => __('Live Growth'),
            'value' => '+24.3%',
        ],
        [
            'chart' => 'images/hero-chart-b.svg',
            'planning' => 'images/hero-planning-b.svg',
            'label' => __('Scenario mix'),
            'value' => '+18.1%',
        ],
        [
            'chart' => 'images/hero-chart-c.svg',
            'planning' => 'images/hero-planning-c.svg',
            'label' => __('Real Value'),
            'value' => '+12.4%',
        ],
    ];
    $heroVisualConfig = [
        'interval' => 10_000,
        'announcements' => array_map(
            static fn (array $s): string => trim($s['label'].' '.$s['value']),
            $heroVisualSlides
        ),
    ];
@endphp

@section('content')
<div class="home-ambient">
<section id="hero-section" role="region" aria-label="{{ __('Hero') }}" class="hero hero--frameless">
    <div class="hero-content">
        <h1 class="home-hero-title hero-title">
            {{ config('app.name') }}<br>
            {{ __('Invest Smarter, Simulate Faster') }}
        </h1>
        <p class="home-hero-subtitle hero-subtitle">
            {{ __('Simulate investing day-by-day to learn compounding, volatility, and inflation — with no real money.') }}
        </p>

        <div class="home-hero-badges" aria-label="{{ __('Key points') }}">
            <span class="home-hero-badge">{{ __('Educational only') }}</span>
            <span class="home-hero-badge">{{ __('No real money') }}</span>
            <span class="home-hero-badge">{{ __('Nominal vs real value') }}</span>
        </div>
        
        <div class="cta-cluster">
            @auth
                <a href="{{ route('simulations.create') }}" class="btn btn-primary btn-lg">{{ __('Create Simulation') }}</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">{{ __('Create Simulation') }}</a>
            @endauth
            <a href="{{ route('quick-tour') }}" class="btn btn-secondary">{{ __('Quick Tour') }}</a>
        </div>

        <div class="home-hero-points" aria-label="{{ __('Highlights') }}">
            <div class="home-hero-point">
                <span class="home-hero-point__icon" aria-hidden="true">⏱</span>
                <span class="home-hero-point__text">{{ __('Step day-by-day') }}</span>
            </div>
            <div class="home-hero-point">
                <span class="home-hero-point__icon" aria-hidden="true">📉</span>
                <span class="home-hero-point__text">{{ __('See drawdowns & recovery') }}</span>
            </div>
            <div class="home-hero-point">
                <span class="home-hero-point__icon" aria-hidden="true">💾</span>
                <span class="home-hero-point__text">{{ __('Save snapshots to compare') }}</span>
            </div>
        </div>

    </div>

    <div
        class="visual-stack"
        id="hero-visual-stack"
        role="region"
        aria-label="{{ __('Hero illustrations') }}"
    >
        <span id="hero-visual-live" class="sr-only" aria-live="polite"></span>
        <div class="backplate" aria-hidden="true"></div>
        <div class="hero-visual-charts" aria-hidden="true">
            @foreach ($heroVisualSlides as $i => $slide)
                <div
                    class="chart-slice hero-visual-slide {{ $i === 0 ? 'is-active' : '' }}"
                    data-hero-slide="{{ $i }}"
                >
                    <img src="{{ asset($slide['chart']) }}" width="480" height="360" alt="" decoding="async" @if($i === 0) fetchpriority="high" @else loading="lazy" @endif />
                </div>
            @endforeach
        </div>
        <div class="hero-visual-mockups">
            @foreach ($heroVisualSlides as $i => $slide)
                <div
                    class="simulation-mockup hero-visual-slide {{ $i === 0 ? 'is-active' : '' }}"
                    data-hero-slide="{{ $i }}"
                    role="group"
                    aria-label="{{ $slide['label'] }} {{ $slide['value'] }}"
                    @if($i !== 0) aria-hidden="true" @endif
                >
                    <div class="mockup-overlay" aria-hidden="true">
                        <div class="mockup-content">
                            <span class="metric-label">{{ $slide['label'] }}</span>
                            <span class="metric-value">{{ $slide['value'] }}</span>
                        </div>
                    </div>
                    <img src="{{ asset($slide['planning']) }}" width="480" height="360" alt="" decoding="async" @if($i === 0) fetchpriority="high" @else loading="lazy" @endif />
                </div>
            @endforeach
        </div>
    </div>
</section>

<script type="application/json" id="hero-visual-config">@json($heroVisualConfig)</script>
</div>

<section class="home-section home-section--preview" aria-label="{{ __('Preview simulation') }}">
    <div class="home-section__inner home-preview">
        <div class="home-preview__left">
            <p class="home-section__kicker">{{ __('Try it instantly') }}</p>
            <h2 class="home-section__title">{{ __('Preview a scenario in 10 seconds') }}</h2>
            <p class="home-section__subtitle">{{ __('Adjust a few inputs and see how contributions + volatility can change the path. Results are illustrative.') }}</p>

            <div class="home-mini" data-mini-sim>
                <div class="home-mini__controls">
                    <label class="home-mini__field">
                        <span class="home-mini__label">{{ __('Initial') }}</span>
                        <input class="home-mini__input" name="initial" type="number" min="0" step="100" value="1000" inputmode="numeric">
                    </label>
                    <label class="home-mini__field">
                        <span class="home-mini__label">{{ __('Monthly contribution') }}</span>
                        <input class="home-mini__input" name="monthly" type="number" min="0" step="50" value="100" inputmode="numeric">
                    </label>
                    <label class="home-mini__field">
                        <span class="home-mini__label">{{ __('Horizon (years)') }}</span>
                        <input class="home-mini__input" name="years" type="number" min="1" max="50" step="1" value="10" inputmode="numeric">
                    </label>
                    <label class="home-mini__field">
                        <span class="home-mini__label">{{ __('Regime') }}</span>
                        <select class="home-mini__input" name="regime">
                            <option value="balanced">{{ __('Balanced') }}</option>
                            <option value="growth">{{ __('Growth') }}</option>
                            <option value="defensive">{{ __('Defensive') }}</option>
                            <option value="volatile">{{ __('Volatile') }}</option>
                            <option value="stress">{{ __('Stress test') }}</option>
                        </select>
                    </label>
                </div>

                <div class="home-mini__results" role="status" aria-live="polite">
                    <div class="home-mini__metric">
                        <div class="home-mini__metric-k">{{ __('Expected end value') }}</div>
                        <div class="home-mini__metric-v" data-mini-end>—</div>
                    </div>
                    <div class="home-mini__metric">
                        <div class="home-mini__metric-k">{{ __('Total contributions') }}</div>
                        <div class="home-mini__metric-v" data-mini-contrib>—</div>
                    </div>
                    <div class="home-mini__metric">
                        <div class="home-mini__metric-k">{{ __('Illustrative range') }}</div>
                        <div class="home-mini__metric-v" data-mini-range>—</div>
                    </div>
                    <div class="home-mini__spark" aria-hidden="true">
                        <svg viewBox="0 0 240 56" width="240" height="56" preserveAspectRatio="none">
                            <path d="" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" opacity="0.85" data-mini-path></path>
                            <path d="" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="4 5" stroke-linecap="round" opacity="0.32" data-mini-path-low></path>
                            <path d="" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="4 5" stroke-linecap="round" opacity="0.32" data-mini-path-high></path>
                        </svg>
                    </div>
                </div>

                <div class="home-mini__cta">
                    @auth
                        <a href="{{ route('simulations.create') }}" class="btn btn-primary">{{ __('Create this simulation') }}</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary">{{ __('Create free account') }}</a>
                    @endauth
                    <a href="{{ route('quick-tour') }}" class="btn btn-secondary">{{ __('See the full demo') }}</a>
                </div>
            </div>
        </div>

        <div class="home-preview__right" aria-label="{{ __('Example scenarios') }}">
            <div class="home-preview__cards">
                <div class="home-previewCard">
                    <div class="home-previewCard__top">
                        <div class="home-previewCard__tag">{{ __('Example') }}</div>
                        <div class="home-previewCard__name">{{ __('Balanced path') }}</div>
                    </div>
                    <p class="home-previewCard__text">{{ __('A steady baseline: learn compounding and the impact of consistent contributions.') }}</p>
                </div>
                <div class="home-previewCard">
                    <div class="home-previewCard__top">
                        <div class="home-previewCard__tag">{{ __('Example') }}</div>
                        <div class="home-previewCard__name">{{ __('Volatility lesson') }}</div>
                    </div>
                    <p class="home-previewCard__text">{{ __('Same inputs, rougher ride: see drawdowns, recovery, and sequence-of-returns effects.') }}</p>
                </div>
                <div class="home-previewCard">
                    <div class="home-previewCard__top">
                        <div class="home-previewCard__tag">{{ __('Example') }}</div>
                        <div class="home-previewCard__name">{{ __('Crash + recovery') }}</div>
                    </div>
                    <p class="home-previewCard__text">{{ __('Stress test your plan. Notice how time horizon and staying invested matter.') }}</p>
                </div>
            </div>

            <div class="home-preview__note">
                <p><strong>{{ __('Heads up') }}:</strong> {{ __('These numbers are illustrative. The full simulator shows day-by-day events, snapshots, and comparisons.') }}</p>
            </div>
        </div>
    </div>
</section>

<section class="home-section" aria-label="{{ __('How it works') }}">
    <div class="home-section__inner">
        <div class="home-section__header">
            <div>
                <p class="home-section__kicker">{{ __('How it works') }}</p>
                <h2 class="home-section__title">{{ __('A simple workflow') }}</h2>
                <p class="home-section__subtitle">{{ __('Create a scenario, run it, and save snapshots so you can compare outcomes.') }}</p>
            </div>
        </div>

        <div class="home-steps home-steps--enhanced">
            <div class="home-step">
                <div class="home-step__num">1</div>
                <h3 class="home-step__title">{{ __('Create a scenario') }}</h3>
                <p class="home-step__text">{{ __('Set an initial investment, monthly contributions, and assumptions like growth and inflation.') }}</p>
                <div class="home-step__meta">{{ __('Takes 1–2 minutes') }}</div>
            </div>
            <div class="home-step">
                <div class="home-step__num">2</div>
                <h3 class="home-step__title">{{ __('Run the simulation') }}</h3>
                <p class="home-step__text">{{ __('Use market regimes (balanced, growth, defensive, volatile, stress test) to explore different paths.') }}</p>
                <div class="home-step__meta">{{ __('Try Stress test for drawdowns') }}</div>
            </div>
            <div class="home-step">
                <div class="home-step__num">3</div>
                <h3 class="home-step__title">{{ __('Learn and compare') }}</h3>
                <p class="home-step__text">{{ __('Save progress, review events, and compare multiple scenarios to understand trade-offs.') }}</p>
                <div class="home-step__meta">{{ __('Nominal vs inflation-adjusted') }}</div>
            </div>
        </div>

        <div style="margin-top:18px;" class="home-ctaRow">
            <div>
                <p class="home-ctaRow__title">{{ __('Try the guided demo first') }}</p>
                <p class="home-ctaRow__text">{{ __('If you’re new, start with the Quick Tour and then create your own simulation.') }}</p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="{{ route('quick-tour') }}" class="btn btn-secondary">{{ __('Quick Tour') }}</a>
                @auth
                    <a href="{{ route('simulations.create') }}" class="btn btn-primary">{{ __('Create Simulation') }}</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary">{{ __('Create free account') }}</a>
                @endauth
            </div>
        </div>
    </div>
</section>

<section id="learn-more" class="home-section" aria-label="{{ __('Learn more') }}">
    <div class="home-section__inner">
        <div class="home-section__header">
            <div>
                <p class="home-section__kicker">{{ __('Built for learning') }}</p>
                <h2 class="home-section__title">{{ __('Understand compounding — not just charts') }}</h2>
                <p class="home-section__subtitle">
                    {{ __('This app is designed to help you build intuition. Run scenarios, step day-by-day, and compare nominal growth vs inflation-adjusted purchasing power.') }}
                </p>
            </div>
            @auth
                <a class="btn btn-primary" href="{{ route('simulations.index') }}">{{ __('Open dashboard') }}</a>
            @else
                <a class="btn btn-primary" href="{{ route('register') }}">{{ __('Create free account') }}</a>
            @endauth
        </div>

        <div class="home-cards home-cards--enhanced" aria-label="{{ __('Key features') }}">
            <div class="home-card">
                <div class="home-card__icon" aria-hidden="true">⏱</div>
                <h3 class="home-card__title">{{ __('Step day-by-day') }}</h3>
                <p class="home-card__text">{{ __('Use Step mode to see what happens each day, then Run to explore long horizons (10–30 years).') }}</p>
            </div>
            <div class="home-card">
                <div class="home-card__icon" aria-hidden="true">📉</div>
                <h3 class="home-card__title">{{ __('Risk & drawdowns') }}</h3>
                <p class="home-card__text">{{ __('Volatility matters. The simulator highlights drawdowns so you learn why staying consistent is hard — and why it matters.') }}</p>
            </div>
            <div class="home-card">
                <div class="home-card__icon" aria-hidden="true">🧾</div>
                <h3 class="home-card__title">{{ __('Nominal vs real') }}</h3>
                <p class="home-card__text">{{ __('A bigger number is not always a better outcome. Compare nominal value to real value after inflation.') }}</p>
            </div>
            <div class="home-card">
                <div class="home-card__icon" aria-hidden="true">🧪</div>
                <h3 class="home-card__title">{{ __('Save & compare') }}</h3>
                <p class="home-card__text">{{ __('Overlay a second scenario to see trade-offs: contributions vs risk, and sequence-of-returns effects.') }}</p>
            </div>
        </div>

        <div class="home-trust" aria-label="{{ __('Important notes') }}">
            <div class="home-trust__item">
                <strong>{{ __('Educational simulator') }}</strong>
                <span>{{ __('Illustrative results, not financial advice, and no real market data.') }}</span>
            </div>
            <div class="home-trust__item">
                <strong>{{ __('Private by default') }}</strong>
                <span>{{ __('Your simulations are private to your account. Email verification and optional 2FA are supported.') }}</span>
            </div>
        </div>

        <div class="home-section__header" style="margin-top:24px;">
            <div>
                <p class="home-section__kicker">{{ __('App preview') }}</p>
                <h2 class="home-section__title">{{ __('See the experience before you sign up') }}</h2>
                <p class="home-section__subtitle">{{ __('A quick look at the simulator UI: controls, outcomes, and comparison modes.') }}</p>
            </div>
        </div>

        <div class="home-shots" aria-label="{{ __('App screenshots') }}">
            <figure class="home-shot">
                <div class="home-shot__img">
                    <img src="{{ asset('images/hero-planning.svg') }}" width="900" height="675" alt="{{ __('Simulation controls preview') }}" loading="lazy" decoding="async">
                </div>
                <figcaption class="home-shot__cap">{{ __('Controls + scenarios') }}</figcaption>
            </figure>
            <figure class="home-shot">
                <div class="home-shot__img">
                    <img src="{{ asset('images/hero-chart.svg') }}" width="900" height="675" alt="{{ __('Portfolio chart preview') }}" loading="lazy" decoding="async">
                </div>
                <figcaption class="home-shot__cap">{{ __('Nominal vs real value') }}</figcaption>
            </figure>
            <figure class="home-shot">
                <div class="home-shot__img">
                    <img src="{{ asset('images/hero-chart-b.svg') }}" width="900" height="675" alt="{{ __('Scenario comparison preview') }}" loading="lazy" decoding="async">
                </div>
                <figcaption class="home-shot__cap">{{ __('Compare choices') }}</figcaption>
            </figure>
        </div>

        <div class="home-reviews" aria-label="{{ __('Reviews') }}">
            <h3 class="home-faq__title">{{ __('What learners say') }}</h3>
            <div class="home-reviews__grid">
                <blockquote class="home-review">
                    <p>{{ __('“Stepping day-by-day made compounding finally click for me.”') }}</p>
                    <footer>— {{ __('Student') }}</footer>
                </blockquote>
                <blockquote class="home-review">
                    <p>{{ __('“Seeing drawdowns alongside CAGR was the best lesson: returns are not a straight line.”') }}</p>
                    <footer>— {{ __('Beginner investor') }}</footer>
                </blockquote>
                <blockquote class="home-review">
                    <p>{{ __('“Nominal vs real value is a great reminder that inflation matters.”') }}</p>
                    <footer>— {{ __('Classroom demo') }}</footer>
                </blockquote>
            </div>
        </div>

        <div class="home-faq" aria-label="{{ __('FAQ') }}">
            <h3 class="home-faq__title">{{ __('Common questions') }}</h3>
            <div class="home-faq__grid">
                <details class="home-faq__item">
                    <summary>{{ __('Is this real investing?') }}</summary>
                    <p>{{ __('No — it is an educational simulator. It uses simplified, random market behavior to teach concepts like compounding, volatility, and inflation.') }}</p>
                </details>
                <details class="home-faq__item">
                    <summary>{{ __('How are returns generated?') }}</summary>
                    <p>{{ __('Returns are generated by a simplified model using daily growth assumptions and randomness (volatility). The goal is learning — not prediction.') }}</p>
                </details>
                <details class="home-faq__item">
                    <summary>{{ __('What does “real value” mean?') }}</summary>
                    <p>{{ __('Real value adjusts your portfolio for inflation, so you can compare account balance vs purchasing power.') }}</p>
                </details>
                <details class="home-faq__item">
                    <summary>{{ __('Why do I see drawdowns?') }}</summary>
                    <p>{{ __('Drawdowns are normal in markets. The simulator highlights them so you learn how risk feels over time — not just the final result.') }}</p>
                </details>
                <details class="home-faq__item">
                    <summary>{{ __('What is a market regime?') }}</summary>
                    <p>{{ __('A regime is a “market mood” preset (e.g., balanced, defensive, volatile, stress) that changes the simulated return and volatility pattern.') }}</p>
                </details>
                <details class="home-faq__item">
                    <summary>{{ __('How do I start?') }}</summary>
                    <p>{{ __('Use Quick Tour first, then create your own simulation and Step for a few days before running long horizons.') }}</p>
                </details>
                <details class="home-faq__item">
                    <summary>{{ __('Do I need an account?') }}</summary>
                    <p>{{ __('You can preview the idea on the homepage, but you need an account to save simulations, snapshots, and comparisons.') }}</p>
                </details>
            </div>
        </div>

        <div class="home-finalCta" aria-label="{{ __('Get started') }}">
            <div>
                <h3 class="home-finalCta__title">{{ __('Ready to explore a scenario?') }}</h3>
                <p class="home-finalCta__text">{{ __('Start with the guided demo or create your own simulation in under two minutes.') }}</p>
            </div>
            <div class="home-finalCta__actions">
                <a href="{{ route('quick-tour') }}" class="btn btn-secondary btn-lg">{{ __('Quick Tour') }}</a>
                @auth
                    <a href="{{ route('simulations.create') }}" class="btn btn-primary btn-lg">{{ __('Create Simulation') }}</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">{{ __('Create free account') }}</a>
                @endauth
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const visualStack = document.getElementById('hero-visual-stack');
    const chartsLayer = visualStack ? visualStack.querySelector('.hero-visual-charts') : null;
    if (visualStack && chartsLayer) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const parallaxSpeed = 0.06;
            chartsLayer.style.transform = 'translateY(' + (scrolled * parallaxSpeed) + 'px)';
        }, { passive: true });
    }

    const cfgEl = document.getElementById('hero-visual-config');
    const liveEl = document.getElementById('hero-visual-live');
    if (!visualStack || !cfgEl) return;

    let cfg;
    try {
        cfg = JSON.parse(cfgEl.textContent || '{}');
    } catch (e) {
        return;
    }
    if (!visualStack.querySelector('[data-hero-slide]') || !cfg.announcements || !cfg.announcements.length) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const intervalMs = typeof cfg.interval === 'number' ? cfg.interval : 10000;
    const n = cfg.announcements.length;
    let idx = 0;

    function setSlide(next) {
        idx = ((next % n) + n) % n;
        visualStack.querySelectorAll('.hero-visual-slide').forEach(function(el) {
            var i = parseInt(el.getAttribute('data-hero-slide'), 10);
            var on = i === idx;
            el.classList.toggle('is-active', on);
            if (el.classList.contains('simulation-mockup')) {
                el.setAttribute('aria-hidden', on ? 'false' : 'true');
            }
        });
        if (liveEl && cfg.announcements[idx]) {
            liveEl.textContent = cfg.announcements[idx];
        }
    }

    if (liveEl && cfg.announcements[0]) {
        liveEl.textContent = cfg.announcements[0];
    }

    if (!reduceMotion && n > 1 && intervalMs > 0) {
        window.setInterval(function() {
            setSlide(idx + 1);
        }, intervalMs);
    }
});
</script>
@endsection