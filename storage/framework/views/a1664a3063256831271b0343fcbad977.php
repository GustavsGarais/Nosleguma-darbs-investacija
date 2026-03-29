<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <title><?php echo $__env->yieldContent('title', __('Investment Simulation')); ?> | <?php echo e(config('app.name')); ?></title>
    <meta name="application-name" content="<?php echo e(config('app.name')); ?>" />
    <meta property="og:title" content="<?php echo e(config('app.name')); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="utf-8" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="theme-color" content="#07a05a" />
    <link rel="icon" href="<?php echo e(asset('favicon.svg')); ?>" type="image/svg+xml" />
    <link rel="icon" href="<?php echo e(asset('favicon.ico')); ?>" sizes="any" />
    <link rel="manifest" href="<?php echo e(asset('site.webmanifest')); ?>" />

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" />
    
    <!-- Styles (tokens + site + simulation layout via Vite) -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
    <?php echo $__env->yieldPushContent('styles'); ?>

    <!-- Apply saved theme early -->
    <script>
    (function(){
        try {
            var t = localStorage.getItem('theme');
            if (t === 'dark') document.documentElement.setAttribute('data-theme','dark');
        } catch (e) {}
    })();
    </script>

    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</head>
<body>
    <?php if (isset($component)) { $__componentOriginalf75d29720390c8e1fa3307604849a543 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf75d29720390c8e1fa3307604849a543 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.navigation','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('navigation'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf75d29720390c8e1fa3307604849a543)): ?>
<?php $attributes = $__attributesOriginalf75d29720390c8e1fa3307604849a543; ?>
<?php unset($__attributesOriginalf75d29720390c8e1fa3307604849a543); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf75d29720390c8e1fa3307604849a543)): ?>
<?php $component = $__componentOriginalf75d29720390c8e1fa3307604849a543; ?>
<?php unset($__componentOriginalf75d29720390c8e1fa3307604849a543); ?>
<?php endif; ?>
    
    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

</body>
</html><?php /**PATH C:\laragon\www\Nosleguma-darbs-investacija\resources\views/layouts/app.blade.php ENDPATH**/ ?>