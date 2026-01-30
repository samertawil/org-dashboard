<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading ?? 'Roles List' }}</flux:heading>
            <flux:subheading>{{ $subheading ?? 'Details for your Roles below.' }}</flux:subheading>
        </div>

        <flux:button href="{{ route('role.create') }}" wire:navigate variant="primary" icon="list-bullet">
            {{ __('Create New Role') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />



    {{-- Search & Filter Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Search & Filter') }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Search by Name --}}
            <flux:input wire:model.live.debounce.300ms="search" :label="__('Search by ability Name')" type="text"
                :placeholder="__('Search ability name...')" icon="magnifying-glass" />



        </div>

        {{-- Clear Filters --}}
        @if ($search)
            <div class="mt-4 flex items-center justify-end">
                <flux:button wire:click="$set('search', ''); " variant="ghost" size="sm" icon="x-mark">
                    {{ __('Clear Filters') }}
                </flux:button>
            </div>
        @endif
    </div>

    {{-- Clear Filters --}}
    @if ($search)
        <div class="mt-4 flex items-center justify-end">
            <button wire:click="$set('search', '')" type="button"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800">
                <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
                {{ __('Clear Filters') }}
            </button>
        </div>
    @endif

    {{-- Data Table Section --}}
    <div class="w-full">
        @if ($roles->count() > 0)
            <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm">
                {{-- Table Header Info --}}
                <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                            {{ __('Showing') }}
                            <span class="font-medium text-zinc-900 dark:text-white">{{ $roles->firstItem() }}</span>
                            {{ __('to') }}
                            <span class="font-medium text-zinc-900 dark:text-white">{{ $roles->lastItem() }}</span>
                            {{ __('of') }}
                            <span class="font-medium text-zinc-900 dark:text-white">{{ $roles->total() }}</span>
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
                                <th wire:click="sortBy('name')"
                                    class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                    <div class="flex items-center gap-1">
                                        {{ __('Role Name') }}
                                        @if ($sortBy === 'name')
                                            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M{{ $sortDir === 'asc' ? '4.5 15.75l7.5-7.5 7.5 7.5' : '19.5 8.25l-7.5 7.5-7.5-7.5' }}" />
                                            </svg>
                                        @else
                                            <svg class="size-3 text-zinc-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5-9L16.5 3m0 0L12 7.5m4.5-4.5v13.5" />
                                            </svg>
                                        @endif
                                    </div>
                                </th>
                                <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Abilities') }}
                            </th>
                                <th wire:click="sortBy('created_at')"
                                    class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                    <div class="flex items-center gap-1">
                                        {{ __('Created At') }}
                                        @if ($sortBy === 'created_at')
                                            <svg class="size-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M{{ $sortDir === 'asc' ? '4.5 15.75l7.5-7.5 7.5 7.5' : '19.5 8.25l-7.5 7.5-7.5-7.5' }}" />
                                            </svg>
                                        @else
                                            <svg class="size-3 text-zinc-300" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 7.5L7.5 3m0 0L12 7.5M7.5 3v13.5m13.5-9L16.5 3m0 0L12 7.5m4.5-4.5v13.5" />
                                            </svg>
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
                            @foreach ($roles as $index => $role)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                        {{ $roles->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                                {{ $role->name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td >{{ implode(',', $role->abilities_description) }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                        {{ $role->created_at->format('Y-m-d H:i:s') }}
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                           
                                            <a href="{{ route('role.edit', $role->id) }}" wire:navigate
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-indigo-900 dark:text-indigo-200 dark:hover:bg-indigo-800">
                                                <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path
                                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.38-2.827-2.828z" />
                                                </svg>
                                                {{ __('Edit') }}
                                            </a>
                                            <button wire:click="destroy({{ $role->id }})"
                                                wire:confirm="{{ __('customTrans.confirm delete') }}"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800">
                                                <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm6 0a1 1 0 012 0v6a1 1 0 11-2 0V8z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                {{ __('Delete') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                    {{ $roles->links() }}
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div
                class="flex flex-col items-center justify-center py-12 px-4 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">

                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-700 mb-4">
                    <svg class="w-8 h-8 text-zinc-400 dark:text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m5.23 6.342l-2.736-2.736m2.736 2.736L6.5 18.75m3.75-9.375h.008v.008h-.008v-.008zm1.5 0h.008v.008h-.008v-.008zm1.5 0h.008v.008h-.008v-.008z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">
                    {{ __('No Roles Found') }}
                </h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 text-center mb-6 max-w-sm">
                    @if ($search)
                        {{ __('No roles match your search criteria. Try adjusting your filters.') }}
                    @else
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 text-center mb-6 max-w-sm">
                            {{ __('Get started by creating your first role.') }}
                        </p>
                        <a href="{{ route('role.create') }}" wire:navigate
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ __('Create Role') }}
                        </a>
                    @endif
                </p>
                @if ($search)
                <flux:button
                wire:click="$set('search', '');"
                variant="primary" icon="x-mark">
                {{ __('Clear Filters') }}
            </flux:button>
                @endif
            </div>
        @endif
    </div>
</div>