<x-layouts::app.sidebar :title="$title ?? null">
    <style>
        @media print {
            aside, header, .no-print { display: none !important; }
            main { margin: 0 !important; padding: 0 !important; width: 100% !important; }
            .flux-sidebar { display: none !important; }
             /* Ensure charts take full width and pages break nicely */
            .flux-card { break-inside: avoid; page-break-inside: avoid; border: 1px solid #ddd; box-shadow: none; }
            body { background: white; }
        }
    </style>
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts::app.sidebar>
