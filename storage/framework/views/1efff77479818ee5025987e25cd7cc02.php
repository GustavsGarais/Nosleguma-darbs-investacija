

<?php $__env->startSection('title', 'User Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-header" style="display: flex; justify-content: space-between; align-items: start;">
    <div>
        <h1>User Details</h1>
        <p><?php echo e($user->name); ?></p>
    </div>
    <div style="display: flex; gap: 8px;">
        <a href="<?php echo e(route('admin.users.index')); ?>" class="admin-btn admin-btn-secondary">Back to Users</a>
        <a href="<?php echo e(route('admin.users.edit', $user)); ?>" class="admin-btn admin-btn-primary">Edit User</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 24px;">
    <div class="admin-card">
        <h2 style="margin: 0 0 16px; font-size: 18px; font-weight: 600;">User Information</h2>
        <div style="display: grid; gap: 12px;">
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Name</p>
                <p style="margin: 0; font-weight: 600;"><?php echo e($user->name); ?></p>
            </div>
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Email</p>
                <p style="margin: 0; font-weight: 600;"><?php echo e($user->email); ?></p>
            </div>
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Role</p>
                <p style="margin: 0;">
                    <?php if($user->is_admin): ?>
                        <span class="admin-badge" style="background: var(--admin-primary)20; color: var(--admin-primary);">Admin</span>
                    <?php else: ?>
                        <span class="admin-badge" style="background: var(--admin-border); color: var(--admin-text-muted);">User</span>
                    <?php endif; ?>
                </p>
            </div>
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Member Since</p>
                <p style="margin: 0; font-weight: 600;"><?php echo e($user->created_at->format('F d, Y')); ?></p>
                <p style="margin: 4px 0 0; font-size: 12px; color: var(--admin-text-muted);"><?php echo e($user->created_at->diffForHumans()); ?></p>
            </div>
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Tutorial Completed</p>
                <p style="margin: 0;">
                    <?php if($user->tutorial_completed): ?>
                        <span style="color: var(--admin-success); font-weight: 600;">Yes</span>
                    <?php else: ?>
                        <span style="color: var(--admin-text-muted);">No</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h2 style="margin: 0 0 16px; font-size: 18px; font-weight: 600;">Two-Factor Authentication</h2>
        <div style="display: grid; gap: 12px;">
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Status</p>
                <?php if($user->hasTwoFactorEnabled()): ?>
                    <span class="admin-badge" style="background: #10b98120; color: #10b981;">Enabled</span>
                <?php else: ?>
                    <span class="admin-badge" style="background: var(--admin-border); color: var(--admin-text-muted);">Disabled</span>
                <?php endif; ?>
            </div>

            <?php if($user->hasTwoFactorEnabled() && $user->id !== auth()->id()): ?>
                <div>
                    <p style="margin: 0 0 8px; font-size: 13px; color: var(--admin-text-muted); line-height:1.5;">
                        Disable 2FA clears the user's 2FA secret so they can log in again.
                    </p>
                    <form method="POST" action="<?php echo e(route('admin.users.disableTwoFactor', $user)); ?>" onsubmit="return confirm('Disable 2FA for this user?');">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="admin-btn admin-btn-danger" style="width:100%;">Disable 2FA</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="admin-card">
        <h2 style="margin: 0 0 16px; font-size: 18px; font-weight: 600;">Statistics</h2>
        <div style="display: grid; gap: 12px;">
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Total Simulations</p>
                <p style="margin: 0; font-size: 24px; font-weight: 700;"><?php echo e($user->simulations->count()); ?></p>
            </div>
            <div>
                <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Support Tickets</p>
                <p style="margin: 0; font-size: 24px; font-weight: 700;"><?php echo e($tickets->total()); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="admin-card">
    <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">User Support Tickets</h2>
    
    <?php if($tickets->count()): ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>#<?php echo e($ticket->id); ?></td>
                            <td>
                                <a href="<?php echo e(route('admin.tickets.show', $ticket)); ?>" style="color: var(--admin-text); text-decoration: none; font-weight: 500;">
                                    <?php echo e(Str::limit($ticket->subject, 50)); ?>

                                </a>
                            </td>
                            <td>
                                <span class="admin-badge" style="background: <?php echo e($ticket->getPriorityColor()); ?>20; color: <?php echo e($ticket->getPriorityColor()); ?>;">
                                    <?php echo e(ucfirst($ticket->priority)); ?>

                                </span>
                            </td>
                            <td>
                                <span class="admin-badge" style="background: <?php echo e($ticket->getStatusColor()); ?>20; color: <?php echo e($ticket->getStatusColor()); ?>;">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $ticket->status))); ?>

                                </span>
                            </td>
                            <td style="color: var(--admin-text-muted); font-size: 13px;">
                                <?php echo e($ticket->created_at->format('M d, Y')); ?>

                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.tickets.show', $ticket)); ?>" class="admin-btn admin-btn-secondary" style="padding: 6px 12px; font-size: 13px;">View</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 16px;">
            <?php echo e($tickets->links()); ?>

        </div>
    <?php else: ?>
        <p style="margin: 0; color: var(--admin-text-muted);">This user has no support tickets yet.</p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/admin/users/show.blade.php ENDPATH**/ ?>