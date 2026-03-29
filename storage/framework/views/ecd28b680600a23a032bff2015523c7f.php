<?php $__env->startSection('title', __('Verify Email')); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-page">
	<section class="auth-card" aria-label="<?php echo e(__('Verify Email')); ?>">
		<h1 style="margin:0 0 6px;"><?php echo e(__('Verify Email')); ?></h1>
		<p style="margin:0 0 16px; color: var(--c-on-surface-2);">
			<?php echo e(__('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn’t receive the email, we will gladly send you another.')); ?>

		</p>

		<?php if(session('status') === 'verification-link-sent'): ?>
			<div role="status" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
				<?php echo e(__('A new verification link has been sent to the email address you provided during registration.')); ?>

			</div>
		<?php endif; ?>

		<div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
			<form method="POST" action="<?php echo e(route('verification.send')); ?>" style="margin:0;">
				<?php echo csrf_field(); ?>
				<button type="submit" class="btn btn-primary"><?php echo e(__('Resend Verification Email')); ?></button>
			</form>

			<form method="POST" action="<?php echo e(route('logout')); ?>" style="margin:0;">
				<?php echo csrf_field(); ?>
				<button type="submit" class="btn btn-outline"><?php echo e(__('Logout')); ?></button>
			</form>
		</div>
	</section>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/auth/verify-email.blade.php ENDPATH**/ ?>