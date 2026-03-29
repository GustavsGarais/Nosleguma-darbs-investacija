<div style="font-family: Inter, system-ui, -apple-system, Segoe UI, Arial, sans-serif; line-height: 1.6;">
    <p style="margin:0 0 12px;">
        Hello <?php echo e($userName); ?>,
    </p>

    <p style="margin:0 0 12px;">
        Your account recovery request has been reviewed, and <b>two-factor authentication (2FA) has been disabled</b> for your account.
    </p>

    <p style="margin:0 0 12px;">
        Support Ticket ID: <b><?php echo e($ticketId); ?></b>
    </p>

    <p style="margin:0 0 12px;">
        You can now log in normally. If you encounter any issues, you can request a new password reset from the login page.
    </p>

    <p style="margin:0; color:#6b7280;">
        This message was sent automatically by <?php echo e(config('app.name')); ?>.
    </p>
</div>

<?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/emails/two_factor_disabled.blade.php ENDPATH**/ ?>