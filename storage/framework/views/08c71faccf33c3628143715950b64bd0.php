<?php $__env->startSection('title', 'Ticket #' . $ticket->id); ?>

<?php $__env->startSection('content'); ?>
<div class="admin-header admin-header-row">
    <div>
        <h1>Ticket #<?php echo e($ticket->id); ?></h1>
        <p><?php echo e($ticket->subject); ?></p>
    </div>
    <a href="<?php echo e(route('admin.tickets.index')); ?>" class="admin-btn admin-btn-secondary" style="flex-shrink: 0;">Back to Tickets</a>
</div>

<div class="admin-grid-2">
    <div>
        <div class="admin-card">
            <h2 style="margin: 0 0 16px; font-size: 18px; font-weight: 600;">Ticket Details</h2>
            <div style="margin-bottom: 20px;">
                <p style="margin: 0 0 8px; font-size: 13px; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Subject</p>
                <p class="admin-prose" style="margin: 0; font-size: 16px; font-weight: 500;"><?php echo e($ticket->subject); ?></p>
            </div>
            <div style="margin-bottom: 20px;">
                <p style="margin: 0 0 8px; font-size: 13px; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Description</p>
                <div class="admin-prose" style="padding: 16px; background: var(--admin-surface-light); border-radius: 8px; white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word; line-height: 1.6;">
                    <?php echo e($ticket->description); ?>

                </div>
            </div>
            <?php if($ticket->admin_response): ?>
                <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--admin-border);">
                    <p style="margin: 0 0 8px; font-size: 13px; color: var(--admin-text-muted); text-transform: uppercase; letter-spacing: 0.5px;">Admin Response</p>
                    <div class="admin-prose" style="padding: 16px; background: rgba(59, 130, 246, 0.1); border-left: 3px solid var(--admin-primary); border-radius: 8px; white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word; line-height: 1.6;">
                        <?php echo e($ticket->admin_response); ?>

                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div>
        <div class="admin-card">
            <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">Update Ticket</h2>
            <form method="POST" action="<?php echo e(route('admin.tickets.updateStatus', $ticket)); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Status</label>
                    <select name="status" class="admin-select" required>
                        <option value="open" <?php echo e($ticket->status === 'open' ? 'selected' : ''); ?>>Open</option>
                        <option value="in_progress" <?php echo e($ticket->status === 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
                        <option value="resolved" <?php echo e($ticket->status === 'resolved' ? 'selected' : ''); ?>>Resolved</option>
                        <option value="closed" <?php echo e($ticket->status === 'closed' ? 'selected' : ''); ?>>Closed</option>
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Priority</label>
                    <select name="priority" class="admin-select" required>
                        <option value="low" <?php echo e($ticket->priority === 'low' ? 'selected' : ''); ?>>Low</option>
                        <option value="medium" <?php echo e($ticket->priority === 'medium' ? 'selected' : ''); ?>>Medium</option>
                        <option value="high" <?php echo e($ticket->priority === 'high' ? 'selected' : ''); ?>>High</option>
                        <option value="urgent" <?php echo e($ticket->priority === 'urgent' ? 'selected' : ''); ?>>Urgent</option>
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Assign To</label>
                    <select name="assigned_to" class="admin-select">
                        <option value="">Unassigned</option>
                        <?php $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($admin->id); ?>" <?php echo e($ticket->assigned_to === $admin->id ? 'selected' : ''); ?>>
                                <?php echo e($admin->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500;">Admin Response</label>
                    <textarea name="admin_response" rows="6" class="admin-textarea" placeholder="Add your response here..."><?php echo e(old('admin_response', $ticket->admin_response)); ?></textarea>
                </div>

                <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">Update Ticket</button>
            </form>
        </div>

        <div class="admin-card" style="margin-top: 24px;">
            <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">Ticket Information</h2>
            <div style="display: grid; gap: 12px;">
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">User</p>
                    <?php if($ticket->user): ?>
                        <a href="<?php echo e(route('admin.users.show', $ticket->user)); ?>" style="color: var(--admin-primary); text-decoration: none; font-weight: 500;">
                            <?php echo e($ticket->user->name); ?>

                        </a>
                        <p style="margin: 4px 0 0; font-size: 12px; color: var(--admin-text-muted);"><?php echo e($ticket->user->email); ?></p>
                    <?php else: ?>
                        <span style="color: var(--admin-text-muted);">Anonymous</span>
                        <?php if($ticket->contact_email): ?>
                            <p style="margin: 6px 0 0; font-size: 12px; color: var(--admin-text-muted);"><?php echo e($ticket->contact_email); ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Error Type</p>
                    <p style="margin: 0; font-weight: 500;"><?php echo e($ticket->getErrorTypeLabel()); ?></p>
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Status</p>
                    <span class="admin-badge" style="background: <?php echo e($ticket->getStatusColor()); ?>20; color: <?php echo e($ticket->getStatusColor()); ?>;">
                        <?php echo e(ucfirst(str_replace('_', ' ', $ticket->status))); ?>

                    </span>
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Priority</p>
                    <span class="admin-badge" style="background: <?php echo e($ticket->getPriorityColor()); ?>20; color: <?php echo e($ticket->getPriorityColor()); ?>;">
                        <?php echo e(ucfirst($ticket->priority)); ?>

                    </span>
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Assigned To</p>
                    <?php if($ticket->assignedAdmin): ?>
                        <span style="font-weight: 500;"><?php echo e($ticket->assignedAdmin->name); ?></span>
                    <?php else: ?>
                        <span style="color: var(--admin-text-muted);">Unassigned</span>
                    <?php endif; ?>
                </div>
                <div>
                    <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Created</p>
                    <span style="font-size: 13px;"><?php echo e($ticket->created_at->format('M d, Y H:i')); ?></span>
                </div>
                <?php if($ticket->resolved_at): ?>
                    <div>
                        <p style="margin: 0 0 4px; font-size: 13px; color: var(--admin-text-muted);">Resolved</p>
                        <span style="font-size: 13px;"><?php echo e($ticket->resolved_at->format('M d, Y H:i')); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <?php
                $isTwoFactorRecovery = $ticket->subject === 'Lost 2FA / Account Recovery';
            ?>
            <?php if($isTwoFactorRecovery): ?>
                <div class="admin-card" style="margin-top: 24px;">
                    <h2 style="margin: 0 0 20px; font-size: 18px; font-weight: 600;">Account Recovery (2FA)</h2>

                    <p style="margin: 0 0 14px; color: var(--admin-text-muted); line-height: 1.6;">
                        Disable 2FA for the user related to this ticket and notify them by email.
                    </p>

                    <form method="POST" action="<?php echo e(route('admin.tickets.disableTwoFactor', $ticket)); ?>"
                          onsubmit="return confirm('Disable 2FA for this account and send the notification email?');">
                        <?php echo csrf_field(); ?>
                        <textarea name="admin_response" rows="3" class="admin-textarea" placeholder="Optional admin note..." style="width:100%; margin-bottom: 12px;"></textarea>
                        <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%;">Disable 2FA & Notify</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/admin/tickets/show.blade.php ENDPATH**/ ?>