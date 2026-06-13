<div class="w-full">
    @if ($isLazy && !$loadData)
        <flux:card class="p-6 overflow-hidden text-right" dir="rtl">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center text-teal-600 dark:text-teal-400">
                        <flux:icon name="users" class="size-5" />
                    </div>
                    <div class="text-right">
                        <flux:heading size="lg" class="font-bold">القسم الثامن: إحصائيات حضور المجموعات</flux:heading>
                        <flux:subheading>إحصائيات الحضور والغياب الإجمالية للمجموعات خلال الفترة الزمنية</flux:subheading>
                    </div>
                </div>
                <flux:button wire:click="$set('loadData', true)" variant="primary" class="bg-teal-600 hover:bg-teal-700 text-white w-full sm:w-auto font-medium">
                    <span wire:loading.remove wire:target="loadData">عرض حضور المجموعات</span>
                    <span wire:loading wire:target="loadData" class="flex items-center gap-2">
                        <flux:icon name="arrow-path" class="size-4 animate-spin text-white" />
                        جاري التحميل...
                    </span>
                </flux:button>
            </div>
        </flux:card>
    @else
        <div class="flex flex-col gap-6">
            @if ($isLazy)
                <flux:card class="p-6">
                    <div class="flex items-center gap-3 border-b pb-4 mb-6 dark:border-zinc-700 text-right" dir="rtl">
                        <div class="size-10 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center text-teal-600 dark:text-teal-400">
                            <flux:icon name="users" class="size-5" />
                        </div>
                        <div class="text-right">
                            <flux:heading size="lg" class="font-bold">القسم الثامن: إحصائيات حضور المجموعات</flux:heading>
                            <flux:subheading>إحصائيات الحضور والغياب الإجمالية للمجموعات خلال الفترة الزمنية</flux:subheading>
                        </div>
                    </div>
                    @include('livewire.org-app.reports.parts.groups-attendance-content')
                </flux:card>
            @else
                <!-- Header & Actions -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 print:hidden">
                    <div class="flex flex-col gap-1">
                        <flux:heading level="1" size="xl">{{ __('Groups Attendance Summary') }}</flux:heading>
                        <flux:subheading>{{ __('Attendance statistics for all student groups.') }}</flux:subheading>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <span title="{{ __('Generate a physical or PDF version of this report') }}" class="w-full">
                            <flux:button onclick="window.print()" icon="printer" variant="primary" class="w-full">
                                {{ __('Print Report') }}
                            </flux:button>
                        </span>
                    </div>
                </div>

                <!-- Print Header -->
                <div class="hidden print:block text-center mb-8">
                    <h1 class="text-2xl font-bold">{{ __('Groups Attendance Summary') }}</h1>
                    <p class="text-sm text-gray-500">{{ __('From') }}: {{ $dateFrom }} - {{ __('To') }}:
                        {{ $dateTo }}</p>
                </div>

                <!-- Filters -->
                <div
                    class="bg-white dark:bg-zinc-800 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm print:hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <flux:input type="date" wire:model.live.debounce.700ms="dateFrom" label="{{ __('Date From') }}" />
                        <flux:input type="date" wire:model.live.debounce.700ms="dateTo" label="{{ __('Date To') }}" />
                    </div>
                </div>

                @include('livewire.org-app.reports.parts.groups-attendance-content')
            @endif
        </div>
    @endif

    <style>
        @media print {
            body {
                visibility: hidden;
            }

            body {
                visibility: visible;
                background: white;
                color: black;
            }

            nav,
            header,
            aside,
            .sidebar {
                display: none !important;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
            }

            .print\:hidden {
                display: none !important;
            }

            .print\:block {
                display: block !important;
            }
        }
    </style>
</div>
