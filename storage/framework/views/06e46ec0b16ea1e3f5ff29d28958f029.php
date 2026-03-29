<section class="auth-card" aria-label="Simulations" style="margin-top:24px;">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
        <h2 style="margin:0;"><?php echo e(__('Your Simulations')); ?></h2>
        <a href="<?php echo e(route('simulations.create')); ?>" class="btn btn-primary btn-lg"><?php echo e(__('New Simulation')); ?></a>
    </div>

    <?php if(session('success')): ?>
        <div role="status" style="margin-top:12px; padding:10px 12px; border:1px solid var(--c-border); border-radius:10px; background: color-mix(in srgb, var(--c-surface) 92%, var(--c-primary) 8%);">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if($simulations->count()): ?>
        <div style="overflow:auto; margin-top:16px;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);"><?php echo e(__('Name')); ?></th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);"><?php echo e(__('Latest Value')); ?></th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);"><?php echo e(__('Last Updated')); ?></th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);"><?php echo e(__('Created')); ?></th>
                        <th style="text-align:left; padding:8px; border-bottom:1px solid var(--c-border);"><?php echo e(__('Actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $simulations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $snapshot = $sim->data['snapshot'] ?? null;
                            $lastValue = $snapshot['value'] ?? ($sim->settings['initialInvestment'] ?? 0);
                            $capturedAt = $snapshot['captured_at'] ?? null;
                            $updatedText = $capturedAt
                                ? \Illuminate\Support\Carbon::parse($capturedAt)->diffForHumans()
                                : 'Not saved yet';
                        ?>
                        <tr>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border);">
                                <a href="<?php echo e(route('simulations.show', $sim)); ?>" class="sim-name-link"><?php echo e($sim->name); ?></a>
                            </td>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border);">
                                <span class="currency-value" data-currency-value="<?php echo e($lastValue); ?>"><?php echo e('€'.number_format($lastValue, 2)); ?></span>
                            </td>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border);"><?php echo e($updatedText); ?></td>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border);"><?php echo e($sim->created_at->diffForHumans()); ?></td>
                            <td style="padding:8px; border-bottom:1px solid var(--c-border); display:flex; gap:8px;">
                                <a class="btn btn-primary btn-sm" href="<?php echo e(route('simulations.edit', $sim)); ?>"><?php echo e(__('Edit')); ?></a>
                                <form method="POST" action="<?php echo e(route('simulations.destroy', $sim)); ?>" onsubmit="return confirm('<?php echo e(__('Delete this simulation?')); ?>');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-outline btn-sm"><?php echo e(__('Delete')); ?></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top:12px;">
            <?php echo e($simulations->links()); ?>

        </div>
    <?php else: ?>
        <p style="margin-top:16px;"><?php echo e(__('No simulations yet.')); ?> <a href="<?php echo e(route('simulations.create')); ?>" class="sim-name-link"><?php echo e(__('Create your first simulation')); ?></a>.</p>
    <?php endif; ?>
</section>
<?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/simulations/partials/list.blade.php ENDPATH**/ ?>