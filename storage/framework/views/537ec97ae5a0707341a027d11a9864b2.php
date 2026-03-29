<?php $__env->startSection('title', __('Two-Factor Authentication')); ?>

<?php $__env->startSection('dashboard_content'); ?>
<section class="auth-card" aria-label="<?php echo e(__('Two-Factor Authentication Setup')); ?>" style="padding:32px; display:flex; flex-direction:column; gap:24px;">
	<header style="display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:12px;">
		<div>
			<h1 style="margin:0;"><?php echo e(__('Two-Factor Authentication')); ?></h1>
			<p style="margin:6px 0 0; color:var(--c-on-surface-2);"><?php echo e(__('Add an extra layer of security to your account.')); ?></p>
		</div>
		<a class="btn btn-outline" href="<?php echo e(route('settings')); ?>">← <?php echo e(__('Back')); ?></a>
	</header>

	<?php if($user->hasTwoFactorEnabled()): ?>
		<div role="status" style="padding:12px 16px; border-radius:10px; background:color-mix(in srgb, var(--c-primary) 18%, var(--c-surface)); border:1px solid color-mix(in srgb, var(--c-primary) 35%, var(--c-border));">
			<strong><?php echo e(__('Two-factor authentication is enabled.')); ?></strong>
		</div>

		<article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; display:flex; flex-direction:column; gap:16px;">
			<h2 style="margin:0;"><?php echo e(__('Recovery Codes')); ?></h2>
			<p style="margin:0; color:var(--c-on-surface-2); font-size:14px;">
				<?php echo e(__('Save these recovery codes in a safe place. You can use them to access your account if you lose access to your authenticator device.')); ?>

			</p>
			
			<?php if(session('recoveryCodes')): ?>
				<div style="background:var(--c-surface); border:1px solid var(--c-border); border-radius:10px; padding:16px; display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:8px;">
					<?php $__currentLoopData = session('recoveryCodes'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<code style="padding:8px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-primary) 5%); border-radius:6px; font-family:monospace; font-size:13px;"><?php echo e($code); ?></code>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</div>
			<?php else: ?>
				<div style="background:var(--c-surface); border:1px solid var(--c-border); border-radius:10px; padding:16px; display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:8px;">
					<?php $__currentLoopData = $recoveryCodes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<code style="padding:8px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-primary) 5%); border-radius:6px; font-family:monospace; font-size:13px;"><?php echo e($code); ?></code>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</div>
			<?php endif; ?>

			<form method="POST" action="<?php echo e(route('two-factor.recovery-codes')); ?>" style="margin-top:8px;">
				<?php echo csrf_field(); ?>
				<button type="submit" class="btn btn-outline"><?php echo e(__('Regenerate Recovery Codes')); ?></button>
			</form>
		</article>

		<article style="border:1px solid #e53935; border-radius:16px; padding:24px; background:color-mix(in srgb, #e53935 10%, var(--c-surface)); display:flex; flex-direction:column; gap:16px;">
			<h2 style="margin:0; color:#e53935;"><?php echo e(__('Disable Two-Factor Authentication')); ?></h2>
			<p style="margin:0; color:var(--c-on-surface-2); font-size:14px;">
				<?php echo e(__('Once disabled, you will only need your password to sign in.')); ?>

			</p>
			<form method="POST" action="<?php echo e(route('two-factor.disable')); ?>" style="display:flex; flex-direction:column; gap:12px;">
				<?php echo csrf_field(); ?>
				<label style="display:flex; flex-direction:column; gap:6px;">
					<span style="font-weight:600;"><?php echo e(__('Confirm Password')); ?></span>
					<input type="password" name="password" class="footer-email-input" required />
				</label>
				<button type="submit" class="btn btn-outline" style="color:#e53935; border-color:#e53935; align-self:flex-start;"><?php echo e(__('Disable 2FA')); ?></button>
			</form>
		</article>
	<?php else: ?>
		<article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; display:flex; flex-direction:column; gap:16px;">
			<h2 style="margin:0;"><?php echo e(__('Step 1: Scan QR Code')); ?></h2>
			<p style="margin:0; color:var(--c-on-surface-2); font-size:14px;">
				<?php echo e(__('Scan this QR code with your authenticator app (Google Authenticator, Authy, Microsoft Authenticator, etc.).')); ?>

			</p>
			
			<div style="display:flex; justify-content:center; padding:20px; background:white; border-radius:12px; border:1px solid var(--c-border);">
				<?php
				?>
				<?php if(is_string($qrCode) && str_starts_with(trim($qrCode), 'data:')): ?>
					<img src="<?php echo e($qrCode); ?>" alt="<?php echo e(__('QR Code')); ?>" style="max-width:100%; height:auto;" />
				<?php else: ?>
					<?php echo $qrCode; ?>

				<?php endif; ?>
			</div>

			<div style="padding:12px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-primary) 5%); border-radius:8px;">
				<p style="margin:0 0 8px; font-weight:600; font-size:13px;"><?php echo e(__('Manual Entry Key')); ?></p>
				<code style="font-family:monospace; font-size:14px; word-break:break-all;"><?php echo e($secret); ?></code>
			</div>
		</article>

		<article style="border:1px solid var(--c-border); border-radius:16px; padding:24px; display:flex; flex-direction:column; gap:16px;">
			<h2 style="margin:0;"><?php echo e(__('Step 2: Verify Code')); ?></h2>
			<p style="margin:0; color:var(--c-on-surface-2); font-size:14px;">
				<?php echo e(__('Enter the 6-digit code from your authenticator app to enable two-factor authentication.')); ?>

			</p>
			
			<form method="POST" action="<?php echo e(route('two-factor.enable')); ?>" style="display:flex; flex-direction:column; gap:12px;">
				<?php echo csrf_field(); ?>
				<label style="display:flex; flex-direction:column; gap:6px;">
					<span style="font-weight:600;"><?php echo e(__('Authentication Code')); ?></span>
					<input 
						type="text" 
						name="code" 
						required 
						autofocus
						class="footer-email-input" 
						placeholder="000000"
						maxlength="6"
						style="text-align:center; letter-spacing:4px; font-size:20px; font-weight:600;"
					/>
					<?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
						<span style="color:#e53935; font-size:12px;"><?php echo e($message); ?></span>
					<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
				</label>
				<button type="submit" class="btn btn-primary" style="align-self:flex-start;"><?php echo e(__('Enable 2FA')); ?></button>
			</form>
		</article>
	<?php endif; ?>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/settings/two-factor.blade.php ENDPATH**/ ?>