

<?php $__env->startSection('title', 'Edit Simulation'); ?>

<?php $__env->startSection('dashboard_content'); ?>
<section class="auth-card" aria-label="<?php echo e(__('Edit Simulation')); ?>">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
        <h1 style="margin:0;"><?php echo e(__('Edit Simulation')); ?></h1>
        <a class="btn btn-secondary" href="<?php echo e(route('simulations.show', $simulation)); ?>"><?php echo e(__('Back')); ?></a>
    </div>

    <?php if($errors->any()): ?>
        <div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
            <ul style="margin:0; padding-left:18px;">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li style="color: var(--c-on-surface);"><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('simulations.update', $simulation)); ?>" style="display:grid; gap:12px;">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <label style="display:grid; gap:6px;">
            <div style="display:flex; align-items:center; gap:6px;">
                <span><?php echo e(__('Name')); ?></span>
                <span style="font-size:12px; color:var(--c-on-surface-2);"><?php echo e(__('(max 30 characters)')); ?></span>
            </div>
            <input type="text" name="name" value="<?php echo e(old('name', $simulation->name)); ?>" required maxlength="30" class="footer-email-input" />
        </label>

        <?php ($s = $simulation->settings); ?>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:12px;">
            <label style="display:grid; gap:6px;">
                <span><?php echo e(__('Initial Investment')); ?></span>
                <input type="number" step="1" name="initial_investment" value="<?php echo e(old('initial_investment', $s['initialInvestment'] ?? 1000)); ?>" required class="footer-email-input" />
            </label>
            <label style="display:grid; gap:6px;">
                <span><?php echo e(__('Monthly Contribution')); ?></span>
                <input type="number" step="1" name="monthly_contribution" value="<?php echo e(old('monthly_contribution', $s['monthlyContribution'] ?? 100)); ?>" required class="footer-email-input" />
            </label>
            <label style="display:grid; gap:6px;">
                <span><?php echo e(__('Growth Rate (annual)')); ?> <span style="font-size:12px; color:var(--c-on-surface-2);">0–100%</span></span>
                <div class="accel-input" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="growth_rate" value="<?php echo e(old('growth_rate', round(($s['growthRate'] ?? 0.07) * 100, 2))); ?>" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label style="display:grid; gap:6px;">
                <span><?php echo e(__('Risk Appetite')); ?> <span style="font-size:12px; color:var(--c-on-surface-2);">0–100%</span></span>
                <div class="accel-input" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="risk_appetite" value="<?php echo e(old('risk_appetite', round(($s['riskAppetite'] ?? 0.5) * 100, 2))); ?>" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label style="display:grid; gap:6px;">
                <span><?php echo e(__('Market Influence')); ?> <span style="font-size:12px; color:var(--c-on-surface-2);">0–100%</span></span>
                <div class="accel-input" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="market_influence" value="<?php echo e(old('market_influence', round(($s['marketInfluence'] ?? 0.5) * 100, 2))); ?>" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label style="display:grid; gap:6px;">
                <span><?php echo e(__('Inflation Rate (annual)')); ?> <span style="font-size:12px; color:var(--c-on-surface-2);">0–100%</span></span>
                <div class="accel-input" style="display:flex; gap:8px; align-items:center;">
                    <button type="button" class="btn btn-outline btn-sm accel-minus" aria-label="Decrease">−</button>
                    <input type="number" step="0.01" min="0" max="100" name="inflation_rate" value="<?php echo e(old('inflation_rate', round(($s['inflationRate'] ?? 0.02) * 100, 2))); ?>" required class="footer-email-input" data-accel="percent" />
                    <button type="button" class="btn btn-outline btn-sm accel-plus" aria-label="Increase">+</button>
                </div>
            </label>
            <label style="display:grid; gap:6px;">
                <span><?php echo e(__('Investors (count)')); ?></span>
                <input type="number" step="1" min="1" name="investors" value="<?php echo e(old('investors', $s['investors'] ?? 1)); ?>" required class="footer-email-input" />
            </label>
        </div>

        <div style="display:flex; gap:12px;">
            <a href="<?php echo e(route('simulations.show', $simulation)); ?>" class="btn btn-outline"><?php echo e(__('Cancel')); ?></a>
            <button type="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
        </div>
    </form>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const clamp = (v, min, max) => Math.min(max, Math.max(min, v));
    const getStep = (elapsedMs) => {
        if (elapsedMs >= 4000) return 1;
        if (elapsedMs >= 2000) return 0.1;
        return 0.01;
    };
    const setupAccel = (container) => {
        const input = container.querySelector('input[data-accel="percent"]');
        const minus = container.querySelector('.accel-minus');
        const plus = container.querySelector('.accel-plus');
        if (!input || !minus || !plus) return;

        const min = Number(input.min ?? 0);
        const max = Number(input.max ?? 100);
        let timer = null;
        let start = 0;

        const tick = (dir) => {
            const elapsed = Date.now() - start;
            const step = getStep(elapsed) * dir;
            const current = Number(input.value || 0);
            const next = clamp(Math.round((current + step) * 100) / 100, min, max);
            input.value = next.toFixed(2).replace(/\.00$/, '').replace(/(\.\d)0$/, '$1');
            input.dispatchEvent(new Event('input', { bubbles: true }));
        };

        const startHold = (dir) => {
            if (timer) clearInterval(timer);
            start = Date.now();
            tick(dir);
            timer = setInterval(() => tick(dir), 50);
        };

        const stopHold = () => {
            if (timer) clearInterval(timer);
            timer = null;
        };

        const bind = (btn, dir) => {
            btn.addEventListener('mousedown', () => startHold(dir));
            btn.addEventListener('touchstart', (e) => { e.preventDefault(); startHold(dir); }, { passive:false });
        };

        bind(minus, -1);
        bind(plus, 1);
        ['mouseup','mouseleave','touchend','touchcancel'].forEach(evt => {
            minus.addEventListener(evt, stopHold);
            plus.addEventListener(evt, stopHold);
        });
        document.addEventListener('mouseup', stopHold);
        document.addEventListener('touchend', stopHold);
    };

    document.querySelectorAll('.accel-input').forEach(setupAccel);
});
</script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/simulations/edit.blade.php ENDPATH**/ ?>