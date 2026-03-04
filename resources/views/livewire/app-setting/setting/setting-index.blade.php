<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between p-6">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('App Settings') }}</flux:heading>
            <flux:subheading>{{ __('Manage and search system-wide configuration keys and values.') }}</flux:subheading>
        </div>
        @can('setting.create')
        <flux:button href="{{ route('setting.create') }}" wire:navigate variant="primary" icon="plus">
            {{ __('Create New Setting') }}
        </flux:button>  
        @endcan
       
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Search & Filter Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6 mx-6">
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Search & Filter') }}</h2>

        <div class="max-w-md">
            {{-- Search by Key/Value/Description --}}
            <div class="relative">
                <flux:input wire:model.live.debounce.300ms="search" :label="__('Search Settings')" type="text"
                    :placeholder="__('Search by key, value or description...')" icon="magnifying-glass" />
                <div wire:loading wire:target="search" class="absolute right-3 top-[2.4rem]">
                    <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                </div>
            </div>
        </div>

        {{-- Clear Filters --}}
        @if ($search)
            <div class="mt-4 flex items-center justify-start">
                <flux:button wire:click="$set('search', '')" variant="ghost" size="sm" icon="x-mark">
                    {{ __('Clear Search') }}
                </flux:button>
            </div>
        @endif
    </div>

    {{-- Data Table Section --}}
    @if ($settings->count() > 0)
        <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm mx-6 mb-10">
            {{-- Table Header Info --}}
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                        {{ __('Showing') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $settings->firstItem() }}</span>
                        {{ __('to') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $settings->lastItem() }}</span>
                        {{ __('of') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $settings->total() }}</span>
                        {{ __('results') }}
                    </p>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                #
                            </th>
                            <th wire:click="sortBy('key')" class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                <div class="flex items-center gap-1">
                                    {{ __('Setting Key') }}
                                    @if ($sortField === 'key')
                                        <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="size-3" />
                                    @else
                                        <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('value')" class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                <div class="flex items-center gap-1">
                                    {{ __('Primary Value') }}
                                    @if ($sortField === 'value')
                                        <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="size-3" />
                                    @else
                                        <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                    @endif
                                </div>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Description') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($settings as $index => $setting)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $settings->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-sm font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ $setting->key }}
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="text-sm text-zinc-900 dark:text-white max-w-sm truncate">
                                        {{ $setting->value }}
                                    </div>
                                    @if($setting->value_array)
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach(collect($setting->value_array)->take(3) as $subVal)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300">
                                                    {{ Str::limit($subVal, 20) }}
                                                </span>
                                            @endforeach
                                            @if(count($setting->value_array) > 3)
                                                <span class="text-[10px] text-zinc-400">+{{ count($setting->value_array) - 3 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    <div class="text-sm text-zinc-600 dark:text-zinc-300 max-w-xs truncate">
                                        {{ $setting->description ?? __('No description') }}
                                    </div>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('setting.create')
                                        <flux:button size="sm" variant="danger" icon="trash" wire:click="destroy({{ $setting->id }})" wire:confirm="{{ __('Are you sure you want to delete this setting?') }}">
                                            {{ __('Delete') }}
                                        </flux:button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                {{ $settings->links() }}
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-20 px-4 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 mx-6">
            <div class="flex items-center justify-center w-20 h-20 rounded-full bg-indigo-50 dark:bg-zinc-900/50 mb-4 border border-indigo-100 dark:border-zinc-800">
                <flux:icon.cpu-chip class="w-10 h-10 text-indigo-600 dark:text-indigo-400" />
            </div>
            <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-2">
                {{ __('No Settings Found') }}
            </h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 text-center mb-6 max-w-sm">
                @if ($search)
                    {{ __('No settings match your search criteria. Try adjusting your filters.') }}
                @else
                    {{ __('You haven\'t defined any system settings yet. Get started by creating one.') }}
                @endif
            </p>
            @if ($search)
                <flux:button wire:click="$set('search', '')" variant="primary" icon="x-mark">
                    {{ __('Clear Search') }}
                </flux:button>
            @else
                <flux:button href="{{ route('setting.create') }}" wire:navigate variant="primary" icon="plus">
                    {{ __('Create New Setting') }}
                </flux:button>
            @endif
        </div>
    @endif
</div>
