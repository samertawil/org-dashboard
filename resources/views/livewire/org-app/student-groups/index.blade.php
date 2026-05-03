<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Education Points') }}</flux:heading>
            <flux:subheading>{{ __('Manage your education points.') }}</flux:subheading>
        </div>
        @can('student.group.create') 
        <span title="{{ __('Add New Education Point') }}">
            <flux:button href="{{ route('student.group.create') }}" wire:navigate variant="primary" icon="plus" class="w-full sm:w-auto">
                {{ __('Add Point') }}
            </flux:button>
        </span>
        @endcan
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Search and Table Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 relative">
            <flux:input wire:model.live="search" :placeholder="__('Search by name or moderator...')"
                icon="magnifying-glass" />
            <div wire:loading wire:target="search" class="absolute right-6 top-1/2 -translate-y-1/2">
                <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
            </div>
        </div>

        @if ($search)
            <div class="px-4 py-2 flex items-center justify-end">
                <span title="{{ __('Clear search and filters') }}">
                    <flux:button wire:click="$set('search', '');" variant="ghost" size="sm" icon="x-mark">
                        {{ __('Clear Filters') }}
                    </flux:button>
                </span>
            </div>
        @endif

        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Showing') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $this->groups->firstItem() }}</span>
                    {{ __('to') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $this->groups->lastItem() }}</span>
                    {{ __('of') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $this->groups->total() }}</span>
                    {{ __('results') }}
                </p>
            </div>
        </div>

        {{-- Mobile Cards View --}}
        <div class="block md:hidden divide-y divide-zinc-200 dark:divide-zinc-700">
            @forelse($this->groups as $group)
                <div class="p-4 space-y-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ $group->name }}</span>
                            <span class="text-xs text-zinc-500">{{ __('Batch') }}: {{ $group->batch_no }}</span>
                        </div>
                        @php
                            $statusEnum = \App\Enums\GlobalSystemConstant::tryFrom($group->activation);
                        @endphp
                        @if ($statusEnum)
                            <span @class([
                                'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium',
                                'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' => $group->activation == 1,
                                'bg-zinc-100 text-zinc-700 dark:bg-zinc-500/20 dark:text-zinc-400' => $group->activation != 1,
                            ])>
                                {{ $statusEnum->label() }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="flex flex-col gap-1">
                            <span class="text-zinc-500">{{ __('Region/City') }}</span>
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $group->region->region_name ?? '-' }}/{{ $group->city->city_name ?? '-' }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-zinc-500">{{ __('Moderator') }}</span>
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $group->Moderator ?? '-' }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-zinc-500">{{ __('Students') }}</span>
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $group->students_count }} / {{ $group->max_students }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-zinc-500">{{ __('Subjects') }}</span>
                            @if (!empty($group->subject_to_learn_id) && is_array($group->subject_to_learn_id))
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ count($group->subject_to_learn_id) }}</span>
                                    <button wire:click="viewSubjects({{ $group->id }})" class="text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ __('View') }}
                                    </button>
                                </div>
                            @else
                                <span class="font-medium text-zinc-700 dark:text-zinc-300">-</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800/50">
                        <span title="{{ __('Weekly Schedule') }}">
                            <flux:button href="{{ route('student.group.schedule', $group) }}" wire:navigate variant="ghost" size="xs" icon="calendar" />
                        </span>
                        <span title="{{ __('Open in Google Maps') }}">
                            <flux:button href="https://www.google.com/maps/search/?api=1&query={{ $group->region->region_name ?? '' }} {{ $group->city->city_name ?? '' }} {{ $group->neighbourhood->name ?? '' }} Gaza Strip" 
                                target="_blank" variant="ghost" size="xs" icon="map-pin" />
                        </span>
                        <span title="{{ __('Edit Education Point') }}">
                            <flux:button href="{{ route('student.group.edit', $group) }}" wire:navigate variant="ghost" size="xs" icon="pencil-square" />
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-zinc-500 italic">
                    {{ __('No Education Points found.') }}
                </div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th wire:click="sortBy('name')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Name') }}
                                @if ($sortField === 'name')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @endif
                            </div>
                        </th>

                        <th wire:click="sortBy('batch_no')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Batch No') }}
                                @if ($sortField === 'batch_no')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        {{ __('Region') }} /  {{ __('City') }}
                    </th>
                    
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Moderator') }}
                        </th>
                      
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Subjects') }}
                        </th>
                         <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Count/Max') }}
                        </th>
                       
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Status') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->groups as $group)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $group->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300 text-center">
                                {{ $group->batch_no }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $group->region->region_name ?? '-' }}/  {{ $group->city->city_name ?? '-' }}
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $group->Moderator ?? '-' }}
                            </td>
                           
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                @if (!empty($group->subject_to_learn_id) && is_array($group->subject_to_learn_id))
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-200">
                                            {{ count($group->subject_to_learn_id) }} {{ __('Subjects') }}
                                        </span>
                                        <button wire:click="viewSubjects({{ $group->id }})" class="text-xs text-blue-600 hover:text-blue-500 hover:underline dark:text-blue-400">
                                            {{ __('View All') }}
                                        </button>
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $group->students_count }} / {{ $group->max_students }}
                            </td>
                         
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @php
                                    $statusEnum = \App\Enums\GlobalSystemConstant::tryFrom($group->activation);
                                @endphp
                                @if ($statusEnum)
                                    <span @class([
                                        'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                        'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' =>
                                            $group->activation == 1,
                                        'bg-zinc-100 text-zinc-700 dark:bg-zinc-500/20 dark:text-zinc-400' =>
                                            $group->activation != 1,
                                    ])>
                                        {{ $statusEnum->label() }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <span title="{{ __('Weekly Schedule') }}">
                                        <flux:button href="{{ route('student.group.schedule', $group) }}" wire:navigate
                                            variant="ghost" size="sm" icon="calendar" />
                                    </span>
                                    <span title="{{ __('Open in Google Maps') }}">
                                        <flux:button href="https://www.google.com/maps/search/?api=1&query={{ $group->region->region_name ?? '' }} {{ $group->city->city_name ?? '' }} {{ $group->neighbourhood->name ?? '' }} Gaza Strip" 
                                            target="_blank" variant="ghost" size="sm" icon="map-pin" />
                                    </span>
                                    <span title="{{ __('Edit Education Point') }}">
                                        <flux:button href="{{ route('student.group.edit', $group) }}" wire:navigate
                                            variant="ghost" size="sm" icon="pencil-square" />
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-zinc-500 italic">
                                {{ __('No Education Points found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $this->groups->links() }}
        </div>
    </div>

    {{-- Subjects Modal --}}
    <flux:modal wire:model="showSubjectsModal">
        <div class="p-6">
            <div class="flex flex-col gap-4">
                <div>
                    <flux:heading size="lg">{{ $viewingGroupName }} - {{ __('Subjects') }}</flux:heading>
                    <flux:subheading>{{ __('List of subjects assigned to this group.') }}</flux:subheading>
                </div>
                
                <div class="flex flex-wrap gap-2 mt-2">
                     @forelse($viewingSubjects as $subjectName)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-100 dark:border-blue-500/30">
                            {{ $subjectName }}
                        </span>
                    @empty
                        <span class="text-zinc-500 italic">{{ __('No subjects found.') }}</span>
                    @endforelse
                </div>

                <div class="flex justify-end mt-4">
                    <flux:button wire:click="closeSubjectsModal" variant="ghost">
                        {{ __('Close') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
