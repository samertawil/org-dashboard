<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Educational Activity Names') }}</flux:heading>
            <flux:subheading>{{ __('Manage unique activity names, domains, availability, and assigned teachers.') }}</flux:subheading>
        </div>
        <span title="{{ __('Add new activity name') }}" class="w-full sm:w-auto">
            <flux:button href="{{ route('educational-activity-names.create') }}" wire:navigate variant="primary" icon="plus" class="w-full">
                {{ __('Add Activity') }}
            </flux:button>
        </span>
    </div>

    {{-- Session Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 p-4 rounded-lg text-center text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 relative">
            <flux:input wire:model.live="search" :placeholder="__('Search by name...')" icon="magnifying-glass" />
            <div wire:loading wire:target="search" class="absolute right-6 top-1/2 -translate-y-1/2">
                <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
            </div>
        </div>

        @if ($search)
            <div class="p-4 flex items-center justify-end border-b border-zinc-200 dark:border-zinc-700">
                <span title="{{ __('Reset search filters') }}">
                    <flux:button wire:click="$set('search', '');" variant="ghost" size="sm" icon="x-mark">
                        {{ __('Clear Filters') }}
                    </flux:button>
                </span>
            </div>
        @endif

        <div class="overflow-x-auto">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                        {{ __('Showing') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $activities->firstItem() ?? 0 }}</span>
                        {{ __('to') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $activities->lastItem() ?? 0 }}</span>
                        {{ __('of') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $activities->total() }}</span>
                        {{ __('results') }}
                    </p>
                </div>
            </div>

            {{-- Mobile View Cards --}}
            <div class="md:hidden divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($activities as $activity)
                    <div class="p-4 space-y-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ $activity->activity_name }}</span>
                                <span class="text-xs text-zinc-500">{{ $activity->domain?->status_name ?? '-' }}</span>
                            </div>
                            <flux:badge color="{{ $activity->activation == 1 ? 'green' : 'zinc' }}" size="sm">
                                {{ $activity->activation == 1 ? __('Active') : __('Inactive') }}
                            </flux:badge>
                        </div>
                        
                        <div class="text-xs text-zinc-500 flex flex-col gap-1">
                            <div><strong>{{ __('Available in Active Groups') }}:</strong> {{ $activity->available_in_active_groups ? __('Yes') : __('No') }}</div>
                            <div>
                                <strong>{{ __('Teachers') }}:</strong>
                                @if(!empty($activity->teachers))
                                    @php
                                        $teacherNames = [];
                                        foreach($activity->teachers as $tId) {
                                            $teacherNames[] = \App\Models\Employee::find($tId)?->full_name ?? '-';
                                        }
                                        echo implode(', ', array_filter($teacherNames));
                                    @endphp
                                @else
                                    -
                                @endif
                            </div>
                        </div>

                        @if($activity->description)
                            <div class="text-xs text-zinc-600 dark:text-zinc-400 line-clamp-2">
                                {{ $activity->description }}
                            </div>
                        @endif

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-700">
                            <flux:button wire:click="toggleActivation({{ $activity->id }})" variant="ghost" size="xs" icon="{{ $activity->activation == 1 ? 'eye-slash' : 'eye' }}" title="{{ __('Toggle Activation') }}" />
                            <flux:button href="{{ route('educational-activity-names.edit', $activity->id) }}" wire:navigate variant="ghost" size="xs" icon="pencil-square" title="{{ __('Edit') }}" />
                            <flux:button wire:click="delete({{ $activity->id }})" wire:confirm="{{ __('Are you sure you want to delete this activity name?') }}" variant="ghost" size="xs" icon="trash" class="text-red-500" title="{{ __('Delete') }}" />
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-zinc-500 italic">
                        {{ __('No activity names found.') }}
                    </div>
                @endforelse
            </div>

            {{-- Desktop View Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th wire:click="sortBy('activity_name')" class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer transition-colors hover:text-zinc-700 dark:hover:text-zinc-200">
                                <div class="flex items-center gap-1">
                                    {{ __('Name') }}
                                    <flux:icon name="{{ $sortField === 'activity_name' ? ($sortDirection === 'asc' ? 'chevron-up' : 'chevron-down') : 'chevron-up-down' }}" class="size-3" />
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Domain') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Available in Active Groups') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Teachers') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Description') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Status') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($activities as $activity)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $activity->activity_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $activity->domain?->status_name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                    <flux:badge color="{{ $activity->available_in_active_groups ? 'sky' : 'zinc' }}" size="sm">
                                        {{ $activity->available_in_active_groups ? __('Yes') : __('No') }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300 max-w-xs truncate">
                                    @if(!empty($activity->teachers))
                                        @php
                                            $teacherNames = [];
                                            foreach($activity->teachers as $tId) {
                                                $teacherNames[] = \App\Models\Employee::find($tId)?->full_name ?? '';
                                            }
                                            echo implode(', ', array_filter($teacherNames));
                                        @endphp
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300 truncate max-w-xs">
                                    {{ \Illuminate\Support\Str::limit($activity->description, 40) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <flux:badge color="{{ $activity->activation == 1 ? 'green' : 'zinc' }}" size="sm">
                                        {{ $activity->activation == 1 ? __('Active') : __('Inactive') }}
                                    </flux:badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <flux:button wire:click="toggleActivation({{ $activity->id }})" variant="ghost" size="sm" icon="{{ $activity->activation == 1 ? 'eye-slash' : 'eye' }}" title="{{ __('Toggle Activation') }}" />
                                        <flux:button href="{{ route('educational-activity-names.edit', $activity->id) }}" wire:navigate variant="ghost" size="sm" icon="pencil-square" title="{{ __('Edit') }}" />
                                        <flux:button wire:click="delete({{ $activity->id }})" wire:confirm="{{ __('Are you sure you want to delete this activity name?') }}" variant="ghost" size="sm" icon="trash" class="text-red-500 hover:text-red-600" title="{{ __('Delete') }}" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-zinc-500">
                                    {{ __('No activity names found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $activities->links() }}
        </div>
    </div>
</div>
