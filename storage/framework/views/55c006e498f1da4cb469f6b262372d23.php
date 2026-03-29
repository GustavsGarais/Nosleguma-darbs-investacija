<?php $__env->startSection('title', 'Account Recovery'); ?>

<?php $__env->startSection('content'); ?>
    <section class="auth-card" aria-label="Account Recovery">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
            <h1 style="margin:0;">Account Recovery</h1>
            <a href="<?php echo e(url('/')); ?>" class="btn btn-outline">Back</a>
        </div>

        <p style="margin:12px 0 0; color:var(--c-on-surface-2); font-size:14px; line-height:1.6;">
            If you lost access to your authenticator (2FA), you can request help here. Add a description so the admin can verify your request, and include the email you used for your account.
        </p>

        <?php if($errors->any()): ?>
            <div role="alert" style="margin-top:12px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, #ef4444 8%);">
                <ul style="margin:0; padding-left:20px;">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('support.store')); ?>" style="margin-top:20px;">
            <?php echo csrf_field(); ?>

            <div style="display:grid; gap:16px;">
                <div>
                    <label for="contact_email" style="display:block; margin-bottom:6px; font-weight:600;">Email for account</label>
                    <input
                        id="contact_email"
                        name="contact_email"
                        type="email"
                        required
                        maxlength="255"
                        value="<?php echo e(old('contact_email')); ?>"
                        placeholder="Enter your account email"
                        style="width:100%; padding:10px 12px; border:1px solid var(--c-border); border-radius:8px; background:var(--c-surface); color:var(--c-on-surface);"
                    />
                </div>

                <div>
                    <label for="description" style="display:block; margin-bottom:6px; font-weight:600;">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="10"
                        required
                        maxlength="400"
                        placeholder="Explain that you lost 2FA access and what happened. Include any helpful details."
                        style="width:100%; padding:10px 12px; border:1px solid var(--c-border); border-radius:8px; background:var(--c-surface); color:var(--c-on-surface); font-family:inherit; resize:vertical;"
                    ><?php echo e(old('description')); ?></textarea>
                    <p style="margin:8px 0 0; font-size:13px; color:var(--c-on-surface-2);">Maximum 400 words (admin preview readability).</p>
                </div>

                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    <button type="submit" class="btn btn-primary" style="flex:1; justify-content:center; min-width:220px;">
                        Submit request
                    </button>
                    <a href="<?php echo e(url('/')); ?>" class="btn btn-outline" style="min-width:200px; justify-content:center; display:inline-flex; align-items:center;">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/support/two-factor-recovery.blade.php ENDPATH**/ ?>