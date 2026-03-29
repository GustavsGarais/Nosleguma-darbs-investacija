<?php $__env->startSection('title', __('Simulations')); ?>

<?php $__env->startSection('dashboard_content'); ?>
<div class="simulations-page">
    <?php echo $__env->make('simulations.partials.welcome', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('simulations.partials.list', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php echo $__env->make('components.currency-script', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('components.tutorial', ['currentPage' => 'dashboard'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/simulations/index.blade.php ENDPATH**/ ?>