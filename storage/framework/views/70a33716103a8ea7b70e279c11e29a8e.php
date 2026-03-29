<section aria-label="Welcome" class="auth-card" style="margin-top:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
        <div>
            <h1 style="margin:0 0 8px;"><?php echo e(__('Welcome :name!', ['name' => auth()->user()->name])); ?></h1>
            <p style="margin:0; color: var(--c-on-surface);"><?php echo e(__("You're signed in. Your data is loaded from the database.")); ?></p>
        </div>
        <button id="start-tutorial" class="btn btn-secondary" type="button">📚 <?php echo e(__('Start Tutorial')); ?></button>
    </div>
</section>
<?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/simulations/partials/welcome.blade.php ENDPATH**/ ?>