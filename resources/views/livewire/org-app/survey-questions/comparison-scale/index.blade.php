<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Survey Comparison Scales') }}</flux:heading>
            <flux:subheading>{{ __('Manage the smart evaluation rules for progress based on percentage differences.') }}
            </flux:subheading>
        </div>
        @can('survey.manage')
            <flux:button href="{{ route('org-app.survey-questions.comparison-scale.create') }}" wire:navigate variant="primary" icon="plus">
                {{ __('Add Comparison Scale') }}
            </flux:button>
        @endcan
    </div>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Search and Table Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <flux:input wire:model.live="search" :placeholder="__('Search by evaluation or description...')" icon="magnifying-glass" />
            </div>
            <div class="flex-1 w-full">
                <flux:select wire:model.live="searchBatch" :placeholder="__('Filter by Batch...')">
                    <option value="">{{ __('All Batches') }}</option>
                    @foreach ($batches as $batch)
                        <option value="{{ $batch->batch_no }}">{{ __('Batch') }} {{ $batch->batch_no }}</option>
                    @endforeach
                </flux:select>
            </div>
            <div class="flex-1 w-full">
                <flux:select wire:model.live="searchSection" :placeholder="__('Filter by Section...')">
                    <option value="">{{ __('All Sections') }}</option>
                    @foreach ($surveySections as $section)
                        <option value="{{ $section->id }}">{{ $section->status_name }}</option>
                    @endforeach
                </flux:select>
            </div>
            @if ($search || $searchBatch || $searchSection)
                <div class="shrink-0 pb-1">
                    <flux:button wire:click="$set('search', ''); $set('searchBatch', ''); $set('searchSection', '');" variant="ghost" size="sm" icon="x-mark">
                        {{ __('Clear Filters') }}
                    </flux:button>
                </div>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th wire:click="sortBy('from_percentage')" class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 uppercase cursor-pointer hover:text-indigo-600 transition-colors">
                            {{ __('From Diff %') }}
                        </th>
                        <th wire:click="sortBy('to_percentage')" class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 uppercase cursor-pointer hover:text-indigo-600 transition-colors">
                            {{ __('To Diff %') }}
                        </th>
                        <th wire:click="sortBy('evaluation')" class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 uppercase cursor-pointer hover:text-indigo-600 transition-colors">
                            {{ __('Evaluation') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                            {{ __('Domain') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 uppercase tracking-wider">
                            {{ __('Batch') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->comparisonScales as $scale)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $scale->from_percentage }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-900 dark:text-white">
                                {{ $scale->to_percentage }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-bold rounded" style="background-color: {{ $scale->color }}20; color: {{ $scale->color }};">
                                    {{ $scale->evaluation }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $scale->domain->status_name ?? __('General / Total') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $scale->batch_no ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button href="{{ route('org-app.survey-questions.comparison-scale.edit', $scale->id) }}" wire:navigate variant="ghost" size="sm" icon="pencil-square" />
                                    <flux:button wire:click="delete({{ $scale->id }})" wire:confirm="{{ __('Are you sure you want to delete this scale?') }}" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-zinc-500">
                                {{ __('No comparison scales found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $this->comparisonScales->links() }}
        </div>
    </div>
</div>
