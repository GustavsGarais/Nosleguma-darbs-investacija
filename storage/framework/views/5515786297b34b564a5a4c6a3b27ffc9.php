

<?php $__env->startSection('title', __('Register')); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-page">
	<section id="register-section" class="auth-card" aria-label="Register">
		<h1><?php echo e(__('Create your account')); ?></h1>
		<p><?php echo e(__('Join and start simulating smarter strategies')); ?></p>

		<?php if($errors->any()): ?>
			<div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
				<ul style="margin:0; padding-left:18px;">
					<?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<li style="color: var(--c-on-surface);"><?php echo e($error); ?></li>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</ul>
			</div>
		<?php endif; ?>

		<form method="POST" action="<?php echo e(route('register')); ?>" style="display:grid; gap:12px;">
			<?php echo csrf_field(); ?>

			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);"><?php echo e(__('Name')); ?></span>
				<input type="text" name="name" value="<?php echo e(old('name')); ?>" required autocomplete="name" class="footer-email-input" />
			</label>

			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);"><?php echo e(__('Email')); ?></span>
				<input type="email" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="username" class="footer-email-input" />
			</label>

			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);"><?php echo e(__('Password')); ?></span>
				<input type="password" name="password" required autocomplete="new-password" class="footer-email-input" />
				<small style="color:var(--c-on-surface-2); font-size:12px; margin-top:4px;">
					<?php echo e(__('Requirements: Minimum 12 characters, at least one uppercase letter, one lowercase letter, and one number or symbol (!@#$%^&*)')); ?>

				</small>
			</label>

			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);"><?php echo e(__('Confirm password')); ?></span>
				<input type="password" name="password_confirmation" required autocomplete="new-password" class="footer-email-input" />
			</label>

			<div class="auth-actions">
				<button type="submit" class="btn btn-primary"><?php echo e(__('Create Account')); ?></button>
				<a href="<?php echo e(route('login')); ?>" class="btn btn-outline"><?php echo e(__('Already have an account?')); ?></a>
			</div>
		</form>
	</section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/auth/register.blade.php ENDPATH**/ ?>