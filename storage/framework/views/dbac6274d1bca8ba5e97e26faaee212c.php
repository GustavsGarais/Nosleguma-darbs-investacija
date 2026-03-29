<?php $__env->startSection('title', __('Forgot Password')); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-page">
    <section class="auth-card" aria-label="<?php echo e(__('Forgot Password')); ?>">
        <h1 style="margin:0 0 6px;"><?php echo e(__('Forgot your password?')); ?></h1>
        <p style="margin:0 0 16px; color: var(--c-on-surface-2);">
            <?php echo e(__('No problem. Enter your email and we will send a reset link.')); ?>

        </p>

        <?php if(session('status')): ?>
            <div role="status" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
                <ul style="margin:0; padding-left:18px;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li style="color: var(--c-on-surface);"><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('password.email')); ?>" style="display:grid; gap:12px;">
            <?php echo csrf_field(); ?>

            <label style="display:grid; gap:6px;">
                <span style="font-weight:700; color: var(--c-on-surface);"><?php echo e(__('Email')); ?></span>
                <input type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus class="footer-email-input" autocomplete="email" />
            </label>

            <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <a href="<?php echo e(route('login')); ?>" class="btn btn-outline"><?php echo e(__('Back to login')); ?></a>
                <button type="submit" class="btn btn-primary"><?php echo e(__('Email Password Reset Link')); ?></button>
            </div>
        </form>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/auth/forgot-password.blade.php ENDPATH**/ ?>