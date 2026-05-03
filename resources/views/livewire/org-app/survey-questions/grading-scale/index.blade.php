<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Survey Grading Scales') }}</flux:heading>
            <flux:subheading>{{ __('Manage the evaluation scales for surveys based on score percentages.') }}
            </flux:subheading>
        </div>
        @can('survey.manage')
            <span title="{{ __('Add a new evaluation scale entry') }}" class="w-full sm:w-auto">
                <flux:button href="{{ route('survey.grading.scale.create') }}" wire:navigate variant="primary" icon="plus" class="w-full">
                    {{ __('Add Grading Scale') }}
                </flux:button>
            </span>
        @endcan
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
                    <div wire:loading wire:target="search,searchBatch,searchSection" class="shrink-0">
                        <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                    </div>
                </div>
            </div>
        </div>

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
    </div>
</div>
