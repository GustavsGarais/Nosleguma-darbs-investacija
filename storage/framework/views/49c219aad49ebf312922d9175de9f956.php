<?php $__env->startSection('title', 'Support request received'); ?>

<?php $__env->startSection('content'); ?>
    <section class="auth-card" aria-label="Support request received">
        <h1 style="margin:0 0 12px;">Support request received</h1>

        <?php if(session('success')): ?>
            <div role="status" style="padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
                <?php echo e(session('success')); ?>

            </div>
        <?php else: ?>
            <div role="status" style="padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
                Your request was submitted successfully.
            </div>
        <?php endif; ?>

        <p style="margin:16px 0 0; color:var(--c-on-surface-2); line-height:1.6;">
            You can continue using the site. An admin will review your request and email you when it is resolved.
        </p>

        <div style="margin-top:16px; display:flex; gap:12px; flex-wrap:wrap;">
            <a href="<?php echo e(url('/')); ?>" class="btn btn-outline">Back to home</a>
            <?php if(auth()->check()): ?>
                <a href="<?php echo e(route('tickets.index')); ?>" class="btn btn-primary">View my tickets</a>
            <?php endif; ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/support/two-factor-recovery-thanks.blade.php ENDPATH**/ ?>