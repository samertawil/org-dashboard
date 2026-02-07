<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{$heading ?? 'System Names'}}</flux:heading>
            <flux:subheading>{{$subheading ?? 'Details for your new Systems list below.'}}</flux:subheading>
        </div>
        
        <flux:button 
            href="{{ route('system.names.create') }}" 
            wire:navigate 
            variant="primary"
            icon="plus"
        >
            {{ __('Create System Name') }}
        </flux:button>
    </div>
    {{-- Search and Filter Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Search & Filter') }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="relative">
                <flux:input wire:model.live.debounce.300ms="search" :label="__('Search by System Name')" type="text"
                    :placeholder="__('Search name...')" icon="magnifying-glass" />
                <div wire:loading wire:target="search" class="absolute right-3 top-[2.4rem]">
                    <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                </div>
            </div>
        </div>

        {{-- Clear Filters --}}
        @if ($search)
            <div class="mt-4 flex items-center justify-end">
                <flux:button wire:click="$set('search', '')" variant="ghost" size="sm" icon="x-mark">
                    {{ __('Clear Filters') }}
                </flux:button>
            </div>
        @endif
    </div>


    <!-- Success Message -->
    <x-auth-session-status class="text-center" :status="session('message')" />

    @php
        $systemNames = $this->index();
    @endphp

    @if($systemNames->count() > 0)
        <!-- Data Table -->
        <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                #
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('System Name') }}
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
                        @foreach($systemNames as $index => $systemName)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-zinc-900 dark:text-white">
                                        {{ $systemName->system_name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-zinc-600 dark:text-zinc-300 max-w-md">
                                        {{ $systemName->description ?? __('No description') }}
                                    </div>
                                </td>
                              
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button 
                                            size="sm" 
                                            variant="ghost"
                                            icon="pencil"
                                        >
                                            {{ __('Edit') }}
                                        </flux:button>
                                        <flux:button 
                                            size="sm" 
                                            variant="danger"
                                            icon="trash"
                                        >
                                            {{ __('Delete') }}
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center py-12 px-4 bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-700 mb-4">
                <flux:icon.document-text class="w-8 h-8 text-zinc-400 dark:text-zinc-500" />
            </div>
            <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">
                {{ __('No System Names Found') }}
            </h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 text-center mb-6 max-w-sm">
                {{ __('Get started by creating your first system name.') }}
            </p>
            <flux:button 
                href="{{ route('system.names.create') }}" 
                wire:navigate 
                variant="primary"
                icon="plus"
            >
                {{ __('Create System Name') }}
            </flux:button>
        </div>
    @endif
</div>
