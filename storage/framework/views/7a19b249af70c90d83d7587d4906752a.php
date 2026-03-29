

<?php $__env->startSection('title', __('Log In')); ?>

<?php $__env->startSection('content'); ?>
<div class="auth-page">
	<section id="login-section" class="auth-card" aria-label="Login">
		<h1><?php echo e(__('Welcome back')); ?></h1>
		<p><?php echo e(__('Sign in to continue your simulations')); ?></p>

		<?php if($errors->any()): ?>
			<div role="alert" aria-live="polite" style="margin:12px 0; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
				<ul style="margin:0; padding-left:18px;">
					<?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<li style="color: var(--c-on-surface);"><?php echo e($error); ?></li>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</ul>
			</div>
		<?php endif; ?>

		<form method="POST" action="<?php echo e(route('login')); ?>" style="display:grid; gap:12px;">
			<?php echo csrf_field(); ?>

			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);"><?php echo e(__('Email')); ?></span>
				<input type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="username" class="footer-email-input" />
			</label>

			<label style="display:grid; gap:6px;">
				<span style="font-weight:700; color: var(--c-on-surface);"><?php echo e(__('Password')); ?></span>
				<input type="password" name="password" required autocomplete="current-password" class="footer-email-input" />
			</label>

			<label style="display:flex; align-items:center; gap:8px; color: var(--c-on-surface-2);">
				<input type="checkbox" name="remember" style="width:16px; height:16px;" />
				<span><?php echo e(__('Remember me')); ?></span>
			</label>

			<div class="auth-actions">
				<button type="submit" class="btn btn-primary"><?php echo e(__('Sign In')); ?></button>
				<a href="<?php echo e(route('password.request')); ?>" class="btn btn-outline"><?php echo e(__('Forgot password?')); ?></a>
			</div>

			<div style="margin-top:6px; color: var(--c-on-surface-2);">
				<?php echo e(__('New here?')); ?>

				<a href="<?php echo e(route('register')); ?>" class="btn btn-link" style="padding:0;"><?php echo e(__('Create an account')); ?></a>
			</div>
		</form>
	</section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/auth/login.blade.php ENDPATH**/ ?>