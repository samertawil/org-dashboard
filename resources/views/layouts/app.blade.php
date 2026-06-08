<x-layouts::app.sidebar :title="$title ?? null">
    <style>
        [x-cloak] { display: none !important; }
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
        {{-- Normal page slot (visible online, or offline if on student groups) --}}
        <div x-show="!isOffline || currentPath.includes('/student-group')">
            {{ $slot }}
        </div>

        {{-- Offline warning fallback card (visible only offline and NOT on student groups) --}}
        <div x-show="isOffline && !currentPath.includes('/student-group')" x-cloak>
            <div class="max-w-xl mx-auto my-12 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-8 text-center flex flex-col items-center gap-6">
                    <div class="size-20 bg-amber-50 dark:bg-amber-950/30 rounded-full flex items-center justify-center border border-amber-200 dark:border-amber-900/50">
                        <flux:icon name="wifi" class="size-10 text-amber-500 animate-pulse" />
                    </div>
                    
                    <div class="space-y-2">
                        <h2 class="text-2xl font-bold text-zinc-950 dark:text-white">
                            {{ __('You are Offline') }}
                        </h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto">
                            {{ __('You have lost your connection, but you can still record attendance for your education groups. All changes will be saved locally and synced when you reconnect.') }}
                        </p>
                    </div>

                    <flux:button href="{{ route('student.group.index') }}" wire:navigate variant="primary" class="w-full sm:w-auto shadow-md">
                        <flux:icon name="rectangle-group" variant="micro" class="mr-2" />
                        {{ __('Go to Education Points List') }}
                    </flux:button>

                    <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800 w-full flex flex-col items-center gap-3">
                        <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">
                            {{ __('Offline-Supported Features') }}
                        </span>
                        <div class="flex flex-wrap gap-2 justify-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                {{ __('Education Points List') }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                {{ __('Daily Attendance Intake') }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                {{ __('Automatic Auto-Sync') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </flux:main>
</x-layouts::app.sidebar>
