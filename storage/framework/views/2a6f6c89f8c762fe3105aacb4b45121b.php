<?php $__env->startSection('title', __('Two-Factor Authentication')); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-page">
	<section class="auth-card" aria-label="<?php echo e(__('Two-Factor Authentication')); ?>">
		<h1><?php echo e(__('Two-Factor Authentication')); ?></h1>
		<p><?php echo e(__('Please enter the 6-digit code from your authenticator app or use a recovery code.')); ?></p>

		<?php if($errors->any()): ?>
			<div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid #e53935; border-radius:10px; background: color-mix(in srgb, #e53935 10%, var(--c-surface));">
				<ul style="margin:0; padding-left:18px;">
					<?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<li style="color: var(--c-on-surface);"><?php echo e($error); ?></li>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</ul>
			</div>
		<?php endif; ?>

		<form method="POST" action="<?php echo e(route('two-factor.login')); ?>" style="display:grid; gap:12px;">
			<?php echo csrf_field(); ?>

			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);"><?php echo e(__('Authentication Code')); ?></span>
				<input 
					type="text" 
					name="code" 
					required 
					autofocus 
					autocomplete="one-time-code"
					inputmode="numeric"
					pattern="[0-9]*"
					class="footer-email-input" 
					placeholder="000000"
					maxlength="10"
					style="text-align:center; letter-spacing:4px; font-size:20px; font-weight:600;"
				/>
				<small style="color: var(--c-on-surface-2); font-size:12px;"><?php echo e(__('Enter the 6-digit code from your authenticator app, or a recovery code.')); ?></small>
			</label>

			<label style="display:flex; align-items:center; gap:8px; color: var(--c-on-surface-2);">
				<input type="checkbox" name="remember" style="width:16px; height:16px;" />
				<span><?php echo e(__('Remember me')); ?></span>
			</label>

			<div class="auth-actions">
				<button type="submit" class="btn btn-primary"><?php echo e(__('Verify')); ?></button>
				<a href="<?php echo e(route('login')); ?>" class="btn btn-outline"><?php echo e(__('Back to login')); ?></a>
			</div>
		</form>
	</section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/auth/two-factor-challenge.blade.php ENDPATH**/ ?>