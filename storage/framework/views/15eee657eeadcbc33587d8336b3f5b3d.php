

<?php $__env->startSection('title', 'Ticket #' . $ticket->id); ?>

<?php $__env->startSection('dashboard_content'); ?>
<section class="auth-card" aria-label="Support Ticket">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap;">
        <div>
            <h1 style="margin:0;">Ticket #<?php echo e($ticket->id); ?></h1>
            <p style="margin:4px 0 0; color:var(--c-on-surface-2);"><?php echo e($ticket->subject); ?></p>
        </div>
        <a href="<?php echo e(route('tickets.index')); ?>" class="btn btn-outline">Back to My Tickets</a>
    </div>

    <?php if(session('success')): ?>
        <div role="status" style="margin-top:12px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div style="margin-top:24px; display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:16px;">
        <div class="auth-card">
            <h2 style="margin:0 0 16px; font-size:18px;">Ticket Information</h2>
            <div style="display:grid; gap:12px;">
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">Status</p>
                    <span style="padding:4px 12px; background:<?php echo e($ticket->getStatusColor()); ?>20; color:<?php echo e($ticket->getStatusColor()); ?>; border-radius:6px; font-size:12px; font-weight:600;">
                        <?php echo e(ucfirst(str_replace('_', ' ', $ticket->status))); ?>

                    </span>
                </div>
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">Error Type</p>
                    <p style="margin:0; font-weight:600;"><?php echo e($ticket->getErrorTypeLabel()); ?></p>
                </div>
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">Priority</p>
                    <span style="padding:4px 12px; background:<?php echo e($ticket->getPriorityColor()); ?>20; color:<?php echo e($ticket->getPriorityColor()); ?>; border-radius:6px; font-size:12px; font-weight:600;">
                        <?php echo e(ucfirst($ticket->priority)); ?>

                    </span>
                </div>
                <div>
                    <p style="margin:0 0 4px; font-size:13px; color:var(--c-on-surface-2);">Submitted</p>
                    <p style="margin:0; font-weight:600;"><?php echo e($ticket->created_at->format('F d, Y')); ?></p>
                    <p style="margin:4px 0 0; font-size:12px; color:var(--c-on-surface-2);"><?php echo e($ticket->created_at->diffForHumans()); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="auth-card" style="margin-top:24px;">
        <h2 style="margin:0 0 16px; font-size:18px;">Your Report</h2>
        <div style="padding:16px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-primary) 5%); border-radius:8px; white-space:pre-wrap; line-height:1.6;">
            <?php echo e($ticket->description); ?>

        </div>
    </div>

    <?php if($ticket->admin_response): ?>
        <div class="auth-card" style="margin-top:24px;">
            <h2 style="margin:0 0 16px; font-size:18px;">Admin Response</h2>
            <div style="padding:16px; background:color-mix(in srgb, var(--c-surface) 95%, var(--c-primary) 5%); border-left:3px solid var(--c-primary); border-radius:8px; white-space:pre-wrap; line-height:1.6;">
                <?php echo e($ticket->admin_response); ?>

            </div>
        </div>
    <?php else: ?>
        <div class="auth-card" style="margin-top:24px;">
            <p style="margin:0; color:var(--c-on-surface-2);">Your ticket is being reviewed. We will respond as soon as possible.</p>
        </div>
    <?php endif; ?>
</section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/tickets/show.blade.php ENDPATH**/ ?>