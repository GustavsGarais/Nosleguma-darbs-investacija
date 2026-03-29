<?php $__env->startSection('title', __('Welcome')); ?>

<?php $__env->startSection('content'); ?>
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
                <span class="theme-toggle__label" style="font-size:13px; font-weight:600;"><?php echo e(__('Theme')); ?></span>
            </button>
            <?php if(auth()->guard()->guest()): ?>
                <a href="<?php echo e(route('login')); ?>" aria-label="Quick sign in" class="quick-signin btn btn-primary btn-sm">
                    <?php echo e(__('Log In')); ?>

                </a>
            <?php endif; ?>
        </div>
        
        <h1 class="home-hero-title hero-title">
            <?php echo e(config('app.name')); ?><br>
            <?php echo e(__('Invest Smarter, Simulate Faster')); ?>

        </h1>
        <p class="home-hero-subtitle hero-subtitle">
            <?php echo e(__('Hero Subtitle')); ?>

        </p>
        
        <div class="cta-cluster">
            <?php if(auth()->guard()->check()): ?>
                <a href="<?php echo e(route('simulations.create')); ?>" class="btn btn-primary btn-lg"><?php echo e(__('Create Simulation')); ?></a>
            <?php else: ?>
                <a href="<?php echo e(route('register')); ?>" class="btn btn-primary btn-lg"><?php echo e(__('Create Simulation')); ?></a>
            <?php endif; ?>
            <a href="<?php echo e(route('quick-tour')); ?>" class="btn btn-secondary"><?php echo e(__('Quick Tour')); ?></a>
            <a class="btn btn-outline" href="#learn-more"><?php echo e(__('Learn More')); ?></a>
        </div>

    </div>

    <div class="visual-stack">
        <div class="backplate"></div>
        <div class="chart-slice">
            <img src="<?php echo e(asset('images/hero-chart.svg')); ?>" width="480" height="360" alt="" decoding="async" loading="lazy" />
        </div>
        <div class="simulation-mockup">
            <div class="mockup-overlay">
                <div class="mockup-content">
                    <span class="metric-label"><?php echo e(__('Live Growth')); ?></span>
                    <span class="metric-value">+24.3%</span>
                </div>
            </div>
            <img src="<?php echo e(asset('images/hero-planning.svg')); ?>" width="480" height="360" alt="" decoding="async" loading="lazy" />
        </div>
    </div>
</section>

<section class="home-section" aria-label="<?php echo e(__('How it works')); ?>">
    <div class="home-section__inner">
        <div class="home-section__header">
            <div>
                <p class="home-section__kicker"><?php echo e(__('How it works')); ?></p>
                <h2 class="home-section__title"><?php echo e(__('A simple workflow')); ?></h2>
                <p class="home-section__subtitle"><?php echo e(__('Create a scenario, run it, and save snapshots so you can compare outcomes.')); ?></p>
            </div>
        </div>

        <div class="home-steps">
            <div class="home-step">
                <div class="home-step__num">1</div>
                <h3 class="home-step__title"><?php echo e(__('Create a scenario')); ?></h3>
                <p class="home-step__text"><?php echo e(__('Set an initial investment, monthly contributions, and assumptions like growth and inflation.')); ?></p>
            </div>
            <div class="home-step">
                <div class="home-step__num">2</div>
                <h3 class="home-step__title"><?php echo e(__('Run the simulation')); ?></h3>
                <p class="home-step__text"><?php echo e(__('Use market regimes (balanced, growth, defensive, volatile, stress test) to explore different paths.')); ?></p>
            </div>
            <div class="home-step">
                <div class="home-step__num">3</div>
                <h3 class="home-step__title"><?php echo e(__('Learn and compare')); ?></h3>
                <p class="home-step__text"><?php echo e(__('Save progress, review events, and compare multiple scenarios to understand trade-offs.')); ?></p>
            </div>
        </div>

        <div style="margin-top:18px;" class="home-ctaRow">
            <div>
                <p class="home-ctaRow__title"><?php echo e(__('Try the guided demo first')); ?></p>
                <p class="home-ctaRow__text"><?php echo e(__('If you’re new, start with the Quick Tour and then create your own simulation.')); ?></p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="<?php echo e(route('quick-tour')); ?>" class="btn btn-secondary"><?php echo e(__('Quick Tour')); ?></a>
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('simulations.create')); ?>" class="btn btn-primary"><?php echo e(__('Create Simulation')); ?></a>
                <?php else: ?>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-primary"><?php echo e(__('Create free account')); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section id="learn-more" class="home-section" aria-label="<?php echo e(__('Learn more')); ?>">
    <div class="home-section__inner">
        <div class="home-section__header">
            <div>
                <p class="home-section__kicker"><?php echo e(__('Built for learning')); ?></p>
                <h2 class="home-section__title"><?php echo e(__('Understand compounding — not just charts')); ?></h2>
                <p class="home-section__subtitle">
                    <?php echo e(__('This app is designed to help you build intuition. Run scenarios, step month-by-month, and compare nominal growth vs inflation-adjusted purchasing power.')); ?>

                </p>
            </div>
            <?php if(auth()->guard()->check()): ?>
                <a class="btn btn-primary" href="<?php echo e(route('simulations.index')); ?>"><?php echo e(__('Open dashboard')); ?></a>
            <?php else: ?>
                <a class="btn btn-primary" href="<?php echo e(route('register')); ?>"><?php echo e(__('Create free account')); ?></a>
            <?php endif; ?>
        </div>

        <div class="home-cards" aria-label="<?php echo e(__('Key features')); ?>">
            <div class="home-card">
                <div class="home-card__icon" aria-hidden="true">⏱</div>
                <h3 class="home-card__title"><?php echo e(__('Run or Step')); ?></h3>
                <p class="home-card__text"><?php echo e(__('Use Step mode to see exactly what happens each month. Then Run to understand longer horizons (10–30 years).')); ?></p>
            </div>
            <div class="home-card">
                <div class="home-card__icon" aria-hidden="true">📉</div>
                <h3 class="home-card__title"><?php echo e(__('Risk & drawdowns')); ?></h3>
                <p class="home-card__text"><?php echo e(__('Volatility matters. The simulator highlights drawdowns so you learn why staying consistent is hard — and why it matters.')); ?></p>
            </div>
            <div class="home-card">
                <div class="home-card__icon" aria-hidden="true">🧾</div>
                <h3 class="home-card__title"><?php echo e(__('Nominal vs real')); ?></h3>
                <p class="home-card__text"><?php echo e(__('A bigger number is not always a better outcome. Compare nominal value to real value after inflation.')); ?></p>
            </div>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/welcome.blade.php ENDPATH**/ ?>