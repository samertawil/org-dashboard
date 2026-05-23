<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Education Points') }}</flux:heading>
            <flux:subheading>{{ __('Manage your education points.') }}</flux:subheading>
        </div>
        @can('student.group.create')
            <span title="{{ __('Add New Education Point') }}">
                <flux:button href="{{ route('student.group.create') }}" wire:navigate variant="primary" icon="plus"
                    class="w-full sm:w-auto">
                    {{ __('Add Point') }}
                </flux:button>
            </span>
        @endcan
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Search and Table Section --}}
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
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
                                'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' =>
                                    $group->activation == 1,
                                'bg-zinc-100 text-zinc-700 dark:bg-zinc-500/20 dark:text-zinc-400' =>
                                    $group->activation != 1,
                            ])>
                                {{ $statusEnum->label() }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="flex flex-col gap-1">
                            <span class="text-zinc-500">{{ __('Region/City') }}</span>
                            <span
                                class="font-medium text-zinc-700 dark:text-zinc-300">{{ $group->region->region_name ?? '-' }}/{{ $group->city->city_name ?? '-' }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-zinc-500">{{ __('Moderator') }}</span>
                            <span
                                class="font-medium text-zinc-700 dark:text-zinc-300">{{ $group->Moderator ?? '-' }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-zinc-500">{{ __('Students') }}</span>
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $group->students_count }} /
                                {{ $group->max_students }}</span>
                        </div>
                        {{-- <div class="flex flex-col gap-1">
                            <span class="text-zinc-500">{{ __('Subjects') }}</span>
                            @if (!empty($group->subject_to_learn_id) && is_array($group->subject_to_learn_id))
                                <div class="flex items-center gap-2">
                                    <span
                                        class="font-medium text-zinc-700 dark:text-zinc-300">{{ count($group->subject_to_learn_id) }}</span>
                                    <button wire:click="viewSubjects({{ $group->id }})"
                                        class="text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ __('View') }}
                                    </button>
                                </div>
                            @else
                                <span class="font-medium text-zinc-700 dark:text-zinc-300">-</span>
                            @endif
                        </div> --}}
                    </div>

                    <div
                        class="flex items-center justify-end gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800/50">
                        <span title="{{ __('View Details') }}">
                            <flux:button wire:click="viewGroupDetails({{ $group->id }})" variant="ghost"
                                size="xs" icon="eye" />
                        </span>
                        @can('student.group.schedule')
                            <span title="{{ __('Weekly Schedule') }}">
                                <flux:button href="{{ route('student.group.schedule', $group) }}" wire:navigate
                                    variant="ghost" size="xs" icon="calendar" />
                            </span>
                        @endcan
                        <span title="{{ __('Open in Google Maps') }}">
                            <flux:button
                                href="https://www.google.com/maps/search/?api=1&query={{ $group->region->region_name ?? '' }} {{ $group->city->city_name ?? '' }} {{ $group->neighbourhood->name ?? '' }} Gaza Strip"
                                target="_blank" variant="ghost" size="xs" icon="map-pin" style="color: red;" />
                        </span>
                        @can('student.group.create')
                            <span title="{{ __('Generate Schedule') }}">
                                <flux:button wire:click="generateSchedule({{ $group->id }})"
                                    wire:confirm="{{ __('Are you sure you want to generate the schedule for this group? This will only work if no schedule exists.') }}"
                                    variant="ghost" size="xs" icon="clock" />
                            </span>
                        @endcan
                        @can('student.group.create')
                            <span title="{{ __('Edit Education Point') }}">
                                <flux:button href="{{ route('student.group.edit', $group) }}" wire:navigate variant="ghost"
                                    size="xs" icon="pencil-square" />
                            </span>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-zinc-500 italic">
                    {{ __('No Education Points found.') }}
                </div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th wire:click="sortBy('name')"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Name') }}
                                @if ($sortField === 'name')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @endif
                            </div>
                        </th>

                        <th wire:click="sortBy('batch_no')"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
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
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Moderator') }}
                        </th>

                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Count/Max') }}
                        </th>

                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->groups as $group)
                        <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-950 dark:text-white">
                                <div class="flex flex-col gap-1.5">
                                    <span
                                        class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $group->name }}</span>
                                    <span class="flex items-center gap-1 text-xs text-zinc-500 dark:text-zinc-400">
                                        <flux:icon name="map-pin" class="size-3 text-zinc-400 shrink-0" />
                                        {{ $group->region->region_name ?? '-' }} /
                                        {{ $group->city->city_name ?? '-' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 dark:bg-zinc-700/50 text-zinc-800 dark:text-zinc-200 border border-zinc-200/50 dark:border-zinc-700/30">
                                    {{ __('Batch') }} #{{ $group->batch_no }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                <div class="flex items-center gap-2">

                                    <span>{{ $group->Moderator ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex flex-col gap-1.5 max-w-[120px]">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-medium text-zinc-700 dark:text-zinc-300">
                                            {{ $group->students_count }} / {{ $group->max_students }}
                                        </span>
                                        <span class="text-[10px] text-zinc-400 dark:text-zinc-500 font-mono">
                                            @php
                                                $percentage =
                                                    $group->max_students > 0
                                                        ? ($group->students_count / $group->max_students) * 100
                                                        : 0;
                                            @endphp
                                            {{ round($percentage) }}%
                                        </span>
                                    </div>
                                    <div
                                        class="w-full bg-zinc-100 dark:bg-zinc-700 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-indigo-600 dark:bg-indigo-500 h-1.5 rounded-full transition-all duration-300"
                                            style="width: {{ min($percentage, 100) }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-1.5">
                                    <span title="{{ __('View Details') }}">
                                        <flux:button wire:click="viewGroupDetails({{ $group->id }})"
                                            variant="ghost" size="sm" icon="eye" />
                                    </span>
                                    @can('student.group.schedule')
                                        <span title="{{ __('Weekly Schedule') }}">
                                            <flux:button href="{{ route('student.group.schedule', $group) }}"
                                                wire:navigate variant="ghost" size="sm" icon="calendar" />
                                        </span>
                                    @endcan
                                    <span title="{{ __('Open in Google Maps') }}">
                                        <flux:button
                                            href="https://www.google.com/maps/search/?api=1&query={{ $group->region->region_name ?? '' }} {{ $group->city->city_name ?? '' }} {{ $group->neighbourhood->name ?? '' }} Gaza Strip"
                                            target="_blank" variant="ghost" size="sm" icon="map-pin"
                                            style="color: red;" />
                                    </span>
                                    @can('student.group.create')
                                        <span title="{{ __('Generate Schedule') }}">
                                            <flux:button wire:click="generateSchedule({{ $group->id }})"
                                                wire:confirm="{{ __('Are you sure you want to generate the schedule for this group? This will only work if no schedule exists.') }}"
                                                variant="ghost" size="sm" icon="clock" />
                                        </span>
                                    @endcan

                                    @can('student.group.create')
                                        <span title="{{ __('Edit Education Point') }}">
                                            <flux:button href="{{ route('student.group.edit', $group) }}" wire:navigate
                                                variant="ghost" size="sm" icon="pencil-square" />
                                        </span>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-zinc-500 italic">
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
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-50 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 border border-blue-100 dark:border-blue-500/30">
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

    {{-- Group Details Modal --}}
    <flux:modal wire:model="showDetailsModal" class="w-full md:w-[750px]">
        @if ($selectedGroup)
            <div class="p-6 space-y-6">
                {{-- Modal Header --}}
                <div class="flex items-start justify-between border-b border-zinc-100 dark:border-zinc-800 pb-4">
                    <div class="space-y-1">
                        <flux:heading size="lg" class="font-bold text-zinc-900 dark:text-white">
                            {{ $selectedGroup->name }}
                        </flux:heading>
                        <flux:subheading class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 border border-zinc-200/50 dark:border-zinc-700/30">
                                {{ __('Batch') }} #{{ $selectedGroup->batch_no }}
                            </span>
                            @php
                                $statusEnum = \App\Enums\GlobalSystemConstant::tryFrom($selectedGroup->activation);
                            @endphp
                            @if ($statusEnum)
                                <span @class([
                                    'inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold',
                                    'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' =>
                                        $selectedGroup->activation == 1,
                                    'bg-zinc-100 text-zinc-700 dark:bg-zinc-500/20 dark:text-zinc-400' =>
                                        $selectedGroup->activation != 1,
                                ])>
                                    {{ $statusEnum->label() }}
                                </span>
                            @endif
                        </flux:subheading>
                    </div>
                </div>

                {{-- Modal Body - Two Column Grid on Desktop, Single Column on Mobile --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">

                    {{-- Capacity & Institution --}}
                    <div
                        class="space-y-4 bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <h4
                            class="font-semibold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5 border-b border-zinc-200/60 dark:border-zinc-700/55 pb-1.5">
                            <flux:icon name="academic-cap" class="size-4" />
                            {{ __('Academic Info') }}
                        </h4>

                        <div class="space-y-3">
                            {{-- Capacity Bar --}}
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="text-zinc-500 dark:text-zinc-400">{{ __('Student Capacity') }}</span>
                                    <span class="font-semibold text-zinc-800 dark:text-zinc-200">
                                        {{ $selectedGroup->students_count }} / {{ $selectedGroup->max_students }}
                                    </span>
                                </div>
                                @php
                                    $percentage =
                                        $selectedGroup->max_students > 0
                                            ? ($selectedGroup->students_count / $selectedGroup->max_students) * 100
                                            : 0;
                                @endphp
                                <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2 overflow-hidden">
                                    <div class="bg-indigo-600 dark:bg-indigo-500 h-2 rounded-full transition-all duration-300"
                                        style="width: {{ min($percentage, 100) }}%"></div>
                                </div>
                                <span
                                    class="text-[10px] text-zinc-400 dark:text-zinc-500 mt-1 block">{{ round($percentage) }}%
                                    {{ __('Capacity Used') }}</span>
                            </div>

                            <div>
                                <span
                                    class="text-xs text-zinc-500 dark:text-zinc-400 block">{{ __('Min Students') }}</span>
                                <span
                                    class="font-medium text-zinc-800 dark:text-zinc-200">{{ $selectedGroup->min_students ?? '-' }}</span>
                            </div>

                            <div>
                                <span
                                    class="text-xs text-zinc-500 dark:text-zinc-400 block">{{ __('Partner Institution') }}</span>
                                <span
                                    class="font-medium text-zinc-800 dark:text-zinc-200">{{ $selectedGroup->partner->name ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Location Details --}}
                    <div
                        class="space-y-4 bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <h4
                            class="font-semibold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5 border-b border-zinc-200/60 dark:border-zinc-700/55 pb-1.5">
                            <flux:icon name="map-pin" class="size-4" />
                            {{ __('Location Details') }}
                        </h4>

                        <div class="space-y-3">
                            <div>
                                <span
                                    class="text-xs text-zinc-500 dark:text-zinc-400 block">{{ __('Region / City') }}</span>
                                <span class="font-medium text-zinc-800 dark:text-zinc-200">
                                    {{ $selectedGroup->region->region_name ?? '-' }} /
                                    {{ $selectedGroup->city->city_name ?? '-' }}
                                </span>
                            </div>

                            <div>
                                <span
                                    class="text-xs text-zinc-500 dark:text-zinc-400 block">{{ __('Neighbourhood') }}</span>
                                <span
                                    class="font-medium text-zinc-800 dark:text-zinc-200">{{ $selectedGroup->neighbourhood->name ?? '-' }}</span>
                            </div>

                            <div>
                                <span
                                    class="text-xs text-zinc-500 dark:text-zinc-400 block">{{ __('Address Details') }}</span>
                                <span
                                    class="font-medium text-zinc-800 dark:text-zinc-200">{{ $selectedGroup->address_details ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Moderator Details --}}
                    <div
                        class="space-y-4 bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <h4
                            class="font-semibold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5 border-b border-zinc-200/60 dark:border-zinc-700/55 pb-1.5">
                            <flux:icon name="user" class="size-4" />
                            {{ __('Moderator Info') }}
                        </h4>

                        <div class="space-y-3">
                            <div>
                                <span
                                    class="text-xs text-zinc-500 dark:text-zinc-400 block">{{ __('Moderator Name') }}</span>
                                <span
                                    class="font-medium text-zinc-800 dark:text-zinc-200">{{ $selectedGroup->Moderator ?? '-' }}</span>
                            </div>

                            <div>
                                <span
                                    class="text-xs text-zinc-500 dark:text-zinc-400 block">{{ __('Phone Number') }}</span>
                                @if ($selectedGroup->Moderator_phone)
                                    <a href="tel:{{ $selectedGroup->Moderator_phone }}"
                                        class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 mt-0.5">
                                        <flux:icon name="phone" class="size-3.5" />
                                        {{ $selectedGroup->Moderator_phone }}
                                    </a>
                                @else
                                    <span class="font-medium text-zinc-800 dark:text-zinc-200">-</span>
                                @endif
                            </div>

                            <div>
                                <span
                                    class="text-xs text-zinc-500 dark:text-zinc-400 block">{{ __('Email Address') }}</span>
                                @if ($selectedGroup->Moderator_email)
                                    <a href="mailto:{{ $selectedGroup->Moderator_email }}"
                                        class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 mt-0.5">
                                        <flux:icon name="envelope" class="size-3.5" />
                                        {{ $selectedGroup->Moderator_email }}
                                    </a>
                                @else
                                    <span class="font-medium text-zinc-800 dark:text-zinc-200">-</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Timing / Dates --}}
                    <div
                        class="space-y-4 bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <h4
                            class="font-semibold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5 border-b border-zinc-200/60 dark:border-zinc-700/55 pb-1.5">
                            <flux:icon name="clock" class="size-4" />
                            {{ __('Schedule Details') }}
                        </h4>

                        <div class="space-y-3">
                            <div>
                                <span
                                    class="text-xs text-zinc-500 dark:text-zinc-400 block">{{ __('Duration') }}</span>
                                <span
                                    class="font-medium text-zinc-800 dark:text-zinc-200 flex items-center gap-1.5 mt-0.5">
                                    <flux:icon name="calendar" class="size-4 text-zinc-400" />
                                    {{ $selectedGroup->start_date ? \Carbon\Carbon::parse($selectedGroup->start_date)->format('Y-m-d') : '-' }}<br>
                                    {{ __('to') }}<br>
                                    {{ $selectedGroup->end_date ? \Carbon\Carbon::parse($selectedGroup->end_date)->format('Y-m-d') : '-' }}
                                </span>
                            </div>

                            <div>
                                <span
                                    class="text-xs text-zinc-500 dark:text-zinc-400 block">{{ __('Daily Time') }}</span>
                                <span
                                    class="font-medium text-zinc-800 dark:text-zinc-200 flex items-center gap-1.5 mt-0.5">
                                    <flux:icon name="clock" class="size-4 text-zinc-400" />
                                    {{ $selectedGroup->start_time ? $selectedGroup->start_time->format('H:i') : '-' }}
                                    -
                                    {{ $selectedGroup->end_time ? $selectedGroup->end_time->format('H:i') : '-' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Assigned Subjects & Description (Full width sections) --}}
                <div class="space-y-4">
                    {{-- Subjects --}}
                    <div
                        class="bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-xl border border-zinc-100 dark:border-zinc-800">
                        <h4
                            class="font-semibold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5 border-b border-zinc-200/60 dark:border-zinc-700/55 pb-1.5 mb-3">
                            <flux:icon name="book-open" class="size-4" />
                            {{ __('Assigned Subjects') }}
                        </h4>
                        @php
                            $subjects = $selectedGroup->subjects;
                        @endphp
                        <div class="flex flex-wrap gap-2">
                            @forelse($subjects as $subject)
                                <span
                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-300 border border-blue-100 dark:border-blue-500/20">
                                    {{ $subject->name }}
                                </span>
                            @empty
                                <span
                                    class="text-xs text-zinc-500 italic">{{ __('No subjects assigned to this group.') }}</span>
                            @endforelse
                        </div>
                    </div>

                    {{-- Description --}}
                    @if ($selectedGroup->description)
                        <div
                            class="bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-xl border border-zinc-100 dark:border-zinc-800">
                            <h4
                                class="font-semibold text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5 border-b border-zinc-200/60 dark:border-zinc-700/55 pb-1.5 mb-2">
                                <flux:icon name="document-text" class="size-4" />
                                {{ __('Description') }}
                            </h4>
                            <p class="text-zinc-700 dark:text-zinc-300 leading-relaxed text-xs">
                                {{ $selectedGroup->description }}
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div
                    class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                    <flux:button
                        href="https://www.google.com/maps/search/?api=1&query={{ $selectedGroup->region->region_name ?? '' }} {{ $selectedGroup->city->city_name ?? '' }} {{ $selectedGroup->neighbourhood->name ?? '' }} Gaza Strip"
                        target="_blank" variant="filled" icon="map-pin"
                        class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white">
                        {{ __('Open in Google Maps') }}
                    </flux:button>

                    <flux:button wire:click="closeDetailsModal" variant="ghost"
                        class="w-full sm:w-auto border border-zinc-200 dark:border-zinc-700">
                        {{ __('Close') }}
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
