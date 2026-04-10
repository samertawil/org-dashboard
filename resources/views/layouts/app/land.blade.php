<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'دليل إعداد ومتابعة الطلاب' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    {{-- @fluxStyles --}}
</head>
<body class="font-sans antialiased text-gray-900 bg-slate-50 min-h-screen">
    {{ $slot }}
    
    @fluxScripts
</body>
</html>
