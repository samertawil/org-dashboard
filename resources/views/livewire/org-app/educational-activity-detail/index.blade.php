<div>
    <div class="flex justify-between items-center mb-6">
        <flux:heading level="1" size="xl">{{ __('Educational Activity Report') }}</flux:heading>
        
        <div class="flex gap-2 items-center">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search...') }}" icon="magnifying-glass" class="w-64" />
            @can('educational-activity-detail.create')
            <flux:button href="{{ route('educational-activity-detail.create') }}" wire:navigate variant="primary" icon="plus">
                {{ __('Create New') }}
            </flux:button>   
            @endcan
           
        </div>
    </div>

    <x-auth-session-status class="mb-4 text-center {{ session('type') == 'error' ? 'text-red-500' : '' }}" :status="session('message')" />

    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-zinc-500 dark:text-zinc-400">
                <thead class="text-xs text-zinc-700 uppercase bg-zinc-50 dark:bg-zinc-700 dark:text-zinc-300">
                    <tr>
                        <th scope="col" class="px-6 py-3">{{ __('Activity Name') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('What Learned') }}</th>
                        <th scope="col" class="px-6 py-3">{{ __('Teacher Report') }}</th>
                        <th scope="col" class="px-6 py-3 text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($details as $detail)
                        <tr wire:key="detail-{{ $detail->id }}" class="bg-white border-b dark:bg-zinc-800 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-600 transition-colors">
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white whitespace-nowrap">
                                {{ $detail->educationalActivity?->activity_name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ str($detail->what_learned)->limit(50) }}
                            </td>
                            <td class="px-6 py-4">
                                {{ str($detail->teacher_report_detail)->limit(50) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <flux:button href="{{ route('educational-activity-detail.show', $detail->id) }}" wire:navigate size="sm" variant="ghost" icon="eye" class="text-zinc-500 hover:text-zinc-700" title="{{ __('View') }}" />
                                    <flux:button href="{{ route('educational-activity-detail.edit', $detail->id) }}" wire:navigate size="sm" variant="ghost" icon="pencil-square" class="text-blue-500 hover:text-blue-700" title="{{ __('Edit') }}" />
                                    <flux:button href="{{ route('educational-activity-detail.gallery', $detail->id) }}" wire:navigate size="sm" variant="ghost" icon="photo" class="text-green-500 hover:text-green-700" title="{{ __('Gallery') }}" />
                                        @can('educational-activity-detail.create')
                                    <flux:button wire:click="delete({{ $detail->id }})" wire:confirm="{{ __('Are you sure you want to delete this record?') }}" size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-700" title="{{ __('Delete') }}" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-zinc-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon icon="inbox" class="size-8 text-zinc-400" />
                                    <p>{{ __('No records found.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $details->links() }}
        </div>
    </div>
</div>
