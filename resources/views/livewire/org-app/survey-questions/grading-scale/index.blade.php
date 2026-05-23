<div class="flex flex-col gap-6" x-data="{
    expanded: {},
    toggle(id) {
        this.expanded[id] = !this.expanded[id];
    },
    expandAll(tree) {
        tree.forEach(s => {
            this.expanded['section_' + s.section_id] = true;
            s.batches.forEach(b => {
                this.expanded['batch_' + s.section_id + '_' + b.batch_no] = true;
            });
        });
    },
    collapseAll() {
        this.expanded = {};
    }
}">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Survey Grading Scales') }}</flux:heading>
            <flux:subheading>{{ __('Manage the evaluation scales for surveys based on score percentages.') }}
            </flux:subheading>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto items-stretch sm:items-center">
            @if ($viewType === 'tree')
                <div class="flex gap-2 w-full sm:w-auto justify-end">
                    <flux:button x-on:click="expandAll(@js($gradingScalesTree))" variant="subtle" icon="arrows-pointing-out" size="sm">
                        {{ __('Expand All') }}
                    </flux:button>
                    <flux:button x-on:click="collapseAll()" variant="subtle" icon="arrows-pointing-in" size="sm">
                        {{ __('Collapse All') }}
                    </flux:button>
                </div>
            @endif

            {{-- View Switcher --}}
            <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-900/50 p-1 rounded-lg border border-zinc-200 dark:border-zinc-700/50 shrink-0">
                <button wire:click="$set('viewType', 'table')" type="button"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1.5 transition-all {{ $viewType === 'table' ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-sm border border-zinc-200 dark:border-zinc-700' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 border border-transparent' }}">
                    <flux:icon name="table-cells" class="size-3.5" />
                    {{ __('Table') }}
                </button>
                <button wire:click="$set('viewType', 'tree')" type="button"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1.5 transition-all {{ $viewType === 'tree' ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-sm border border-zinc-200 dark:border-zinc-700' : 'text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 border border-transparent' }}">
                    <flux:icon name="queue-list" class="size-3.5" />
                    {{ __('Tree View') }}
                </button>
            </div>

            <span title="{{ __('Manage domain-specific descriptions') }}" class="w-full sm:w-auto">
                <flux:button href="{{ route('survey.grading.scale.manage-descriptions') }}" wire:navigate variant="ghost" icon="chat-bubble-bottom-center-text" class="w-full">
                    {{ __('Domain Descriptions') }}
                </flux:button>
            </span>
            @can('survey.manage')
                <span title="{{ __('Add a new evaluation scale entry') }}" class="w-full sm:w-auto">
                    <flux:button href="{{ route('survey.grading.scale.create') }}" wire:navigate variant="primary" icon="plus" class="w-full">
                        {{ __('Add Grading Scale') }}
                    </flux:button>
                </span>
            @endcan
        </div>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Search and Table Section --}}
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 space-y-4">
            <div class="w-full">
                <flux:input wire:model.live="search" :placeholder="__('Search by evaluation or description...')"
                    icon="magnifying-glass" class="w-full" />
            </div>
            <div class="flex flex-col md:flex-row gap-4 items-center">
                <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto flex-1">
                    <div class="w-full sm:w-1/2 md:w-48">
                        <flux:select wire:model.live="searchBatch" :placeholder="__('Batch...')">
                            <option value="">{{ __('All Batches') }}</option>
                            @foreach ($batches as $batch)
                                <option value="{{ $batch->batch_no }}">{{ __('Batch') }} {{ $batch->batch_no }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div class="w-full sm:w-1/2 md:w-64">
                        <flux:select wire:model.live="searchSection" :placeholder="__('Section...')">
                            <option value="">{{ __('All Sections') }}</option>
                            @foreach ($surveySections as $section)
                                <option value="{{ $section->id }}">{{ $section->status_name }}</option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
                <div class="flex items-center gap-2 w-full md:w-auto shrink-0 justify-end">
                    @if ($search || $searchBatch || $searchSection)
                        <span title="{{ __('Reset all filters') }}">
                            <flux:button wire:click="$set('search', ''); $set('searchBatch', ''); $set('searchSection', '');"
                                variant="ghost" size="sm" icon="x-mark">
                                {{ __('Clear') }}
                            </flux:button>
                        </span>
                    @endif
                    <div wire:loading wire:target="search,searchBatch,searchSection,viewType" class="shrink-0">
                        <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                    </div>
                </div>
            </div>
        </div>

        @if ($viewType === 'table')
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                        {{ __('Showing') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $gradingScales->firstItem() }}</span>
                        {{ __('to') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $gradingScales->lastItem() }}</span>
                        {{ __('of') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $gradingScales->total() }}</span>
                        {{ __('results') }}
                    </p>
                </div>
            </div>

            {{-- Mobile Cards View --}}
            <div class="md:hidden divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($gradingScales as $scale)
                    <div class="p-4 space-y-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ $scale->evaluation }}</span>
                                <span class="text-xs text-zinc-500">{{ $scale->typeRel->status_name ?? '-' }}</span>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-medium text-zinc-900 dark:text-white">{{ $scale->from_percentage }}% - {{ $scale->to_percentage }}%</span>
                                <span class="text-[10px] text-zinc-500">{{ __('Batch') }}: {{ $scale->batch_no }}</span>
                            </div>
                        </div>
                        
                        <div class="text-xs text-zinc-600 dark:text-zinc-300">
                            <span class="font-medium">{{ __('Section') }}:</span> {{ $scale->surveyForSection->status_name ?? '-' }}
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-700">
                            <span title="{{ __('Edit scale entry') }}">
                                <flux:button href="{{ route('survey.grading.scale.edit', $scale) }}" wire:navigate
                                    variant="ghost" size="xs" icon="pencil-square" />
                            </span>
                            @can('survey.manage')
                                <span title="{{ __('Delete scale entry') }}">
                                    <flux:button wire:click="delete({{ $scale->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this grading scale?') }}"
                                        variant="ghost" size="xs" icon="trash" class="text-red-500" />
                                </span>
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-zinc-500 italic">
                        {{ __('No grading scales found.') }}
                    </div>
                @endforelse
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th wire:click="sortBy('from_percentage')"
                                class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                {{ __('From %') }}
                            </th>
                            <th wire:click="sortBy('to_percentage')"
                                class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                {{ __('To %') }}
                            </th>
                            <th wire:click="sortBy('evaluation')"
                                class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                {{ __('Evaluation') }}
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Batch') }}
                            </th>

                            <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Grading Type') }}
                        </th>

                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Section') }}
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($gradingScales as $scale)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                    {{ $scale->from_percentage }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                    {{ $scale->to_percentage }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $scale->evaluation }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $scale->batch_no }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $scale->typeRel->status_name ?? '-' }}
                                </td>
                                <td class="px-6 py-4  whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $scale->surveyForSection->status_name ?? '-'  }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <span title="{{ __('Edit') }}">
                                            <flux:button href="{{ route('survey.grading.scale.edit', $scale) }}" wire:navigate
                                                variant="ghost" size="sm" icon="pencil-square" />
                                        </span>
                                        @can('survey.manage')
                                            <span title="{{ __('Delete') }}">
                                                <flux:button wire:click="delete({{ $scale->id }})"
                                                    wire:confirm="{{ __('Are you sure you want to delete this grading scale?') }}"
                                                    variant="ghost" size="sm" icon="trash"
                                                    class="text-red-500 hover:text-red-600" />
                                            </span>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-zinc-500">
                                    {{ __('No grading scales found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $gradingScales->links() }}
            </div>
        @else
            {{-- Tree Structure View --}}
            <div class="p-5 md:p-6 space-y-4">
                @forelse($gradingScalesTree as $sectionItem)
                    @php
                        $sectionId = 'section_' . $sectionItem['section_id'];
                    @endphp
                    <!-- Section Node -->
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-xs overflow-hidden transition-all duration-300 hover:shadow-md">
                        <!-- Section Header -->
                        <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 cursor-pointer select-none bg-zinc-50/50 dark:bg-zinc-800/40 hover:bg-zinc-100/50 dark:hover:bg-zinc-700/40 transition-colors"
                             x-on:click="toggle('{{ $sectionId }}')">
                            
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-blue-50 dark:bg-blue-950/50 rounded-lg text-blue-600 dark:text-blue-400 shrink-0">
                                    <flux:icon name="building-office-2" class="size-6" />
                                </div>
                                <div class="space-y-1">
                                    <flux:heading level="3" class="text-zinc-950 dark:text-white font-semibold text-base">
                                        {{ $sectionItem['section_name'] }}
                                    </flux:heading>
                                    <div class="text-xs text-zinc-500">
                                        {{ __('Section ID') }}: {{ $sectionItem['section_id'] }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 self-start md:self-center">
                                <flux:icon name="chevron-down" class="size-5 text-zinc-400 transition-transform duration-300" x-bind:class="expanded['{{ $sectionId }}'] ? 'rotate-180' : ''" />
                            </div>
                        </div>

                        <!-- Section Content (Batches) -->
                        <div x-show="expanded['{{ $sectionId }}']" x-collapse>
                            <div class="p-5 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900/30">
                                <!-- Vertical connector line on the left side for LTR -->
                                <div class="relative ml-4 pl-6 border-l-2 border-dashed border-zinc-200 dark:border-zinc-700 space-y-6">
                                    @foreach($sectionItem['batches'] as $batchItem)
                                        @php
                                            $batchNo = $batchItem['batch_no'];
                                            $batchId = 'batch_' . $sectionItem['section_id'] . '_' . $batchNo;
                                        @endphp
                                        
                                        <!-- Batch Node -->
                                        <div class="relative">
                                            <!-- Connect point indicator on the left -->
                                            <div class="absolute left-[-31px] top-[14px] size-3.5 rounded-full border-2 border-white dark:border-zinc-950 bg-blue-500 shadow-xs"></div>
                                            
                                            <!-- Batch Header -->
                                            <div class="flex items-center gap-2.5 p-2 px-3 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700/80 rounded-lg cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700 w-fit select-none transition-colors"
                                                 x-on:click="toggle('{{ $batchId }}')">
                                                <flux:icon name="users" class="size-4.5 text-blue-500" />
                                                <span class="font-semibold text-sm text-zinc-800 dark:text-zinc-200">
                                                    {{ __('Batch') }} {{ $batchItem['batch_no'] }}
                                                </span>
                                                <flux:icon name="chevron-down" class="size-4 text-zinc-400 transition-transform duration-200" x-bind:class="expanded['{{ $batchId }}'] ? 'rotate-180' : ''" />
                                            </div>

                                            <!-- Batch Content (Grading Scales list) -->
                                            <div x-show="expanded['{{ $batchId }}']" x-collapse class="mt-4 ml-4 pl-6 border-l-2 border-dotted border-zinc-200 dark:border-zinc-700 space-y-6">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                                                    @foreach($batchItem['scales'] as $scale)
                                                        @php
                                                            $evalClasses = match($scale->evaluation) {
                                                                'جيد جداً', 'جيد جدا' => 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-300 border-emerald-100 dark:border-emerald-900',
                                                                'جيد' => 'bg-sky-50 dark:bg-sky-950/20 text-sky-700 dark:text-sky-300 border-sky-100 dark:border-sky-900',
                                                                'هشاشة', 'هشاشة نفسية واضحة', 'ضعيف' => 'bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-300 border-rose-100 dark:border-rose-900',
                                                                default => 'bg-zinc-50 dark:bg-zinc-950/20 text-zinc-700 dark:text-zinc-300 border-zinc-100 dark:border-zinc-900'
                                                            };
                                                        @endphp
                                                        
                                                        <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-lg space-y-4 shadow-2xs hover:shadow-xs transition-shadow">
                                                            <div class="flex items-center justify-between gap-2">
                                                                <span class="text-xs font-mono font-extrabold text-zinc-500 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-800/80 px-2 py-0.5 rounded border border-zinc-200/40 dark:border-zinc-700/40">
                                                                    {{ $scale->from_percentage }}% - {{ $scale->to_percentage }}%
                                                                </span>
                                                                <div class="flex items-center gap-2">
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $evalClasses }} border">
                                                                        {{ $scale->evaluation }}
                                                                    </span>
                                                                    <span title="{{ __('Edit') }}">
                                                                        <flux:button href="{{ route('survey.grading.scale.edit', $scale) }}" wire:navigate
                                                                            variant="ghost" size="xs" icon="pencil-square" />
                                                                    </span>
                                                                    @can('survey.manage')
                                                                        <span title="{{ __('Delete') }}">
                                                                            <flux:button wire:click="delete({{ $scale->id }})"
                                                                                wire:confirm="{{ __('Are you sure you want to delete this grading scale?') }}"
                                                                                variant="ghost" size="xs" icon="trash"
                                                                                class="text-red-500 hover:text-red-600" />
                                                                        </span>
                                                                    @endcan
                                                                </div>
                                                            </div>

                                                            @if($scale->description)
                                                                <div class="text-xs text-zinc-600 dark:text-zinc-300 italic bg-zinc-50 dark:bg-zinc-800/40 p-3 rounded-lg border border-zinc-200/20 dark:border-zinc-700/20">
                                                                    <span class="text-zinc-400 font-normal block not-italic text-[9px] uppercase tracking-wider mb-0.5">{{ __('Scale Description') }}:</span>
                                                                    {{ $scale->description }}
                                                                </div>
                                                            @endif

                                                            <div class="flex flex-wrap gap-x-3 gap-y-1.5 text-[11px] text-zinc-500 pt-0.5">
                                                                @if($scale->typeRel)
                                                                    <div>
                                                                        <span class="text-zinc-400">{{ __('Scale Type') }}:</span>
                                                                        <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $scale->typeRel->status_name }}</span>
                                                                    </div>
                                                                @endif
                                                                @if($scale->batch_no)
                                                                    <div>
                                                                        <span class="text-zinc-400">{{ __('Batch') }}:</span>
                                                                        <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $scale->batch_no }}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-8 text-center text-zinc-500 dark:text-zinc-400 italic">
                        {{ __('No grading scales found matching the search filters.') }}
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</div>
