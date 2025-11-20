@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<section id="hero-section" role="region" aria-label="Hero" class="hero">
    <div class="hero-content">
        <div class="controls">
            <button aria-pressed="false" aria-label="Toggle light theme" class="theme-toggle" data-theme="light">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32l1.41 1.41M2 12h2m16 0h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"></path>
                </svg>
                <span>Light</span>
            </button>
            <button aria-pressed="false" aria-label="Toggle dark theme" class="theme-toggle" data-theme="dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                    <path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"></path>
                </svg>
                <span>Dark</span>
            </button>
            @guest
                <a href="{{ route('login') }}" aria-label="Quick sign in" class="quick-signin btn btn-primary btn-sm">
                    Sign In
                </a>
            @endguest
        </div>
        
        <h1 class="home-hero-title hero-title">
            {{ config('app.name') }} — Invest Smarter, Simulate Faster
        </h1>
        <p class="home-hero-subtitle hero-subtitle">
            Log in to explore live simulations • Create scenarios • Save strategies
        </p>
        
        <div class="cta-cluster">
            @auth
                <a href="{{ route('simulations.create') }}" class="btn btn-primary btn-lg">Create Simulation</a>
            @else
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Create Simulation</a>
            @endauth
            <button class="btn btn-secondary">Quick Tour</button>
            <button class="btn btn-outline">Learn More</button>
        </div>

        <div class="regulatory-badge">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24">
                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
            </svg>
            <span>Secure • Encrypted • Compliant</span>
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