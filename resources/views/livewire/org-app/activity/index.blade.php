<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Activities') }}</flux:heading>
            <flux:subheading>{{ __('Manage and filter organization Activities.') }}</flux:subheading>
        </div>

        <flux:button href="{{ route('activity.create') }}" wire:navigate variant="primary" icon="plus">
            {{ __('Add Activity') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Search and Filters Section --}}
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            {{-- Name Search --}}
            <flux:field class="relative">
                <flux:label>{{ __('Activity Name') }}</flux:label>
                <flux:input wire:model.live.debounce.300ms="search" :placeholder="__('Search by name...')" icon="magnifying-glass" />
                <div wire:loading wire:target="search" class="absolute right-3 bottom-2">
                    <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                </div>
            </flux:field>

            {{-- Start Date --}}
            <flux:field>
                <flux:label>{{ __('Start Date') }}</flux:label>
                <flux:input type="date" wire:model.live="start_date" />
            </flux:field>

            {{-- Status --}}
            <flux:select wire:model.live="status_id" :label="__('Status')">
                <option value="">{{ __('All Statuses') }}</option>
                @foreach ($this->allStatuses->where('p_id_sub', config('appConstant.activity_status')) as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </flux:select>

            {{-- Region --}}
            <flux:select wire:model.live="region_id" :label="__('Region')">
                <option value="">{{ __('All Regions') }}</option>
                @foreach ($regions as $region)
                    <option value="{{ $region->id }}">{{ $region->region_name }}</option>
                @endforeach
            </flux:select>

            {{-- City --}}
            <flux:select wire:model.live="city_id" :label="__('City')" :disabled="!$region_id">
                <option value="">{{ __('All Cities') }}</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                @endforeach
            </flux:select>



            {{-- Clear Filters --}}
            @if ($search || $start_date || $status_id || $region_id || $city_id)
                <div class="mt-4 flex items-center justify-end">
                    <flux:button
                        wire:click="$set('search', ''); $set('start_date', ''); $set('status_id', ''); $set('region_id', ''); $set('city_id', '')"
                        variant="ghost" size="sm" icon="x-mark">
                        {{ __('Clear Filters') }}
                    </flux:button>
                </div>
            @endif

        </div>

        <div class="overflow-x-auto -mx-6">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Name') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Start Date') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Status') }}
                        </th>

                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Specific Sector') }}
                        </th>

                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Region/City') }}
                        </th>
{{-- 
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Rating') }}
                        </th> --}}

                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->activities as $activity)
                        <tr wire:key="activity-{{ $activity->id }}"
                            class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-6 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $activity->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $activity->start_date }}
                            </td>

                            {{--
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <span @class([
                                    'text-sm max-w-xs  truncate',
                                    'text-green-600 dark:text-green-400' => $activity->status === 27,
                                    'text-yellow-600 dark:text-yellow-400' => $activity->status === 26,
                                    'text-purple-600 dark:text-purple-400' => $activity->status === 25,
                                    'text-red-600 dark:text-red-400' => $activity->status === 28,
                                ])>
                                    {{ $activity->status_name ?? ($activity->activityStatus->status_name ?? '-') }}
                                </span>
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge :color="$activity->status_info['color']" size="sm" inset="top bottom">
                                    {{ $activity->status_info['name'] }}
                                </flux:badge>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $activity->statusSpecificSector->status_name ?? '-' }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $activity->regions->region_name ?? '-' }} /
                                {{ $activity->cities->city_name ?? '-' }}
                            </td>
{{-- 
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                <div class="flex items-center gap-1">
                                    <flux:icon icon="star" variant="solid" class="{{ $activity->rating_info['color'] }} w-4 h-4" />
                                    <span>{{ $activity->rating_info['rating'] }}</span>
                                </div>
                            </td> --}}

                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button href="{{ route('activity.edit', $activity) }}" wire:navigate
                                        variant="ghost" size="sm" icon="pencil-square" />
                                    <flux:button wire:click="delete({{ $activity->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this activity?') }}"
                                        variant="ghost" size="sm" icon="trash"
                                        class="text-red-500 hover:text-red-600 dark:hover:text-red-400" />
                                    @php $modalName = 'activity-show-' . $activity->id; @endphp

                                    <flux:modal.trigger :name="$modalName">
                                        <flux:button icon="eye" variant="ghost" size="sm"
                                            tooltip="Show All Data" wire:click="openShowModal({{ $activity->id }})">
                                        </flux:button>
                                    </flux:modal.trigger>

                                    <flux:modal :name="$modalName" class="!w-11/12 !max-w-7xl md:!w-11/12 md:!max-w-7xl"
                                        x-on:close-modal="$wire.closeShowModal()">
                                        <div class="mt-4">
                                            @if ($selectedactivityIdForShowModal === $activity->id)
                                                <livewire:OrgApp.activity.show :activity="$activity"
                                                    wire:key="show-activity-{{ $activity->id }}" />
                                            @endif
                                        </div>
                                    </flux:modal>

                                    <div class="relative">
                                            <flux:button href="{{ route('activity.gallery', $activity->id) }}" wire:navigate icon="paper-clip"
                                                variant="ghost" size="sm" tooltip="Attachments" style="{{ $activity->attachments_count > 0 ? 'color: #3b82f6 !important;' : '' }}">
                                            </flux:button>
                                            @if($activity->attachments_count > 0)
                                                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-blue-500 ring-1 ring-white dark:ring-zinc-900"></span>
                                            @endif
                                        </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-zinc-500">
                                {{ __('No activitys found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>



        <div class="mt-4">
            {{ $this->activities->links() }}
        </div>
    </div>
</div>
