<div class="flex flex-col gap-6 print-container">
    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 no-print pb-4 border-b border-zinc-200 dark:border-zinc-700">
        <div>
            <flux:heading level="1" size="xl">{{ __('Report Details') }}</flux:heading>
            <flux:subheading>{{ __('Full report details, classifications, items and attachments.') }}</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <flux:button href="{{ route('reports.saved-reports') }}" wire:navigate icon="arrow-right" variant="ghost" class="text-zinc-500">
                {{ __('Back to Saved Reports') }}
            </flux:button>
            <flux:button icon="printer" onclick="window.print()" variant="primary">
                {{ __('Print Report') }}
            </flux:button>
        </div>
    </div>

    {{-- Print Only Header --}}
    <div class="hidden print:block pb-6 border-b-2 border-zinc-850 space-y-4">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-zinc-950">{{ $report->report_name }}</h1>
                <p class="text-sm text-zinc-500 mt-1">{{ __('Report Date') }}: {{ $report->report_date ? $report->report_date->format('Y-m-d') : '-' }}</p>
            </div>
            <div class="text-right">
                <span class="text-sm font-bold bg-zinc-100 px-3 py-1 rounded text-zinc-800">{{ $report->mainType->status_name ?? '-' }}</span>
            </div>
        </div>
    </div>

    {{-- Card 1: Report Information --}}
    <flux:card class="p-6">
        <div class="flex items-center gap-2 mb-5 pb-3 border-b border-zinc-200 dark:border-zinc-700">
            <flux:icon name="document-text" class="size-5 text-zinc-500" />
            <flux:heading size="lg">{{ __('Report Information') }}</flux:heading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-3">
                <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-1">{{ __('Report Name') }}</span>
                <span class="text-base font-bold text-zinc-900 dark:text-white">{{ $report->report_name }}</span>
            </div>

            <div>
                <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-1">{{ __('Report Date') }}</span>
                <span class="text-sm font-bold text-zinc-800 dark:text-zinc-250">{{ $report->report_date ? $report->report_date->format('Y-m-d') : '-' }}</span>
            </div>

            <div>
                <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-1">{{ __('Report Coverage Period') }}</span>
                <span class="text-sm font-bold text-zinc-800 dark:text-zinc-250 flex items-center gap-1.5 mt-0.5">
                    <span>{{ $report->date_from ? $report->date_from->format('Y-m-d') : '-' }}</span>
                    <span class="text-zinc-450">&rarr;</span>
                    <span>{{ $report->date_to ? $report->date_to->format('Y-m-d') : '-' }}</span>
                </span>
            </div>

            @if ($report->batch_no)
                <div>
                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-1">{{ __('Batch Number') }}</span>
                    <span class="text-sm font-bold text-zinc-800 dark:text-zinc-250">{{ __('Batch') }} {{ $report->batch_no }}</span>
                </div>
            @endif

            <div>
                <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-1">{{ __('Report Type') }}</span>
                <span class="text-sm font-bold text-zinc-800 dark:text-zinc-250">{{ $report->mainType->status_name ?? '-' }}</span>
            </div>

            <div>
                <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-1">{{ __('Report Period Type') }}</span>
                <span class="text-sm font-bold text-zinc-800 dark:text-zinc-250">{{ $report->periodType->status_name ?? '-' }}</span>
            </div>

            @if ($report->requiredFrom)
                <div>
                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-1">{{ __('Required From') }}</span>
                    <span class="text-sm font-bold text-zinc-800 dark:text-zinc-250">{{ $report->requiredFrom->status_name }}</span>
                </div>
            @endif
        </div>
    </flux:card>

    {{-- Card 2: Recipients & Routing --}}
    <flux:card class="p-6">
        <div class="flex items-center gap-2 mb-5 pb-3 border-b border-zinc-200 dark:border-zinc-700">
            <flux:icon name="users" class="size-5 text-zinc-500" />
            <flux:heading size="lg">{{ __('Recipients & Routing') }}</flux:heading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-1">{{ __('Creator') }}</span>
                <span class="text-sm font-bold text-zinc-800 dark:text-zinc-250">{{ $report->employee->full_name ?? '-' }}</span>
            </div>

            <div>
                <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-1">{{ __('Addressed To') }}</span>
                <span class="text-sm font-bold text-zinc-800 dark:text-zinc-250">{{ $report->addressedToEmployee->full_name ?? '-' }}</span>
            </div>

            <div>
                <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-2">{{ __('Addressed Departments') }}</span>
                <div class="flex flex-wrap gap-1.5">
                    @forelse ($report->addressed_to_dept_types ?: [] as $dept)
                        <flux:badge size="sm" color="zinc" variant="outline">{{ __($dept) }}</flux:badge>
                    @empty
                        <span class="text-sm text-zinc-455 italic">-</span>
                    @endforelse
                </div>
            </div>

            @if (!empty($ccEmployees))
                <div class="md:col-span-3">
                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-2">{{ __('CC Recipients') }}</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($ccEmployees as $ccName)
                            <flux:badge size="sm" color="indigo" variant="outline">{{ $ccName }}</flux:badge>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </flux:card>

    {{-- Card 3: Scope of Report (Covered Student Groups & Activities) --}}
    @if (!empty($coveredGroups) || !empty($coveredActivities))
        <flux:card class="p-6">
            <div class="flex items-center gap-2 mb-5 pb-3 border-b border-zinc-200 dark:border-zinc-700">
                <flux:icon name="academic-cap" class="size-5 text-zinc-500" />
                <flux:heading size="lg">{{ __('Scope of Report') }}</flux:heading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if (!empty($coveredGroups))
                    <div>
                        <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-2">{{ __('Covered Student Groups') }}</span>
                        <ul class="space-y-1.5 text-sm text-zinc-700 dark:text-zinc-300 list-disc list-inside bg-zinc-50 dark:bg-zinc-800/40 p-4 rounded-xl border border-zinc-150 dark:border-zinc-750">
                            @foreach ($coveredGroups as $groupName)
                                @php
                                    $isGrpArabic = preg_match('/\p{Arabic}/u', $groupName);
                                @endphp
                                <li dir="{{ $isGrpArabic ? 'rtl' : 'ltr' }}" class="{{ $isGrpArabic ? 'text-right' : 'text-left' }}">{{ $groupName }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (!empty($coveredActivities))
                    <div>
                        <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-2">{{ __('Covered Educational Activities') }}</span>
                        <ul class="space-y-1.5 text-sm text-zinc-700 dark:text-zinc-300 list-disc list-inside bg-zinc-50 dark:bg-zinc-800/40 p-4 rounded-xl border border-zinc-150 dark:border-zinc-750">
                            @foreach ($coveredActivities as $actName)
                                @php
                                    $isActArabic = preg_match('/\p{Arabic}/u', $actName);
                                @endphp
                                <li dir="{{ $isActArabic ? 'rtl' : 'ltr' }}" class="{{ $isActArabic ? 'text-right' : 'text-left' }}">{{ $actName }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </flux:card>
    @endif

    {{-- Card 4: Report Items (Bodies) --}}
    <div class="flex flex-col gap-4">
        <div class="flex items-center justify-between pb-2 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center gap-2">
                <flux:heading size="lg">{{ __('Report Items') }}</flux:heading>
                <flux:badge color="amber" size="sm">{{ count($report->bodies) }} {{ __('items') }}</flux:badge>
            </div>
        </div>

        <div class="space-y-6">
            @forelse ($report->bodies as $body)
                <flux:card class="border-l-4 border-l-indigo-400 p-6 space-y-4 print-avoid-break">
                    @php
                        $itemTitle = $body->title ?: __('Untitled Item');
                        $isTitleArabic = preg_match('/\p{Arabic}/u', $itemTitle);
                    @endphp
                    <div class="flex items-center justify-between mb-2 pb-3 border-b border-zinc-200 dark:border-zinc-700">
                        <div dir="{{ $isTitleArabic ? 'rtl' : 'ltr' }}" class="flex items-center gap-2">
                            <flux:badge color="indigo" size="sm">{{ $body->item_order }}</flux:badge>
                            <h3 class="text-base font-bold text-zinc-850 dark:text-zinc-150">
                                {{ $itemTitle }}
                            </h3>
                        </div>
                    </div>

                    {{-- Content --}}
                    @php
                        $isContentArabic = preg_match('/\p{Arabic}/u', $body->content);
                    @endphp
                    <div dir="{{ $isContentArabic ? 'rtl' : 'ltr' }}" class="text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed whitespace-pre-wrap {{ $isContentArabic ? 'text-right' : 'text-left' }}">
                        {{ $body->content }}
                    </div>

                    {{-- Observation --}}
                    @if ($body->observation)
                        @php
                            $isObsArabic = preg_match('/\p{Arabic}/u', $body->observation);
                        @endphp
                        <div dir="{{ $isObsArabic ? 'rtl' : 'ltr' }}" class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 text-sm text-amber-800 dark:text-amber-300 space-y-1 {{ $isObsArabic ? 'text-right' : 'text-left' }}">
                            <strong class="text-xs uppercase tracking-wider block font-bold text-amber-900 dark:text-amber-400">{{ __('Observation') }}</strong>
                            <div>{{ $body->observation }}</div>
                        </div>
                    @endif

                    {{-- Attachments --}}
                    @if (!empty($body->attachments))
                        <div class="space-y-2 pt-2">
                            <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block mb-2">{{ __('Attachments') }}</span>
                            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
                                @foreach ($body->attachments as $att)
                                    @php
                                        $url = is_array($att) ? ($att['url'] ?? $att['path'] ?? '') : $att;
                                        $name = is_array($att) ? ($att['name'] ?? __('File')) : __('File');
                                        if ($url && !str_starts_with($url, 'http') && !str_contains($url, 'storage')) {
                                            $url = 'storage/' . ltrim($url, '/');
                                        }
                                    @endphp
                                    @if ($url)
                                        <a href="{{ asset($url) }}" target="_blank" class="border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 flex flex-col items-center gap-1.5 bg-white dark:bg-zinc-900 hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors shadow-xs">
                                            @if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $url))
                                                <img src="{{ asset($url) }}" class="size-12 object-cover rounded shadow-xs" />
                                            @else
                                                <flux:icon name="document" class="size-12 text-zinc-300 dark:text-zinc-650" />
                                            @endif
                                            <span class="text-[10px] text-zinc-450 dark:text-zinc-500 truncate w-full text-center leading-tight">{{ $name }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </flux:card>
            @empty
                <div class="bg-white dark:bg-zinc-800 p-8 text-center rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                    <flux:icon name="document-text" class="size-12 mx-auto text-zinc-300 mb-3" />
                    <p class="text-sm font-medium text-zinc-500">{{ __('No items in this report.') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Card 5: General Notes --}}
    @if ($report->note)
        <flux:card class="p-6">
            <div class="flex items-center gap-2 mb-4 pb-2 border-b border-zinc-200 dark:border-zinc-700">
                <flux:icon name="chat-bubble-left-ellipsis" class="size-5 text-zinc-500" />
                <flux:heading size="lg">{{ __('General Notes') }}</flux:heading>
            </div>
            <p class="text-sm text-zinc-750 dark:text-zinc-300 whitespace-pre-wrap leading-relaxed">
                {{ $report->note }}
            </p>
        </flux:card>
    @endif

    {{-- Custom Printing Style --}}
    <style>
        @media print {
            aside, nav, header, footer, button, .no-print, [data-flux-sidebar], [data-flux-header] {
                display: none !important;
            }

            body {
                background: white !important;
                color: black !important;
                font-size: 11pt;
            }

            .print-container {
                margin: 0 !important;
                padding: 0 !important;
            }

            .print-avoid-break {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</div>
