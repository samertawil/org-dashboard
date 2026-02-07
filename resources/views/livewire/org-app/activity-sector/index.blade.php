<section class="w-full">
    <div class="relative mb-2 w-full text-blue-600 flex items-start justify-between bg-zinc-50 dark:bg-zinc-900 ">
        <div class="flex flex-col gap-2">
            <flux:heading size="xl" level="1" class="text-blue-600">{{ __('Sectors & Activities') }}
            </flux:heading>
            <flux:subheading size="lg">{{ __('monitor and manage your sectors') }}</flux:subheading>

        </div>
        <div class="self-center">
            <flux:button href="{{ route('activity.create') }}" wire:navigate variant="ghost" icon="plus">
                {{ __('New Activity') }}
            </flux:button>
        </div>

    </div>

    <flux:separator variant="subtle" />

    <div class="flex items-start max-md:flex-col py-10 mt-6">
        {{-- Left Sidebar: Sectors --}}

        <div class="me-10 w-full pb-4 md:w-[220px] shrink-0">
            <div class="mb-4 relative">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search..." />
                <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                </div>
            </div>

            @if ($search)
                <div class="mb-4">
                    <flux:button wire:click="$set('search', '');" variant="ghost" size="sm" icon="x-mark" class="w-full justify-center">
                        {{ __('Clear Filters') }}
                    </flux:button>
                </div>
            @endif

            <flux:navlist>
                @forelse ($this->sectors as $sector)
                    <div class="mb-6">
                        <flux:navlist.item
                            wire:click="selectSector({{ $sector->sector_id }}, '{{ $sector->activites_date }}')"
                            :current="$selectedSectorId === $sector->sector_id && $selectedSectorDate === $sector->activites_date"
                            class="cursor-pointer !py-3 !px-3">
                            <div class="flex flex-col gap-0.5">
                                <span class="font-medium truncate">{{ $sector->sector_name }}</span>
                                <span class="text-[10px] opacity-60 font-mono">
                                    {{ $this->formatDate($sector->activites_date) }}
                                </span>
                            </div>
                        </flux:navlist.item>
                    </div>

                @empty
                    <div class="px-3 py-2 text-sm text-zinc-400 italic">
                        No sectors found.
                    </div>
                @endforelse
            </flux:navlist>
        </div>

        <flux:separator class="md:hidden my-6" />

        {{-- Right Content: Activities --}}
        <div class="flex-1 self-stretch max-md:pt-6 w-full min-w-0">
            @if ($this->selectedSector)
                <div class="mb-8">
                    <flux:heading size="xl" level="1" class="text-center">
                        {{ $this->selectedSector->sector_name }}</flux:heading>
                    <flux:subheading class="text-center">
                        Activity Report for <span
                            class="font-medium text-zinc-800 dark:text-zinc-200">{{ $this->formatDate($this->selectedSector->activites_date) }}</span>
                    </flux:subheading>
                </div>

                <div class="space-y-4 mt-5">
                    @forelse ($this->activities as $index => $activity)
                        <flux:card wire:key="activity-{{ $activity->id }}"
                            class="p-5 hover:border-indigo-400 transition-colors group">
                            <div class="flex flex-col gap-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex justify-end">


                                            <div class="flex items-center gap-2 mb-1">
                                                <span
                                                    class=" font-bold text-lg text-blue-800 dark:text-blue  dark:text-white group-hover:text-indigo-600 transition-colors">{{ $index + 1 }}
                                                    &nbsp;- </span>
                                                <h3
                                                    class=" font-bold text-lg text-blue-800 dark:text-blue  dark:text-white group-hover:text-indigo-600 transition-colors">
                                                    {{ $activity->name }}
                                                </h3> <span class="flex items-center gap-1">
                                                    <flux:icon.calendar class="w-3 h-3" />
                                                    {{ \Carbon\Carbon::parse($activity->start_date)->format('M d, Y') }}
                                                </span>

                                                <span @class([
                                                    'max-w-xs  truncate',
                                                    'text-green-600 dark:text-green-400' => $activity->status === 27,
                                                    'text-yellow-600 dark:text-yellow-400' => $activity->status === 26,
                                                    'text-purple-600 dark:text-purple-400' => $activity->status === 25,
                                                    'text-red-600 dark:text-red-400' => $activity->status === 28,
                                                ])>

                                                    <flux:badge :color="$activity->status_info['color']" size="sm"
                                                        inset="top bottom">
                                                        {{ $activity->status_info['name'] }}
                                                    </flux:badge>


                                            </div>
                                        </div>


                                        <div class="flex flex-wrap gap-y-1 gap-x-2 text-md ">

                                            <span> </span>
                                            <span>{{ $activity->regions->region_name ?? 'N/A Location' }}</span>
                                            @if ($activity->cities->city_name ?? false)
                                                <span> &nbsp; &bull; &nbsp; </span>
                                                <span>{{ $activity->cities->city_name }}</span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                                @php
                                    $report = $activityReport->firstWhere('id', $activity->id);
                                @endphp
                                <div class="flex gap-2">
                                    <flux:badge size="sm" inset="top bottom" color="zinc"
                                        class="{{ ($report?->parcels_status ?? '') === 'ADDED PARCELS' ? '!text-green-600' : '' }}">
                                        {{ $report?->parcels_status ?? '' }}
                                    </flux:badge>
                                    <flux:badge size="sm" inset="top bottom" color="zinc"
                                        class="{{ ($report?->beneficiaries_status ?? '') === 'ADDED BENEFICIARIES' ? '!text-green-600' : '' }}">
                                        {{ $report?->beneficiaries_status ?? '' }}
                                    </flux:badge>
                                    <flux:badge size="sm" inset="top bottom" color="zinc"
                                        class="{{ ($report?->work_teams_status ?? '') === 'ADDED WORK TEAMS' ? '!text-green-600' : '' }}">
                                        {{ $report?->work_teams_status ?? '' }}
                                    </flux:badge>
                                    <flux:badge size="sm" inset="top bottom" color="zinc"
                                        class="{{ ($report?->attchemnts_status ?? '') === 'ADDED ATTCHMENTS' ? '!text-green-600' : '' }}">
                                        {{ $report?->attchemnts_status ?? '' }}
                                    </flux:badge>
                                </div>


                                <flux:separator variant="subtle" />
                                @if ($activity->parcels->isNotEmpty())
                                    <div class="flex flex-wrap gap-2 pt-2   dark:border-zinc-800">
                                        @foreach ($activity->parcels as $parcel)
                                            <flux:badge size="xs" color="zinc" variant="pill" icon="cube">
                                                {{ $parcel->parcelType->status_name }}
                                            </flux:badge>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="flex justify-between items-center mt-4">

                                <div>

                                    @if ($activity->rating_info['rating'] > 0)
                                        <div class="flex items-center gap-1 ml-2">
                                            <flux:icon icon="star" variant="solid"
                                                class="{{ $activity->rating_info['color'] }} w-4 h-4" />
                                            <span
                                                class="text-xs font-medium text-zinc-600 dark:text-zinc-300">{{ $activity->rating_info['rating'] }}</span>
                                            <span
                                                class="text-[10px] text-zinc-500">({{ $activity->rating_info['text'] }})</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex justify-end items-center">
                                    @php $modalName = 'activity-show-' . $activity->id; @endphp

                                    <flux:modal.trigger :name="$modalName">
                                        <flux:button variant="subtle" size="xs" icon="eye"
                                            tooltip="Show All Data" wire:click="openShowModal({{ $activity->id }})">
                                            Details </flux:button>
                                    </flux:modal.trigger>

                                    <flux:modal :name="$modalName" class="md:w-96"
                                        x-on:close-modal="$wire.closeShowModal()">
                                        <div class="mt-4">
                                            @if ($selectedactivityIdForShowModal === $activity->id)
                                                <livewire:OrgApp.activity.show :activity="$activity"
                                                    wire:key="show-activity-{{ $activity->id }}" />
                                            @endif
                                        </div>
                                    </flux:modal>
                                </div>
                            </div>
                        </flux:card>
                    @empty
                        <div
                            class="flex flex-col items-center justify-center py-12 text-center border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/50">
                            <flux:icon.document-magnifying-glass class="w-8 h-8 text-zinc-300 mb-3" />
                            <h3 class="text-zinc-900 dark:text-white font-medium">No activities found</h3>
                            <p class="text-zinc-500 text-xs mt-1">This sector currently has no recorded activities.</p>
                        </div>
                    @endforelse
                </div>
            @else
                <div
                    class="h-64 flex flex-col items-center justify-center text-center border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-xl bg-zinc-50/50 dark:bg-zinc-900/50">
                    <flux:icon.cursor-arrow-rays class="w-8 h-8 text-indigo-300 mb-3" />
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-white">Select a Sector</h3>
                    <p class="text-zinc-500 text-sm mt-1 max-w-xs">Select a sector from the sidebar to view its
                        activities.
                    </p>
                </div>
            @endif
        </div>
    </div>
</section>
