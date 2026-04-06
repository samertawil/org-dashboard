<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Survey Grading Scales') }}</flux:heading>
            <flux:subheading>{{ __('Manage the evaluation scales for surveys based on score percentages.') }}
            </flux:subheading>
        </div>
        @can('survey.manage')
            <flux:button href="{{ route('survey.grading.scale.create') }}" wire:navigate variant="primary" icon="plus">
                {{ __('Add Grading Scale') }}
            </flux:button>
        @endcan
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Search and Table Section --}}
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <flux:input wire:model.live="search" :placeholder="__('Search by evaluation or description...')"
                    icon="magnifying-glass" />
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
                    <flux:button wire:click="$set('search', ''); $set('searchBatch', ''); $set('searchSection', '');"
                        variant="ghost" size="sm" icon="x-mark">
                        {{ __('Clear Filters') }}
                    </flux:button>
                </div>
            @endif
            <div wire:loading wire:target="search,searchBatch,searchSection" class="shrink-0 pb-2">
                <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
            </div>
        </div>

        <div class="overflow-x-auto">
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
                            {{ __('Section') }}
                        </th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->gradingScales as $scale)
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
                                {{ $scale->survey_for_section ? \App\Models\Status::find($scale->survey_for_section)->status_name ?? '-' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button href="{{ route('survey.grading.scale.edit', $scale) }}" wire:navigate
                                        variant="ghost" size="sm" icon="pencil-square" />
                                    <flux:button wire:click="delete({{ $scale->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this grading scale?') }}"
                                        variant="ghost" size="sm" icon="trash"
                                        class="text-red-500 hover:text-red-600" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-zinc-500">
                                {{ __('No grading scales found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $this->gradingScales->links() }}
        </div>
    </div>
</div>
