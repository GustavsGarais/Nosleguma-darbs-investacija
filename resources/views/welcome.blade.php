@extends('layouts.app')

@section('title', __('Welcome'))

@section('content')
<section id="hero-section" role="region" aria-label="Hero" class="hero">
    <div class="hero-content">
        <div class="controls">
            <button
                type="button"
                class="theme-toggle theme-toggle--combined"
                aria-pressed="false"
                title="Toggle light or dark mode"
                style="display:flex; align-items:center; gap:6px; border:1px solid var(--c-border); border-radius:999px; padding:6px 12px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-primary) 5%); cursor:pointer;">
                <span class="theme-toggle__icon theme-toggle__icon--light" aria-hidden="true" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="4"></circle>
                        <path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                    </svg>
                </span>
                <span class="theme-toggle__icon theme-toggle__icon--dark" aria-hidden="true" style="display:flex; align-items:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                        <path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"></path>
                    </svg>
                </span>
                <span class="theme-toggle__label" style="font-size:13px; font-weight:600;">{{ __('Theme') }}</span>
            </button>
            @guest
                <a href="{{ route('login') }}" aria-label="Quick sign in" class="quick-signin btn btn-primary btn-sm">
                    {{ __('Log In') }}
                </a>
            @endguest
        </div>
        
        <h1 class="home-hero-title hero-title">
            {{ config('app.name') }}<br>
            {{ __('Invest Smarter, Simulate Faster') }}
        </h1>
        <p class="home-hero-subtitle hero-subtitle">
            {{ __('Hero Subtitle') }}
        </p>
        
        <div class="cta-cluster">
            @auth
                <a href="{{ route('simulations.create') }}" class="btn btn-primary btn-lg">{{ __('Create Simulation') }}</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">{{ __('Create Simulation') }}</a>
            @endauth
            <a href="{{ route('quick-tour') }}" class="btn btn-secondary">{{ __('Quick Tour') }}</a>
            <button class="btn btn-outline">{{ __('Learn More') }}</button>
        </div>

    </div>

    <div class="visual-stack">
        <div class="backplate"></div>
        <div class="chart-slice">
            <img src="https://images.pexels.com/photos/577195/pexels-photo-577195.jpeg?auto=compress&cs=tinysrgb&w=1500" alt="Overhead view of a laptop showing data visualizations and charts on its screen." />
        </div>
        <div class="simulation-mockup">
            <div class="mockup-overlay">
                <div class="mockup-content">
                    <span class="metric-label">Live Growth</span>
                    <span class="metric-value">+24.3%</span>
                </div>
            </div>
            <img src="https://images.pexels.com/photos/95916/pexels-photo-95916.jpeg?auto=compress&cs=tinysrgb&w=1500" alt="Flatlay of a business analytics report, keyboard, pen, and smartphone on a wooden desk." />
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const visualStack = document.querySelector('.visual-stack');
    if (visualStack) {
        const chartSlice = visualStack.querySelector('.chart-slice');
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const parallaxSpeed = 0.06;
            if (chartSlice) {
                chartSlice.style.transform = `translateY(${scrolled * parallaxSpeed}px)`;
            }
        });
    }
});
</script>
@endsection