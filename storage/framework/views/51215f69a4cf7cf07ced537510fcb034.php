<?php $__env->startSection('content'); ?>
<div class="dashboard-shell">
    <aside class="dashboard-sidebar auth-card" aria-label="<?php echo e(__('Sidebar navigation')); ?>">
        <nav aria-label="Main">
            <ul style="list-style:none; margin:0; padding:0; display:grid; gap:8px;">
                <li>
                    <a href="<?php echo e(route('simulations.index')); ?>" class="btn btn-primary dashboard-nav-link <?php if(request()->routeIs('simulations.*')): ?> is-current <?php endif; ?>" style="width:100%; text-align:left;" <?php if(request()->routeIs('simulations.*')): ?> aria-current="page" <?php endif; ?>><?php echo e(__('Simulations')); ?></a>
                </li>
                <li>
                    <a href="<?php echo e(url('/reports')); ?>" class="btn btn-primary dashboard-nav-link <?php if(request()->routeIs('tickets.*') || request()->is('reports')): ?> is-current <?php endif; ?>" style="width:100%; text-align:left;" <?php if(request()->routeIs('tickets.*') || request()->is('reports')): ?> aria-current="page" <?php endif; ?>><?php echo e(__('Reports')); ?></a>
                </li>
                <li>
                    <a href="<?php echo e(route('settings')); ?>" class="btn btn-primary dashboard-nav-link <?php if(request()->routeIs('settings*')): ?> is-current <?php endif; ?>" style="width:100%; text-align:left;" <?php if(request()->routeIs('settings*')): ?> aria-current="page" <?php endif; ?>><?php echo e(__('Account')); ?></a>
                </li>
                <?php if(auth()->check() && auth()->user()->isAdmin()): ?>
                    <li style="border-top:1px solid var(--c-border); padding-top:8px; margin-top:8px;">
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-secondary dashboard-nav-link <?php if(request()->is('admin*')): ?> is-current <?php endif; ?>" style="width:100%; text-align:left;" <?php if(request()->is('admin*')): ?> aria-current="page" <?php endif; ?>>
                            <span style="display:flex; align-items:center; gap:8px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                    <path d="M2 17l10 5 10-5"></path>
                                    <path d="M2 12l10 5 10-5"></path>
                                </svg>
                                <?php echo e(__('Admin Panel')); ?>

                            </span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </aside>

    <section class="dashboard-content">
        <?php echo $__env->yieldContent('dashboard_content'); ?>
    </section>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/layouts/dashboard.blade.php ENDPATH**/ ?>