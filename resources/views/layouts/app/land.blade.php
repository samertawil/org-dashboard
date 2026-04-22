<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl" data-flux-appearance="{{ $mode ?? 'light' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'دليل إعداد ومتابعة الطلاب' }}</title>
    <link rel="icon" href="{{ asset('logo2.png') }}" sizes="any">
    <link rel="icon" href="{{ asset('logo2.png') }}" type="image/svg+xml">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    <script>
        const forcedMode = "{{ $mode ?? 'light' }}";
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
    {{ $slot }}
    
    @fluxScripts
</body>
</html>
