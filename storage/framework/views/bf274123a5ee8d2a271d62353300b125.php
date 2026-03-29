

<?php $__env->startSection('title', 'Admin Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-header">
    <h1><?php echo e(__('Dashboard')); ?></h1>
    <p><?php echo e(__('Overview of system statistics and recent activity')); ?></p>
</div>

<div class="admin-stats-grid">
    <div class="admin-stat-card">
        <div class="admin-stat-label"><?php echo e(__('Total Users')); ?></div>
        <div class="admin-stat-value"><?php echo e($totalUsers); ?></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label"><?php echo e(__('Total Tickets')); ?></div>
        <div class="admin-stat-value"><?php echo e($totalTickets); ?></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label"><?php echo e(__('Open Tickets')); ?></div>
        <div class="admin-stat-value" style="color: var(--admin-primary);"><?php echo e($openTickets); ?></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label"><?php echo e(__('In Progress')); ?></div>
        <div class="admin-stat-value" style="color: var(--admin-warning);"><?php echo e($inProgressTickets); ?></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label"><?php echo e(__('Urgent Tickets')); ?></div>
        <div class="admin-stat-value" style="color: var(--admin-danger);"><?php echo e($urgentTickets); ?></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label"><?php echo e(__('Unassigned')); ?></div>
        <div class="admin-stat-value" style="color: var(--admin-warning);"><?php echo e($unassignedTickets); ?></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
    <div class="admin-card">
        <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;"><?php echo e(__('Recent Users')); ?></h2>
        <?php if($latestUsers->count()): ?>
            <div style="display: grid; gap: 12px;">
                <?php $__currentLoopData = $latestUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: var(--admin-surface-light); border-radius: 8px;">
                        <div>
                            <a href="<?php echo e(route('admin.users.show', $user)); ?>" style="color: var(--admin-text); text-decoration: none; font-weight: 500;"><?php echo e($user->name); ?></a>
                            <p style="margin: 4px 0 0; font-size: 12px; color: var(--admin-text-muted);"><?php echo e($user->email); ?></p>
                        </div>
                        <span style="font-size: 12px; color: var(--admin-text-muted);"><?php echo e($user->created_at->diffForHumans()); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div style="margin-top: 16px;">
                <a href="<?php echo e(route('admin.users.index')); ?>" class="admin-btn admin-btn-secondary"><?php echo e(__('View All Users')); ?></a>
            </div>
        <?php else: ?>
            <p style="color: var(--admin-text-muted);"><?php echo e(__('No users yet.')); ?></p>
        <?php endif; ?>
    </div>

    <div class="admin-card">
        <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;"><?php echo e(__('Recent Tickets')); ?></h2>
        <?php if($latestTickets->count()): ?>
            <div style="display: grid; gap: 12px;">
                <?php $__currentLoopData = $latestTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="padding: 12px; background: var(--admin-surface-light); border-radius: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                            <a href="<?php echo e(route('admin.tickets.show', $ticket)); ?>" style="color: var(--admin-text); text-decoration: none; font-weight: 500; flex: 1;"><?php echo e($ticket->subject); ?></a>
                            <span class="admin-badge" style="background: <?php echo e($ticket->getStatusColor()); ?>20; color: <?php echo e($ticket->getStatusColor()); ?>;">
                                <?php echo e(ucfirst(str_replace('_', ' ', $ticket->status))); ?>

                            </span>
                        </div>
                        <p style="margin: 0; font-size: 12px; color: var(--admin-text-muted);">
                            <?php echo e($ticket->user->name ?? 'Anonymous'); ?> • <?php echo e($ticket->created_at->diffForHumans()); ?>

                        </p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div style="margin-top: 16px;">
                <a href="<?php echo e(route('admin.tickets.index')); ?>" class="admin-btn admin-btn-secondary"><?php echo e(__('View All Tickets')); ?></a>
            </div>
        <?php else: ?>
            <p style="color: var(--admin-text-muted);"><?php echo e(__('No tickets yet.')); ?></p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>