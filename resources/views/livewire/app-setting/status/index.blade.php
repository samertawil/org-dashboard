<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading ?? 'Status List' }}</flux:heading>
            <flux:subheading>{{ $subheading ?? 'Details for your new Status below.' }}</flux:subheading>
        </div>

        <flux:button href="{{ route('status.create') }}" wire:navigate variant="primary" icon="list-bullet">
            {{ __('Create Status Name') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />



    {{-- Search & Filter Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Search & Filter') }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Search by Name --}}
            <div class="relative">
                <flux:input wire:model.live.debounce.300ms="search" :label="__('Search by Name')" type="text"
                    :placeholder="__('Search status name...')" icon="magnifying-glass" />
                <div wire:loading wire:target="search" class="absolute right-3 top-[2.4rem]">
                    <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                </div>
            </div>

            {{-- Filter by Parent Status --}}
            <div class="relative">
                <flux:select wire:model.live="searchParentStatus" :label="__('Filter by Parent Status')"
                    :placeholder="__('All parent statuses')">
                    <option value="">{{ __('All Parent Statuses') }}</option>
                    @foreach ($this->getParentStatuses as $status)
                        <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                    @endforeach
                </flux:select>
                <div wire:loading wire:target="searchParentStatus" class="absolute right-8 top-[2.4rem]">
                    <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                </div>
            </div>

            {{-- Filter by System Name --}}
            <div class="relative">
                <flux:select wire:model.live="searchSystemName" :label="__('Filter by System')"
                    :placeholder="__('All systems')">
                    <option value="">{{ __('All Systems') }}</option>
                    @foreach ($this->systemNames as $system)
                        <option value="{{ $system->id }}">{{ $system->system_name }}</option>
                    @endforeach
                </flux:select>
                <div wire:loading wire:target="searchSystemName" class="absolute right-8 top-[2.4rem]">
                    <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                </div>
            </div>
        </div>

        {{-- Clear Filters --}}
        @if ($search || $searchParentStatus || $searchSystemName)
            <div class="mt-4 flex items-center justify-end">
                <flux:button
                    wire:click="$set('search', ''); $set('searchParentStatus', ''); $set('searchSystemName', '')"
                    variant="ghost" size="sm" icon="x-mark">
                    {{ __('Clear Filters') }}
                </flux:button>
            </div>
        @endif
    </div>

    {{-- Data Table Section --}}
    @if ($statuses->count() > 0)
        <div
            class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm">
            {{-- Table Header Info --}}
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                        {{ __('Showing') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $statuses->firstItem() }}</span>
                        {{ __('to') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $statuses->lastItem() }}</span>
                        {{ __('of') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $statuses->total() }}</span>
                        {{ __('results') }}
                    </p>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                #
                            </th>
                            <th wire:click="sortBy('status_name')"
                                class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                <div class="flex items-center gap-1">
                                    {{ __('Status Name') }}
                                    @if ($sortField === 'status_name')
                                        <flux:icon
                                            name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                            class="size-3" />
                                    @else
                                        <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                    @endif
                                </div>
                            </th>


                            <th wire:click="sortBy('p_id_sub')"
                                class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                <div class="flex items-center gap-1">
                                    {{ __('Parent Status') }}
                                    @if ($sortField === 'p_id_sub')
                                        <flux:icon
                                            name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                            class="size-3" />
                                    @else
                                        <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                    @endif
                                </div>
                            </th>

                            <th wire:click="sortBy('used_in_system_id')"
                                class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                <div class="flex items-center gap-1">
                                    {{ __('System Name') }}
                                    @if ($sortField === 'used_in_system_id')
                                        <flux:icon
                                            name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                            class="size-3" />
                                    @else
                                        <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                    @endif
                                </div>
                            </th>

                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Description') }}
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($statuses as $index => $status)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $statuses->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                            {{ $status->status_name }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-sm text-zinc-600 dark:text-zinc-300">
                                        @if ($status->status_p_id_sub)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $status->status_p_id_sub->status_name }}
                                            </span>
                                        @else
                                            <span class="text-zinc-400 dark:text-zinc-500">{{ __('Root') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-sm text-zinc-600 dark:text-zinc-300">
                                        @if ($status->systemname)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                {{ $status->systemname->system_name }}
                                            </span>
                                        @else
                                            <span class="text-zinc-400 dark:text-zinc-500">{{ __('N/A') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="text-sm text-zinc-600 dark:text-zinc-300 max-w-xs truncate">
                                        {{ $status->description ?? __('No description') }}
                                    </div>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button size="sm" variant="ghost" icon="pencil"
                                            :href="route('status.edit', $status)" wire:navigate>
                                            {{ __('Edit') }}
                                        </flux:button>
                                        <flux:button size="sm" variant="danger" icon="trash">
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                {{ $statuses->links() }}
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div
            class="flex flex-col items-center justify-center py-12 px-4 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-700 mb-4">
                <flux:icon.document-text class="w-8 h-8 text-zinc-400 dark:text-zinc-500" />
            </div>
            <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">
                {{ __('No Statuses Found') }}
            </h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 text-center mb-6 max-w-sm">
                @if ($search || $searchParentStatus || $searchSystemName)
                    {{ __('No statuses match your search criteria. Try adjusting your filters.') }}
                @else
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 text-center mb-6 max-w-sm">
                        {{ __('Get started by creating your first status using the form above.') }}
                    </p>
                    <flux:button href="{{ route('status.create') }}" wire:navigate variant="primary" icon="plus">
                        {{ __('Create Status Name') }}
                    </flux:button>
                @endif
            </p>
            @if ($search || $searchParentStatus || $searchSystemName)
                <flux:button
                    wire:click="$set('search', ''); $set('searchParentStatus', ''); $set('searchSystemName', '')"
                    variant="primary" icon="x-mark">
                    {{ __('Clear Filters') }}
                </flux:button>
            @endif
        </div>
    @endif
</div>
