<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Departments') }}</flux:heading>
            <flux:subheading>{{ __('Manage your organization departments.') }}</flux:subheading>
        </div>
        
        <flux:button 
            href="{{ route('department.create') }}" 
            wire:navigate 
            variant="primary"
            icon="plus"
        >
            {{ __('Create Department') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Search and Table Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 relative">
            <flux:input 
                wire:model.live="search" 
                :placeholder="__('Search departments Or Locations...')" 
                icon="magnifying-glass"
            />
            <div wire:loading wire:target="search" class="absolute right-6 top-1/2 -translate-y-1/2">
                <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
            </div>
        </div>

        @if ($search)
            <div class="p-4 flex items-center justify-end border-b border-zinc-200 dark:border-zinc-700">
                <flux:button wire:click="$set('search', '');" variant="ghost" size="sm" icon="x-mark">
                    {{ __('Clear Filters') }}
                </flux:button>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Name') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Location') }}
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
                    @forelse($departments as $department)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $department->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $department->location }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ Str::limit($department->description, 50)??'-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button 
                                        href="{{ route('department.edit', $department) }}" 
                                        wire:navigate 
                                        variant="ghost" 
                                        size="sm" 
                                        icon="pencil-square" 
                                    />
                                    <flux:button 
                                        wire:click="delete({{ $department->id }})" 
                                        wire:confirm="{{ __('Are you sure you want to delete this department?') }}"
                                        variant="ghost" 
                                        size="sm" 
                                        icon="trash" 
                                        class="text-red-500 hover:text-red-600"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-zinc-500">
                                {{ __('No departments found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $departments->links() }}
        </div>
    </div>
</div>
