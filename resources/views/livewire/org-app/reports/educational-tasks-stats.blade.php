<div class="w-full">
    @if ($lazy && !$loadData)
        <flux:card class="p-6 overflow-hidden text-right" dir="rtl">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center text-violet-600 dark:text-violet-400">
                        <flux:icon name="chart-bar-square" class="size-5" />
                    </div>
                    <div class="text-right">
                        <flux:heading size="lg" class="font-bold">القسم السابع: إحصائيات المهام التعليمية (الدفعة الأخيرة)</flux:heading>
                        <flux:subheading>إحصائيات إنجاز المهام موزعة حسب الدفعة والشهر والمركز التعليمي</flux:subheading>
                    </div>
                </div>
                <flux:button wire:click="$set('loadData', true)" variant="primary" class="bg-violet-600 hover:bg-violet-700 text-white w-full sm:w-auto font-medium">
                    <span wire:loading.remove wire:target="loadData">عرض الإحصائيات</span>
                    <span wire:loading wire:target="loadData" class="flex items-center gap-2">
                        <flux:icon name="arrow-path" class="size-4 animate-spin text-white" />
                        جاري التحميل...
                    </span>
                </flux:button>
            </div>
        </flux:card>
    @else
        <div class="flex flex-col gap-6">
            @if ($lazy)
                <flux:card class="p-6">
                    <div class="flex items-center gap-3 border-b pb-4 mb-6 dark:border-zinc-700 text-right" dir="rtl">
                        <div class="size-10 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center text-violet-600 dark:text-violet-400">
                            <flux:icon name="chart-bar-square" class="size-5" />
                        </div>
                        <div class="text-right">
                            <flux:heading size="lg" class="font-bold">القسم السابع: إحصائيات المهام التعليمية (الدفعة الأخيرة)</flux:heading>
                            <flux:subheading>إحصائيات إنجاز المهام موزعة حسب الدفعة والشهر والمركز التعليمي</flux:subheading>
                        </div>
                    </div>
                    @include('livewire.org-app.reports.parts.stats-content')
                </flux:card>
            @else
                {{-- Page Header --}}
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex flex-col gap-1">
                        <flux:heading level="1" size="xl">{{ __('Educational Tasks Statistics') }}</flux:heading>
                        <flux:subheading>
                            {{ __('Task completion overview grouped by batch, month, and student group.') }}
                        </flux:subheading>
                    </div>

                    <span class="w-full sm:w-auto">
                        <flux:button href="{{ route('educational-tasks.index') }}" wire:navigate variant="ghost"
                            icon="clipboard-document-list" class="w-full">
                            {{ __('Task List') }}
                        </flux:button>
                    </span>
                </div>

                @include('livewire.org-app.reports.parts.stats-content')
            @endif
        </div>
    @endif
</div>
