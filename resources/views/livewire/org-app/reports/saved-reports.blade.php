<div class="flex flex-col gap-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <flux:heading level="1" size="xl">{{ __('Saved Reports') }}</flux:heading>
            <flux:subheading>{{ __('View, manage, and inspect all generated and saved reports.') }}</flux:subheading>
        </div>
        @can('reports.create')
            <flux:button href="{{ route('reports.create') }}" wire:navigate variant="primary" icon="plus">
                {{ __('Create Report') }}
            </flux:button>
        @endcan
    </div>

    {{-- Feedback Messages --}}
    <x-auth-session-status class="text-center" :status="session('message')" />
    @if (session('error'))
        <div class="p-3 bg-red-100 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 text-center rounded-lg text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    {{-- Filters Card --}}
    <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <div class="flex flex-col gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                {{-- Search by name --}}
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search report name...') }}" icon="magnifying-glass" />
                
                {{-- Date From --}}
                <flux:input type="date" wire:model.live="dateFrom" placeholder="{{ __('Report Date From') }}" />

                {{-- Date To --}}
                <flux:input type="date" wire:model.live="dateTo" placeholder="{{ __('Report Date To') }}" />

                {{-- Is Read Filter --}}
                <flux:select wire:model.live="isReadFilter" placeholder="{{ __('All Statuses') }}">
                    <flux:select.option value="">{{ __('All Statuses') }}</flux:select.option>
                    <flux:select.option value="unread">{{ __('Unread') }}</flux:select.option>
                    <flux:select.option value="read">{{ __('Read') }}</flux:select.option>
                </flux:select>

                {{-- Creator Filter --}}
                <div class="relative w-full" x-data="{
                    open: false,
                    search: '',
                    selectedId: @entangle('creatorId').live,
                    selectedLabel: '',
                    options: [
                        @foreach ($allEmployees as $employee)
                            { id: '{{ $employee->id }}', name: '{{ addslashes($employee->full_name) }}' },
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
                        <span x-text="selectedLabel || '{{ __('All Creators') }}'"></span>
                        <svg class="size-4 text-zinc-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" x-transition 
                        class="absolute left-0 right-0 z-50 mt-1 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2 shadow-md max-h-72 flex flex-col"
                        style="display: none;">
                        <!-- Search Field -->
                        <div class="relative mb-2 shrink-0">
                            <input type="text" x-model="search" placeholder="{{ __('Search creator...') }}"
                                class="w-full rounded-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 py-1.5 px-3 text-sm text-zinc-700 dark:text-zinc-300 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                @keydown.escape="open = false">
                        </div>

                        <!-- Options List -->
                        <div class="overflow-y-auto space-y-1 flex-1">
                            <button type="button" @click="select('', '')"
                                class="flex w-full items-center rounded-md px-3 py-2 text-left text-sm text-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                {{ __('All Creators') }}
                            </button>
                            <template x-for="opt in filteredOptions" :key="opt.id">
                                <button type="button" @click="select(opt.id, opt.name)"
                                    class="flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-sm text-zinc-700 dark:text-zinc-300 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/50 dark:hover:text-indigo-400"
                                    :class="selectedId == opt.id ? 'bg-indigo-50 text-indigo-600 font-medium dark:bg-indigo-900/50 dark:text-indigo-400' : ''">
                                    <span x-text="opt.name"></span>
                                    <svg x-show="selectedId == opt.id" class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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

            @if ($search !== '' || $dateFrom !== '' || $dateTo !== '' || $isReadFilter !== '' || $creatorId !== '')
                <div class="flex items-center justify-end">
                    <flux:button wire:click="clearFilters" variant="ghost" size="sm" icon="x-mark">
                        {{ __('Clear Filters') }}
                    </flux:button>
                </div>
            @endif
        </div>
    </div>

    {{-- Saved Reports List --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Report Name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Status') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Creator') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Addressed To') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Report Date') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Period') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($reports as $report)
                        <tr class="{{ !$report->is_read ? 'bg-blue-50/40 dark:bg-blue-950/10 border-l-2 border-l-blue-400 dark:border-l-blue-500' : '' }} hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('reports.show', $report) }}" wire:navigate class="flex items-center gap-2 group">
                                    @if (!$report->is_read)
                                        <span class="inline-block size-2 rounded-full bg-blue-500 flex-shrink-0" title="{{ __('Unread') }}"></span>
                                    @endif
                                    <span class="text-sm {{ !$report->is_read ? 'font-bold text-zinc-900 dark:text-white' : 'font-medium text-zinc-700 dark:text-zinc-300' }} group-hover:underline group-hover:text-indigo-600">
                                        {{ $report->report_name }}
                                    </span>
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if (!$report->is_read)
                                    <flux:badge color="blue" size="sm" icon="envelope">{{ __('Unread') }}</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm" icon="envelope-open">{{ __('Read') }}</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-650 dark:text-zinc-350">
                                {{ $report->employee->full_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-650 dark:text-zinc-350">
                                {{ $report->addressedToEmployee->full_name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $report->report_date ? $report->report_date->format('Y-m-d') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-500">
                                {{ $report->date_from ? $report->date_from->format('Y-m-d') : '-' }} &rarr; {{ $report->date_to ? $report->date_to->format('Y-m-d') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button href="{{ route('reports.show', $report) }}" wire:navigate variant="ghost" size="sm" icon="eye" />
                                    
                                    @if (auth()->user()->isSuperAdmin() || (auth()->user()->employee && auth()->user()->employee->id === $report->employee_id))
                                        <flux:button wire:click="deleteReport({{ $report->id }})" wire:confirm="{{ __('Are you sure you want to delete this report?') }}" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-zinc-500">
                                <flux:icon name="document-text" class="size-12 mx-auto text-zinc-300 mb-3" />
                                <p class="font-medium">{{ __('No saved reports found.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reports->hasPages())
            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

</div>
