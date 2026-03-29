<?php $__env->startSection('title', 'Edit User'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-header" style="display: flex; justify-content: space-between; align-items: start;">
    <div>
        <h1>Edit User</h1>
        <p><?php echo e($user->name); ?></p>
    </div>
    <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="admin-btn admin-btn-secondary">Back to User</a>
</div>

<?php if($errors->any()): ?>
    <div class="admin-alert admin-alert-error">
        <ul style="margin: 0; padding-left: 20px;">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<div class="admin-card">
    <form method="POST" action="<?php echo e(route('admin.users.update', $user)); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PATCH'); ?>

        <div style="display: grid; gap: 20px;">
            <div>
                <label for="name" style="display: block; margin-bottom: 6px; font-weight: 500;">Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="<?php echo e(old('name', $user->name)); ?>" 
                    required
                    class="admin-input"
                >
            </div>

            <div>
                <p style="margin: 0 0 6px; font-weight: 500;">Email</p>
                <div class="admin-input" style="margin: 0; background: color-mix(in srgb, var(--admin-surface-light) 85%, var(--admin-border)); cursor: default;">
                    <?php echo e($user->email); ?>

                </div>
                <p style="margin: 8px 0 0; font-size: 13px; color: var(--admin-text-muted);"><?php echo e(__('Administrators cannot change a user\'s email address.')); ?></p>
            </div>

            <div>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input 
                        type="checkbox" 
                        name="is_admin" 
                        value="1" 
                        <?php echo e(old('is_admin', $user->is_admin) ? 'checked' : ''); ?>

                        style="width: 18px; height: 18px; cursor: pointer;"
                    >
                    <span style="font-weight: 500;">Admin User</span>
                </label>
                <p style="margin: 8px 0 0; font-size: 13px; color: var(--admin-text-muted);">Grant admin privileges to this user</p>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 8px;">
                <button type="submit" class="admin-btn admin-btn-primary">Update User</button>
                <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="admin-btn admin-btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/admin/users/edit.blade.php ENDPATH**/ ?>