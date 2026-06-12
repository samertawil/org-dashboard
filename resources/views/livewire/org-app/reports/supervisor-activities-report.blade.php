<div class="flex flex-col gap-6">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 print:hidden">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Supervisor Activities Report') }}</flux:heading>
            <flux:subheading>{{ __('Aggregated activity statistics for supervising teachers.') }}</flux:subheading>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <span title="{{ __('Generate a physical or PDF version of this report') }}" class="w-full">
                <flux:button onclick="window.print()" icon="printer" variant="primary"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium shadow-sm transition-all duration-200">
                    {{ __('Print Report') }}
                </flux:button>
            </span>
        </div>
    </div>

    <!-- Print Header -->
    <div class="hidden print:block text-center mb-8 border-b pb-4">
        <h1 class="text-2xl font-bold text-zinc-950">{{ __('Supervisor Activities Report') }}</h1>
        <p class="text-sm text-zinc-500 mt-2">
            {{ __('From') }}: {{ $dateFrom ?: '-' }} &nbsp;&bull;&nbsp; {{ __('To') }}: {{ $dateTo ?: '-' }}
        </p>
    </div>

    <!-- Filters Section -->
    <div
        class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm print:hidden">
        <div class="flex flex-col gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-4">
                <!-- Date From -->
                <flux:input type="date" wire:model.live.debounce.500ms="dateFrom" label="{{ __('Date From') }}"
                    class="w-full" />

                <!-- Date To -->
                <flux:input type="date" wire:model.live.debounce.500ms="dateTo" label="{{ __('Date To') }}"
                    class="w-full" />

                <!-- Batch No -->
                <div class="flex flex-col gap-1">
                    <flux:label>{{ __('Batch Number') }}</flux:label>
                    <flux:select wire:model.live="selectedBatch" class="w-full">
                        <option value="">-- {{ __('All Batches') }} --</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch }}">{{ $batch }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Student Group -->
                <div class="flex flex-col gap-1">
                    <flux:label>{{ __('Student Group') }}</flux:label>
                    <flux:select wire:model.live="selectedGroup" class="w-full">
                        <option value="">-- {{ __('All Groups') }} --</option>
                        @foreach ($groups as $grp)
                            <option value="{{ $grp->id }}">{{ $grp->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Activity Name -->
                <div class="flex flex-col gap-1 min-w-[200px]">
                    <flux:label>{{ __('Activity Name') }}</flux:label>
                    <div class="relative w-full" x-data="{
                        open: false,
                        search: '',
                        selectedId: @entangle('selectedActivityName').live,
                        selectedLabel: '',
                        options: [
                            @foreach ($activityNamesList as $actName)
                                { id: '{{ $actName->id }}', name: '{{ addslashes($actName->activity_name) }}' },
                            @endforeach
                        ],
                        get filteredOptions() {
                            if (!this.search) return this.options;
                            return this.options.filter(opt => opt.name.toLowerCase().includes(this.search.toLowerCase()));
                        },
                        select(id, name) {
                            this.selectedId = id;
                            this.selectedLabel = name;
                            this.open = false;
                            this.search = '';
                        },
                        init() {
                            let updateLabel = () => {
                                let selected = this.options.find(opt => opt.id == this.selectedId);
                                this.selectedLabel = selected ? selected.name : '';
                            };
                            updateLabel();
                            this.$watch('selectedId', () => updateLabel());
                        }
                    }" @click.outside="open = false" x-init="init()">
                        
                        <!-- Trigger Button -->
                        <button type="button" @click="open = !open" 
                            class="flex h-10 w-full items-center justify-between rounded-lg border border-zinc-200 border-b-zinc-300/80 bg-white dark:bg-zinc-800 dark:border-zinc-700 px-3 py-2 text-left text-base sm:text-sm leading-[1.375rem] text-zinc-700 dark:text-zinc-300 shadow-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <span class="truncate pr-2" x-text="selectedLabel || '-- {{ __('All Activities') }} --'"></span>
                            <svg class="size-4 shrink-0 text-zinc-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" x-transition 
                            class="absolute left-0 right-0 z-50 mt-1 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2 shadow-md max-h-72 flex flex-col"
                            style="display: none;">
                            
                            <!-- Search Field -->
                            <div class="relative mb-2 shrink-0">
                                <input type="text" x-model="search" placeholder="{{ __('Search...') }}"
                                    class="w-full rounded-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 py-1.5 px-3 text-sm text-zinc-700 dark:text-zinc-300 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                    @keydown.escape="open = false">
                            </div>

                            <!-- Options List -->
                            <div class="overflow-y-auto space-y-1 flex-1">
                                <button type="button" @click="select('', '')"
                                    class="flex w-full items-center rounded-md px-3 py-2 text-left text-sm text-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                    -- {{ __('All Activities') }} --
                                </button>
                                <template x-for="opt in filteredOptions" :key="opt.id">
                                    <button type="button" @click="select(opt.id, opt.name)"
                                        class="flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-sm text-zinc-700 dark:text-zinc-300 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/50 dark:hover:text-indigo-400"
                                        :class="selectedId == opt.id ? 'bg-indigo-50 text-indigo-600 font-medium dark:bg-indigo-900/50 dark:text-indigo-400' : ''">
                                        <span class="truncate pr-2" x-text="opt.name"></span>
                                        <svg x-show="selectedId == opt.id" class="h-4 w-4 shrink-0 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </template>
                                <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-zinc-400 italic">
                                    {{ __('No results found.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Supervisor Teacher -->
                @if ($canSelectSupervisor)
                    <div class="flex flex-col gap-1">
                        <flux:label>{{ __('Supervisor Teacher') }}</flux:label>
                        <flux:select wire:model.live="selectedSupervisorId" class="w-full">
                            <option value="">-- {{ __('All Supervisors') }} --</option>
                            @foreach ($supervisors as $supervisor)
                                <option value="{{ $supervisor->user_id }}">{{ $supervisor->full_name }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                @else
                    <div class="flex flex-col gap-1">
                        <flux:label>{{ __('Supervisor Teacher') }}</flux:label>
                        <flux:input type="text" value="{{ auth()->user()->name }}" disabled
                            class="w-full bg-zinc-50 dark:bg-zinc-900 cursor-not-allowed opacity-75" />
                    </div>
                @endif

                <!-- Report Status -->
                <div class="flex flex-col gap-1">
                    <flux:label>{{ __('Report Status') }}</flux:label>
                    <flux:select wire:model.live="selectedReportStatus" class="w-full">
                        <option value="">-- {{ __('All Statuses') }} --</option>
                        <option value="unreported">{{ __('Unreported') }}</option>
                        <option value="reported">{{ __('Reported') }}</option>
                    </flux:select>
                </div>
            </div>

            @if ($selectedBatch !== '' || $selectedGroup !== '' || $selectedActivityName !== '' || $selectedReportStatus !== '' || ($canSelectSupervisor && $selectedSupervisorId !== ''))
                <div class="flex items-center justify-end">
                    <flux:button wire:click="clearFilters" variant="ghost" size="sm" icon="x-mark">
                        {{ __('Clear Filters') }}
                    </flux:button>
                </div>
            @endif
        </div>
    </div>

    <!-- Report Card List Container -->
    <div class="space-y-6 relative">
        <!-- Backdrop Blur Loading Overlay (filters & checkbox changes) -->
        <div wire:loading.delay
            wire:target="dateFrom,dateTo,selectedBatch,selectedGroup,selectedActivityName,selectedSupervisorId,selectedActivities,selectedReportStatus"
            class="absolute inset-0 z-10 bg-white/50 dark:bg-zinc-800/50 backdrop-blur-sm flex items-center justify-center rounded-xl">
            <flux:icon name="arrow-path" class="size-8 animate-spin text-zinc-500" />
        </div>

        @forelse($activities as $act)
            <div wire:key="activity-card-{{ $act['compound_key'] }}-{{ implode('-', $act['schedule_ids']) }}"
                class="{{ $act['is_reported'] ? 'bg-emerald-50/20 dark:bg-emerald-950/5 border-emerald-200 dark:border-emerald-800/30' : 'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700' }} rounded-xl border shadow-sm overflow-hidden hover:shadow-md transition-all duration-200 print:border-b print:pb-6 print:mb-6 print:shadow-none">

                <!-- Card Header (Metadata & KPIs) -->
                <div
                    class="{{ $act['is_reported'] ? 'bg-emerald-50/40 dark:bg-emerald-900/10' : 'bg-zinc-50/50 dark:bg-zinc-900/50' }} px-6 py-5 border-b border-zinc-200 dark:border-zinc-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <!-- Checkbox selection -->
                        @if (!$act['is_reported'])
                            <div class="pt-1 print:hidden">
                                <flux:checkbox wire:model.live="selectedActivities" value="{{ $act['compound_key'] }}|{{ implode(',', $act['schedule_ids']) }}" />
                            </div>
                        @else
                            <div class="pt-1 print:hidden text-emerald-600 dark:text-emerald-400" title="{{ __('Report Submitted') }}">
                                <flux:icon name="check-circle" class="size-5" />
                            </div>
                        @endif
                        <div class="space-y-2">
                            <div class="flex items-center flex-wrap gap-2">
                                <span
                                    class="inline-flex items-center rounded-md bg-indigo-50 dark:bg-indigo-950/50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:text-indigo-400">
                                    {{ $act['domain_name'] }}
                                </span>
                                <span
                                    class="inline-flex items-center rounded-md bg-zinc-150 dark:bg-zinc-700 px-2.5 py-1 text-xs font-semibold text-zinc-800 dark:text-zinc-300">
                                    {{ __('Batch') }}: {{ $act['batch_no'] }}
                                </span>
                                @if ($act['is_reported'])
                                    <span
                                        class="inline-flex items-center rounded-md bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/30">
                                        {{ __('Report Submitted') }}
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white leading-tight">
                                {{ $act['activity_name'] }}
                            </h3>

                            <div class="text-sm text-zinc-500 dark:text-zinc-400 flex flex-wrap gap-x-3 gap-y-1">
                                <span>
                                    {{ __('Student Group') }}: <strong
                                        class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $act['group_name'] }}</strong>
                                </span>
                                <span>&bull;</span>
                                <span>
                                    {{ __('Supervisor Teacher') }}: <strong
                                        class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $act['supervisor_name'] }}</strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- KPIs -->
                    <div class="flex items-center gap-3">
                        <div
                            class="bg-green-50/70 dark:bg-green-950/20 border border-green-200 dark:border-green-800/40 px-4 py-2 rounded-lg text-center min-w-24">
                            <span
                                class="text-[10px] font-semibold uppercase tracking-wider text-green-700 dark:text-green-400 block">{{ __('Attendance') }}</span>
                            <span
                                class="text-xl font-extrabold text-green-600 dark:text-green-400">{{ $act['total_attendance'] }}</span>
                        </div>
                        <div
                            class="bg-purple-50/70 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-800/40 px-4 py-2 rounded-lg text-center min-w-24">
                            <span
                                class="text-[10px] font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-400 block">{{ __('Consistent') }}</span>
                            <span
                                class="text-xl font-extrabold text-purple-600 dark:text-purple-400">{{ $act['total_consistent'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card Body (Large Text Data) -->
                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Column 1: What Students Learned -->
                    <div
                        class="bg-zinc-50/30 dark:bg-zinc-900/10 p-5 rounded-lg border border-zinc-100 dark:border-zinc-700/30 relative">
                        <div
                            class="flex items-center gap-2 mb-4 pb-2 border-b border-zinc-200/60 dark:border-zinc-700/60">
                            <flux:icon name="academic-cap" class="size-5 text-indigo-500" />
                            <h4 class="font-bold text-zinc-850 dark:text-white text-sm tracking-wide">
                                {{ __('What Students Learned') }}</h4>
                        </div>
                        @if (!empty($act['what_learned']))
                            <ul
                                class="list-disc list-inside space-y-3 text-zinc-700 dark:text-zinc-300 text-sm leading-relaxed">
                                @foreach ($act['what_learned'] as $item)
                                    <li class="break-words pl-1">
                                        @if (is_array($item))
                                            <span>{{ $item['text'] }}</span>
                                            <span
                                                class="text-xs text-zinc-400 dark:text-zinc-500 ml-2 block sm:inline font-normal">
                                                ({{ __('Teacher') }}: {{ $item['teacher'] }}@if (!empty($item['period_group']) && $item['period_group'] !== '-')
                                                    - {{ __('For Group') }}: {{ $item['period_group'] }}
                                                @endif)
                                            </span>
                                        @else
                                            <span>{{ $item }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">{{ __('No details recorded.') }}
                            </p>
                        @endif
                    </div>

                    <!-- Column 2: Teacher Notes -->
                    <div
                        class="bg-zinc-50/30 dark:bg-zinc-900/10 p-5 rounded-lg border border-zinc-100 dark:border-zinc-700/30 relative">
                        <div
                            class="flex items-center gap-2 mb-4 pb-2 border-b border-zinc-200/60 dark:border-zinc-700/60">
                            <flux:icon name="document-text" class="size-5 text-purple-500" />
                            <h4 class="font-bold text-zinc-850 dark:text-white text-sm tracking-wide">
                                {{ __('Teacher Notes') }}</h4>
                        </div>
                        @if (!empty($act['teacher_report_detail']))
                            <ul
                                class="list-disc list-inside space-y-3 text-zinc-650 dark:text-zinc-400 text-sm leading-relaxed italic">
                                @foreach ($act['teacher_report_detail'] as $note)
                                    <li class="break-words pl-1">
                                        @if (is_array($note))
                                            <span>{{ $note['text'] }}</span>
                                            <span
                                                class="text-xs text-zinc-400 dark:text-zinc-500 ml-2 block sm:inline font-normal">
                                                ({{ __('Teacher') }}: {{ $note['teacher'] }}@if (!empty($note['period_group']) && $note['period_group'] !== '-')
                                                    - {{ __('For Group') }}: {{ $note['period_group'] }}
                                                @endif)
                                            </span>
                                        @else
                                            <span>{{ $note }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">{{ __('No notes recorded.') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            @empty
                <div
                    class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center text-zinc-500 shadow-sm">
                    <flux:icon name="clipboard-document-list" class="size-12 mx-auto text-zinc-300 mb-3" />
                    <p class="text-sm font-medium">{{ __('No activities recorded for this period.') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Floating Action Bar for Selected Activities -->
        @if (!empty($selectedActivities))
            <div
                class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 px-6 py-4 rounded-full shadow-xl flex items-center gap-6 print:hidden">
                <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                    {{ __('Selected') }}: <strong
                        class="text-indigo-600 dark:text-indigo-400">{{ count($selectedActivities) }}</strong>
                    {{ __('activities') }}
                </span>

                <!-- Loading state while redirecting -->
                <div wire:loading wire:target="openCreateReport"
                    class="flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
                    <flux:icon name="arrow-path" class="size-4 animate-spin" />
                    {{ __('Preparing report...') }}
                </div>

                <div wire:loading.remove wire:target="openCreateReport">
                    <flux:button wire:click="openCreateReport" variant="primary" icon="document-text"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-full px-5 shadow-sm">
                        {{ __('Generate Consolidated Report') }}
                    </flux:button>
                </div>
            </div>
        @endif

        <!-- Print Custom Styles -->
        <style>
            @media print {
                body {
                    visibility: hidden;
                    background: white;
                    color: black;
                }

                .sidebar,
                header,
                nav,
                footer,
                .print\:hidden {
                    display: none !important;
                }

                .print\:block {
                    display: block !important;
                }

                body>.flex {
                    visibility: visible;
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }

                .rounded-xl {
                    border-radius: 0 !important;
                }

                .shadow-sm {
                    box-shadow: none !important;
                }
            }
        </style>
    </div>
