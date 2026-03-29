

<?php $__env->startSection('title', 'My Support Tickets'); ?>

<?php $__env->startSection('dashboard_content'); ?>
<section class="auth-card" aria-label="My Support Tickets">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <h1 style="margin:0;">My Support Tickets</h1>
        <a href="<?php echo e(route('tickets.create')); ?>" class="btn btn-primary">Report New Issue</a>
    </div>

    <?php if($tickets->count()): ?>
        <div style="overflow:auto; margin-top:16px;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">ID</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">Title</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">Type</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">Status</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">Submitted</th>
                        <th style="text-align:left; padding:12px 8px; border-bottom:2px solid var(--c-border);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr style="border-bottom:1px solid var(--c-border);">
                            <td style="padding:12px 8px;">#<?php echo e($ticket->id); ?></td>
                            <td style="padding:12px 8px;">
                                <a href="<?php echo e(route('tickets.show', $ticket)); ?>" style="font-weight:600; color:var(--c-on-surface); text-decoration:none;"><?php echo e(Str::limit($ticket->subject, 50)); ?></a>
                            </td>
                            <td style="padding:12px 8px; color:var(--c-on-surface-2);"><?php echo e($ticket->getErrorTypeLabel()); ?></td>
                            <td style="padding:12px 8px;">
                                <span style="padding:4px 12px; background:<?php echo e($ticket->getStatusColor()); ?>20; color:<?php echo e($ticket->getStatusColor()); ?>; border-radius:6px; font-size:12px; font-weight:600;">
                                    <?php echo e(ucfirst(str_replace('_', ' ', $ticket->status))); ?>

                                </span>
                            </td>
                            <td style="padding:12px 8px; color:var(--c-on-surface-2); font-size:13px;">
                                <?php echo e($ticket->created_at->format('M d, Y')); ?>

                            </td>
                            <td style="padding:12px 8px;">
                                <a href="<?php echo e(route('tickets.show', $ticket)); ?>" class="btn btn-secondary btn-sm">View</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:16px;">
            <?php echo e($tickets->links()); ?>

        </div>
    <?php else: ?>
        <div style="margin-top:24px; text-align:center; padding:40px;">
            <p style="margin:0 0 16px; color:var(--c-on-surface-2);">You haven't submitted any support tickets yet.</p>
            <a href="<?php echo e(route('tickets.create')); ?>" class="btn btn-primary">Report Your First Issue</a>
        </div>
    <?php endif; ?>
</section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/tickets/index.blade.php ENDPATH**/ ?>