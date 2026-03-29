<?php $__env->startSection('title', __('Confirm Password')); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-page">
	<section class="auth-card" aria-label="<?php echo e(__('Confirm Password')); ?>">
		<h1 style="margin:0 0 6px;"><?php echo e(__('Confirm Password')); ?></h1>
		<p style="margin:0 0 16px; color: var(--c-on-surface-2);">
			<?php echo e(__('This is a secure area of the application. Please confirm your password before continuing.')); ?>

		</p>

		<?php if($errors->any()): ?>
			<div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid #e53935; border-radius:10px; background: color-mix(in srgb, #e53935 10%, var(--c-surface));">
				<ul style="margin:0; padding-left:18px;">
					<?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<li style="color: var(--c-on-surface);"><?php echo e($error); ?></li>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</ul>
			</div>
		<?php endif; ?>

		<form method="POST" action="<?php echo e(route('password.confirm')); ?>" style="display:grid; gap:12px;">
			<?php echo csrf_field(); ?>
			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);"><?php echo e(__('Password')); ?></span>
				<input type="password" name="password" required autocomplete="current-password" class="footer-email-input" />
			</label>

			<div class="auth-actions">
				<button type="submit" class="btn btn-primary"><?php echo e(__('Confirm')); ?></button>
				<a href="<?php echo e(route('dashboard')); ?>" class="btn btn-outline"><?php echo e(__('Cancel')); ?></a>
			</div>
		</form>
	</section>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/auth/confirm-password.blade.php ENDPATH**/ ?>