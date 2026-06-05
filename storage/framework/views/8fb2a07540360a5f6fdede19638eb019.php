<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="rtl" data-flux-appearance="<?php echo e($mode ?? 'light'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title ?? 'دليل إعداد ومتابعة الطلاب'); ?></title>
    <link rel="icon" href="<?php echo e(asset('logo2.png')); ?>" sizes="any">
    <link rel="icon" href="<?php echo e(asset('logo2.png')); ?>" type="image/svg+xml">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo app('flux')->fluxAppearance(); ?>

    <script>
        const forcedMode = "<?php echo e($mode ?? 'light'); ?>";
        if (forcedMode === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
            
            // Observe and prevent re-adding dark mode
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark');
                        }
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });
        }
    </script>
</head>
<body class="font-sans antialiased text-gray-900 bg-slate-50 min-h-screen">
    <?php echo e($slot); ?>

    
    <?php app('livewire')->forceAssetInjection(); ?>
<?php echo app('flux')->scripts(); ?>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\feras\org-dashboard\resources\views/layouts/app/land.blade.php ENDPATH**/ ?>