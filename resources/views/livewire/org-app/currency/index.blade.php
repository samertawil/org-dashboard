<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Currency Values') }}</flux:heading>
            <flux:subheading>{{ __('Manage your exchange rates') }} <span style="color:blue">, Exchange rate of the US
                    dollar against the Israeli shekel</span></flux:subheading>

        </div>
        @can('currency.create')
            <flux:button href="{{ route('currency.create') }}" wire:navigate variant="primary" icon="plus">
                {{ __('Add Currency Value') }}
            </flux:button>
        @endcan

    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Search and Table Section --}}
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 relative">
            <flux:input wire:model.live="search" :placeholder="__('Search by date as yyyy-mm-dd or value...')"
                icon="magnifying-glass" />
            <div wire:loading wire:target="search" class="absolute right-6 top-1/2 -translate-y-1/2">
                <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
            </div>


            @if ($search)
                <div class="mt-4 flex items-center justify-end">
                    <flux:button wire:click="$set('search', '');" variant="ghost" size="sm" icon="x-mark">
                        {{ __('Clear Filters') }}
                    </flux:button>
                </div>
            @endif
        </div>
        <div class="overflow-x-auto">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                        {{ __('Showing') }}
                        <span
                            class="font-medium text-zinc-900 dark:text-white">{{ $this->currencies->firstItem() }}</span>
                        {{ __('to') }}
                        <span
                            class="font-medium text-zinc-900 dark:text-white">{{ $this->currencies->lastItem() }}</span>
                        {{ __('of') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $this->currencies->total() }}</span>
                        {{ __('results') }}
                    </p>
                </div>
            </div>
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th wire:click="sortBy('exchange_date')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Exchange Date') }}
                                @if ($sortField === 'exchange_date')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('currency_value')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Currency Value') }}
                                @if ($sortField === 'currency_value')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('created_at')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Created Date') }}
                                @if ($sortField === 'created_at')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->currencies as $currency)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $currency->exchange_date }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $currency->currency_value }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $currency->created_at }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button href="{{ route('currency.edit', $currency) }}" wire:navigate
                                        variant="ghost" size="sm" icon="pencil-square" />
                                    <flux:button wire:click="delete({{ $currency->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this currency value?') }}"
                                        variant="ghost" size="sm" icon="trash"
                                        class="text-red-500 hover:text-red-600" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-zinc-500">
                                {{ __('No currency values found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $this->currencies->links() }}
        </div>
    </div>
</div>
