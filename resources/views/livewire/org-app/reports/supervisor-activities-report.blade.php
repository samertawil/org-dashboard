<div class="flex flex-col gap-6">
    <!-- Header & Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 print:hidden">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Supervisor Activities Report') }}</flux:heading>
            <flux:subheading>{{ __('Aggregated activity statistics for supervising teachers.') }}</flux:subheading>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <span title="{{ __('Generate a physical or PDF version of this report') }}" class="w-full">
                <flux:button onclick="window.print()" icon="printer" variant="primary" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium shadow-sm transition-all duration-200">
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
    <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm print:hidden">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Date From -->
            <flux:input type="date" wire:model.live.debounce.500ms="dateFrom" label="{{ __('Date From') }}" class="w-full" />
            
            <!-- Date To -->
            <flux:input type="date" wire:model.live.debounce.500ms="dateTo" label="{{ __('Date To') }}" class="w-full" />
            
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
            <div class="flex flex-col gap-1">
                <flux:label>{{ __('Activity Name') }}</flux:label>
                <flux:select wire:model.live="selectedActivityName" class="w-full">
                    <option value="">-- {{ __('All Activities') }} --</option>
                    @foreach ($activityNamesList as $actName)
                        <option value="{{ $actName->id }}">{{ $actName->activity_name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Supervisor Teacher -->
            @if($canSelectSupervisor)
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
                    <flux:input type="text" value="{{ auth()->user()->name }}" disabled class="w-full bg-zinc-50 dark:bg-zinc-900 cursor-not-allowed opacity-75" />
                </div>
            @endif
        </div>
    </div>

    <!-- KPI & Quick Summary Info -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 print:hidden">
        <!-- KPI 1: Total Rows -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white p-6 rounded-xl shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider opacity-85">{{ __('Total Grouped Entries') }}</h3>
                <p class="text-4xl font-extrabold mt-2">{{ count($activities) }}</p>
            </div>
            <div class="text-xs opacity-75 mt-4">
                {{ __('Grouped by Batch, Group, and Activity') }}
            </div>
        </div>

        <!-- KPI 2: Total Student Attendance -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 text-white p-6 rounded-xl shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider opacity-85">{{ __('Total Student Attendance') }}</h3>
                <p class="text-4xl font-extrabold mt-2">
                    {{ collect($activities)->sum('total_attendance') }}
                </p>
            </div>
            <div class="text-xs opacity-75 mt-4">
                {{ __('Sum of student attendances in the selected range') }}
            </div>
        </div>

        <!-- KPI 3: Total Consistent Students -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-xl shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider opacity-85">{{ __('Total Consistent Students') }}</h3>
                <p class="text-4xl font-extrabold mt-2">
                    {{ collect($activities)->sum('total_consistent') }}
                </p>
            </div>
            <div class="text-xs opacity-75 mt-4">
                {{ __('Sum of consistent student ratings from reports') }}
            </div>
        </div>
    </div>

    <!-- Report Card List Container -->
    <div class="space-y-6">
        @forelse($activities as $act)
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden hover:shadow-md transition-all duration-200 print:border-b print:pb-6 print:mb-6 print:shadow-none">
                
                <!-- Card Header (Metadata & KPIs) -->
                <div class="bg-zinc-50/50 dark:bg-zinc-900/50 px-6 py-5 border-b border-zinc-200 dark:border-zinc-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="space-y-2">
                        <div class="flex items-center flex-wrap gap-2">
                            <span class="inline-flex items-center rounded-md bg-indigo-50 dark:bg-indigo-950/50 px-2.5 py-1 text-xs font-semibold text-indigo-700 dark:text-indigo-400">
                                {{ $act['domain_name'] }}
                            </span>
                            <span class="inline-flex items-center rounded-md bg-zinc-150 dark:bg-zinc-700 px-2.5 py-1 text-xs font-semibold text-zinc-800 dark:text-zinc-300">
                                {{ __('Batch') }}: {{ $act['batch_no'] }}
                            </span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white leading-tight">
                            {{ $act['activity_name'] }}
                        </h3>
                        
                        <div class="text-sm text-zinc-500 dark:text-zinc-400 flex flex-wrap gap-x-3 gap-y-1">
                            <span>
                                {{ __('Student Group') }}: <strong class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $act['group_name'] }}</strong>
                            </span>
                            <span>&bull;</span>
                            <span>
                                {{ __('Supervisor Teacher') }}: <strong class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $act['supervisor_name'] }}</strong>
                            </span>
                        </div>
                    </div>

                    <!-- KPIs -->
                    <div class="flex items-center gap-3">
                        <div class="bg-green-50/70 dark:bg-green-950/20 border border-green-200 dark:border-green-800/40 px-4 py-2 rounded-lg text-center min-w-24">
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-green-700 dark:text-green-400 block">{{ __('Attendance') }}</span>
                            <span class="text-xl font-extrabold text-green-600 dark:text-green-400">{{ $act['total_attendance'] }}</span>
                        </div>
                        <div class="bg-purple-50/70 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-800/40 px-4 py-2 rounded-lg text-center min-w-24">
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-400 block">{{ __('Consistent') }}</span>
                            <span class="text-xl font-extrabold text-purple-600 dark:text-purple-400">{{ $act['total_consistent'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card Body (Large Text Data) -->
                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Column 1: What Students Learned -->
                    <div class="bg-zinc-50/30 dark:bg-zinc-900/10 p-5 rounded-lg border border-zinc-100 dark:border-zinc-700/30 relative">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-zinc-200/60 dark:border-zinc-700/60">
                            <flux:icon name="academic-cap" class="size-5 text-indigo-500" />
                            <h4 class="font-bold text-zinc-850 dark:text-white text-sm tracking-wide">{{ __('What Students Learned') }}</h4>
                        </div>
                        @if(!empty($act['what_learned']))
                            <ul class="list-disc list-inside space-y-3 text-zinc-700 dark:text-zinc-300 text-sm leading-relaxed">
                                @foreach($act['what_learned'] as $item)
                                    <li class="break-words pl-1">{{ $item }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">{{ __('No details recorded.') }}</p>
                        @endif
                    </div>

                    <!-- Column 2: Teacher Notes -->
                    <div class="bg-zinc-50/30 dark:bg-zinc-900/10 p-5 rounded-lg border border-zinc-100 dark:border-zinc-700/30 relative">
                        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-zinc-200/60 dark:border-zinc-700/60">
                            <flux:icon name="document-text" class="size-5 text-purple-500" />
                            <h4 class="font-bold text-zinc-850 dark:text-white text-sm tracking-wide">{{ __('Teacher Notes') }}</h4>
                        </div>
                        @if(!empty($act['teacher_report_detail']))
                            <ul class="list-disc list-inside space-y-3 text-zinc-650 dark:text-zinc-400 text-sm leading-relaxed italic">
                                @foreach($act['teacher_report_detail'] as $note)
                                    <li class="break-words pl-1">{{ $note }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-zinc-400 dark:text-zinc-500 italic">{{ __('No notes recorded.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-12 text-center text-zinc-500 shadow-sm">
                <flux:icon name="clipboard-document-list" class="size-12 mx-auto text-zinc-300 mb-3" />
                <p class="text-sm font-medium">{{ __('No activities recorded for this period.') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Print Custom Styles -->
    <style>
        @media print {
            body {
                visibility: hidden;
                background: white;
                color: black;
            }

            .sidebar, header, nav, footer, .print\:hidden {
                display: none !important;
            }

            .print\:block {
                display: block !important;
            }

            /* Container styling for print */
            body > .flex {
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
