

<?php $__env->startSection('title', 'Users Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-header">
    <h1>Users Management</h1>
    <p>Manage user accounts and permissions</p>
</div>

<div class="admin-card">
    <form method="GET" action="<?php echo e(route('admin.users.index')); ?>" style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
        <input 
            type="text" 
            name="search" 
            placeholder="Search by name or email..." 
            value="<?php echo e(request('search')); ?>"
            class="admin-input"
            style="flex: 1; min-width: 200px;"
        >
        <select name="filter" class="admin-select" style="width: 180px;">
            <option value="">All Users</option>
            <option value="admins" <?php echo e(request('filter') === 'admins' ? 'selected' : ''); ?>>Admins Only</option>
            <option value="users" <?php echo e(request('filter') === 'users' ? 'selected' : ''); ?>>Regular Users</option>
        </select>
        <button type="submit" class="admin-btn admin-btn-primary">Search</button>
        <?php if(request('search') || request('filter')): ?>
            <a href="<?php echo e(route('admin.users.index')); ?>" class="admin-btn admin-btn-secondary">Clear</a>
        <?php endif; ?>
    </form>

    <?php if($users->count()): ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Simulations</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <a href="<?php echo e(route('admin.users.show', $user)); ?>" style="color: var(--admin-text); text-decoration: none; font-weight: 500;"><?php echo e($user->name); ?></a>
                            </td>
                            <td style="color: var(--admin-text-muted);"><?php echo e($user->email); ?></td>
                            <td>
                                <?php if($user->is_admin): ?>
                                    <span class="admin-badge" style="background: var(--admin-primary)20; color: var(--admin-primary);">Admin</span>
                                <?php else: ?>
                                    <span class="admin-badge" style="background: var(--admin-border); color: var(--admin-text-muted);">User</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: var(--admin-text-muted);"><?php echo e($user->simulations_count); ?></td>
                            <td style="color: var(--admin-text-muted); font-size: 13px;">
                                <?php echo e($user->created_at->format('M d, Y')); ?>

                            </td>
                            <td>
                                <div style="display: flex; gap: 8px;">
                                    <a href="<?php echo e(route('admin.users.show', $user)); ?>" class="admin-btn admin-btn-secondary" style="padding: 6px 12px; font-size: 13px;">View</a>
                                    <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="admin-btn admin-btn-secondary" style="padding: 6px 12px; font-size: 13px;">Edit</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 24px;">
            <?php echo e($users->links()); ?>

        </div>
    <?php else: ?>
        <p style="color: var(--admin-text-muted); text-align: center; padding: 40px;">No users found.</p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/admin/users/index.blade.php ENDPATH**/ ?>