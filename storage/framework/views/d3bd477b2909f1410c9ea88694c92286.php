<?php $__env->startSection('title', 'Support Tickets'); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-header">
    <h1>Support Tickets</h1>
    <p>Manage and respond to user support requests</p>
</div>

<div class="admin-card">
    <form method="GET" action="<?php echo e(route('admin.tickets.index')); ?>" style="display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
        <input 
            type="text" 
            name="search" 
            placeholder="Search tickets..." 
            value="<?php echo e(request('search')); ?>"
            class="admin-input"
            style="flex: 1; min-width: 200px;"
        >
        <select name="status" class="admin-select" style="width: 180px;">
            <option value="">All Statuses</option>
            <option value="open" <?php echo e(request('status') === 'open' ? 'selected' : ''); ?>>Open</option>
            <option value="in_progress" <?php echo e(request('status') === 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
            <option value="resolved" <?php echo e(request('status') === 'resolved' ? 'selected' : ''); ?>>Resolved</option>
            <option value="closed" <?php echo e(request('status') === 'closed' ? 'selected' : ''); ?>>Closed</option>
        </select>
        <select name="priority" class="admin-select" style="width: 180px;">
            <option value="">All Priorities</option>
            <option value="urgent" <?php echo e(request('priority') === 'urgent' ? 'selected' : ''); ?>>Urgent</option>
            <option value="high" <?php echo e(request('priority') === 'high' ? 'selected' : ''); ?>>High</option>
            <option value="medium" <?php echo e(request('priority') === 'medium' ? 'selected' : ''); ?>>Medium</option>
            <option value="low" <?php echo e(request('priority') === 'low' ? 'selected' : ''); ?>>Low</option>
        </select>
        <button type="submit" class="admin-btn admin-btn-primary">Search</button>
        <?php if(request('search') || request('status') || request('priority')): ?>
            <a href="<?php echo e(route('admin.tickets.index')); ?>" class="admin-btn admin-btn-secondary">Clear</a>
        <?php endif; ?>
    </form>

    <div class="admin-stats-grid admin-stats-grid--flex" style="margin-bottom: 24px;">
        <div class="admin-stat-card">
            <div class="admin-stat-label">Total</div>
            <div class="admin-stat-value"><?php echo e($stats['total']); ?></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Open</div>
            <div class="admin-stat-value" style="color: var(--admin-primary);"><?php echo e($stats['open']); ?></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">In Progress</div>
            <div class="admin-stat-value" style="color: var(--admin-warning);"><?php echo e($stats['in_progress']); ?></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Resolved</div>
            <div class="admin-stat-value" style="color: var(--admin-success);"><?php echo e($stats['resolved']); ?></div>
        </div>
        <div class="admin-stat-card">
            <div class="admin-stat-label">Urgent</div>
            <div class="admin-stat-value" style="color: var(--admin-danger);"><?php echo e($stats['urgent']); ?></div>
        </div>
    </div>

    <?php if($tickets->count()): ?>
        <div style="overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned To</th>
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
                                <?php if($ticket->user): ?>
                                    <a href="<?php echo e(route('admin.users.show', $ticket->user)); ?>" style="color: var(--admin-primary); text-decoration: none;">
                                        <?php echo e($ticket->user->name); ?>

                                    </a>
                                <?php else: ?>
                                    <span style="color: var(--admin-text-muted);">Anonymous</span>
                                <?php endif; ?>
                            </td>
                            <td style="color: var(--admin-text-muted); font-size: 13px;">
                                <?php echo e($ticket->getErrorTypeLabel()); ?>

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
                            <td>
                                <?php if($ticket->assignedAdmin): ?>
                                    <?php echo e($ticket->assignedAdmin->name); ?>

                                <?php else: ?>
                                    <span style="color: var(--admin-text-muted);">Unassigned</span>
                                <?php endif; ?>
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

        <div style="margin-top: 24px;">
            <?php echo e($tickets->links()); ?>

        </div>
    <?php else: ?>
        <p style="color: var(--admin-text-muted); text-align: center; padding: 40px;">No tickets found.</p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/admin/tickets/index.blade.php ENDPATH**/ ?>