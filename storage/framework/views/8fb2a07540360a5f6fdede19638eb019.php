<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title ?? 'دليل إعداد ومتابعة الطلاب'); ?></title>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo app('flux')->fluxAppearance(); ?>

    
</head>
<body class="font-sans antialiased text-gray-900 bg-slate-50 min-h-screen">
    <?php echo e($slot); ?>

    
    <?php app('livewire')->forceAssetInjection(); ?>
<?php echo app('flux')->scripts(); ?>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\feras\org-dashboard\resources\views/layouts/app/land.blade.php ENDPATH**/ ?>