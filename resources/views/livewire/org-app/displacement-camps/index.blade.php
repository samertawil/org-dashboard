<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Displacement Camps') }}</flux:heading>
            <flux:subheading>{{ __('Manage displacement camps parameters and data.') }}</flux:subheading>
        </div>
        <div class="flex gap-2">
            <flux:modal.trigger name="camps-map-modal">
                <flux:button variant="ghost" icon="map" x-on:click="$dispatch('init-camps-map')">
                    {{ __('Show All on Map') }}
                </flux:button>
            </flux:modal.trigger>

            @can('displacement.camps.create')
                <flux:button href="{{ route('displacement.camps.create') }}" wire:navigate variant="primary" icon="plus">
                    {{ __('New Camp') }}
                </flux:button>
            @endcan
        </div>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Filters --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
            <flux:input wire:model.live.debounce.300ms="search_name" :placeholder="__('Camp Name')"
                icon="magnifying-glass" />

            <flux:input wire:model.live.debounce.300ms="search_moderator" :placeholder="__('Moderator Name')"
                icon="user" />

            {{-- Region --}}
            <flux:select wire:model.live="search_region_id">
                <option value="">{{ __('All Regions') }}</option>
                @foreach ($regions as $region)
                    <option value="{{ $region->id }}">
                        {{ $region->name ?? ($region->region_name ?? ($region->ar_name ?? $region->id)) }}</option>
                @endforeach
            </flux:select>

            {{-- City --}}
            <flux:select wire:model.live="search_city_id">
                <option value="">{{ __('All Cities') }}</option>
                @foreach ($cities as $city)
                    <option value="{{ $city->id }}">
                        {{ $city->name ?? ($city->city_name ?? ($city->ar_name ?? $city->id)) }}</option>
                @endforeach
            </flux:select>

            <flux:input wire:model.live.debounce.300ms="search_address_details" :placeholder="__('Address Details')" />

            {{-- Camp Needs --}}
            <flux:select wire:model.live="search_camp_main_needs">
                <option value="">{{ __('All Needs') }}</option>
                @foreach ($this->needsList as $need)
                    <option value="{{ $need['need'] }}">{{ $need['need'] }}</option>
                @endforeach
            </flux:select>

        </div>
        <div class="mb-4">

            @if (
                $search_name ||
                    $search_moderator ||
                    $search_region_id ||
                    $search_city_id ||
                    $search_address_details ||
                    $search_camp_main_needs)
                <div class="mt-4 flex items-center justify-end">
                    <flux:button
                        wire:click="$set('search_name', ''); $set('search_moderator', ''); $set('search_region_id', ''); $set('search_city_id', ''); 
                    $set('search_address_details', '');  $set('search_camp_main_needs', '')"
                        variant="ghost" size="sm" icon="x-mark">
                        {{ __('Clear Filters') }}
                    </flux:button>
                </div>
            @endif
        </div>
        <div class="overflow-x-auto -mx-6">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                        {{ __('Showing') }}
                        <span
                            class="font-medium text-zinc-900 dark:text-white">{{ $this->displacementCamps->firstItem() }}</span>
                        {{ __('to') }}
                        <span
                            class="font-medium text-zinc-900 dark:text-white">{{ $this->displacementCamps->lastItem() }}</span>
                        {{ __('of') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $this->displacementCamps->total() }}</span>
                        {{ __('results') }}
                    </p>
                </div>
            </div>
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th wire:click="sortBy('name')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Camp Name') }}
                                @if ($sortField === 'name')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('region_id')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Location') }}
                                @if ($sortField === 'region_id')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th wire:click="sortBy('Moderator')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Moderator') }}
                                @if ($sortField === 'Moderator')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Families / Ind.') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->displacementCamps as $camp)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <td class="px-6 py-4 text-sm font-medium">{{ $camp->name }}</td>
                            <td class="px-6 py-4 text-sm truncate max-w-[200px]">
                                {{ $camp->region?->name ?? ($camp->region?->region_name ?? ($camp->region?->ar_name ?? '')) }}
                                {{ $camp->city ? ' - ' . ($camp->city?->name ?? ($camp->city?->city_name ?? $camp->city?->ar_name)) : '' }}
                                <br />
                                <span class="text-xs text-zinc-500">{{ $camp->address_details }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm truncate max-w-[150px]">
                                {{ $camp->Moderator ?? '-' }}<br />
                                <span class="text-xs text-zinc-500">{{ $camp->Moderator_phone }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                <flux:badge size="sm">{{ $camp->number_of_families ?? 0 }} /
                                    {{ $camp->number_of_individuals ?? 0 }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                               
                                  
                             
                                <div class="flex items-center justify-end gap-2">
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $camp->latitude }},{{ $camp->longitudes }}"
                                        target="_blank" title="{{ __('View Map') }}" 
                                        class="inline-flex items-center justify-center size-8 rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                                        <svg viewBox="0 0 24 24" class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="#EA4335"/>
                                        </svg>
                                    </a>
                                    @php
                                        // User request: count attachments and show blue badge dot
                                        $attachmentCount = count($camp->attchments ?? []);
                                    @endphp

                                    <div class="relative">
                                        <flux:button href="{{ route('displacement.camps.gallery', $camp->id) }}"
                                            wire:navigate icon="paper-clip" variant="ghost" size="sm"
                                            tooltip="{{ __('Attachments') }}"
                                            style="{{ $attachmentCount > 0 ? 'color: #3b82f6 !important;' : '' }}">
                                        </flux:button>
                                        @if ($attachmentCount > 0)
                                            <span
                                                class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-blue-500 ring-1 ring-white dark:ring-zinc-900"></span>
                                        @endif
                                    </div>
                                    @can('displacement.camps.create')
                                        <flux:button href="{{ route('displacement.camps.edit', $camp->id) }}" wire:navigate
                                            variant="ghost" size="sm" icon="pencil-square" />
                                        <flux:button wire:click="delete({{ $camp->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this camp?') }}"
                                            variant="ghost" size="sm" icon="trash" class="text-red-500" />
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-zinc-500">
                                {{ __('No displacement camps found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $this->displacementCamps->links() }}
        </div>
    </div>

    {{-- Camps Map Modal --}}
    <flux:modal name="camps-map-modal" class="md:w-[90%] md:h-[90%]">
        <div class="h-full flex flex-col min-h-[600px] p-2">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <flux:heading size="lg">{{ __('Camps Distribution Map') }}</flux:heading>
                    <flux:subheading>{{ __('Gaza Strip - Interactive View') }}</flux:subheading>
                </div>
                <flux:modal.close>
                    <flux:button variant="ghost" icon="x-mark" />
                </flux:modal.close>
            </div>

            {{-- The Map Container --}}
            <div id="leaf-map-container" 
                 class="flex-1 rounded-xl border-2 border-zinc-200 shadow-inner bg-zinc-50" 
                 wire:ignore
                 style="min-height: 600px; height: 600px; width: 100%; position: relative; z-index: 1;">
                 <div class="absolute inset-0 flex items-center justify-center text-zinc-400 italic" id="map-loader-text">
                    {{ __('Loading Map Assets...') }}
                 </div>
            </div>
        </div>
    </flux:modal>

    @push('scripts')
    {{-- Assets loaded directly --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        (function() {
            var mapInstance = null;
            var campsData = @js($this->allCampsForMap);

            function initMapNow() {
                var container = document.getElementById('leaf-map-container');
                
                // If container not found or map already initialized, skip
                if (!container || container.dataset.loaded === 'true') return;
                
                // Ensure Leaflet is loaded
                if (typeof L === 'undefined') return;

                console.log('Initializing Bulletproof Map...');
                
                try {
                    // Mark as loaded to prevent multiple initializations
                    container.dataset.loaded = 'true';
                    
                    // Remove loader text
                    var loader = document.getElementById('map-loader-text');
                    if (loader) loader.remove();

                    // Create Map centered on Gaza
                    mapInstance = L.map(container).setView([31.3547, 34.3088], 11);

                    // Add Tiles
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap'
                    }).addTo(mapInstance);

                    // Add Markers
                    var markerGroup = L.featureGroup();
                    var count = 0;

                    campsData.forEach(function(camp) {
                        if (camp.latitude && camp.longitudes) {
                            var lat = parseFloat(camp.latitude);
                            var lng = parseFloat(camp.longitudes);
                            
                            if (!isNaN(lat) && !isNaN(lng)) {
                                var popup = '<div class="p-2 text-right rtl" dir="rtl">' +
                                            '<h3 class="font-bold text-sm">' + camp.name + '</h3>' +
                                            '<p class="text-xs">' + (camp.address_details || '') + '</p>' +
                                            '<a href="/org-app/displacement-camps/' + camp.id + '/edit" class="text-blue-600 text-[10px] font-bold">تعديل</a>' +
                                            '</div>';
                                
                                L.marker([lat, lng]).bindPopup(popup).addTo(markerGroup);
                                count++;
                            }
                        }
                    });

                    if (count > 0) {
                        markerGroup.addTo(mapInstance);
                        mapInstance.fitBounds(markerGroup.getBounds(), { padding: [30, 30] });
                    }
                    
                    // Fix grey tiles issue in modals
                    setTimeout(function() {
                        mapInstance.invalidateSize();
                    }, 500);

                } catch (e) {
                    console.error('Map Init Error:', e);
                    container.dataset.loaded = 'false';
                }
            }

            // Check every 1 second if the map container has appeared in the DOM
            // This is the most reliable way for Livewire/Flux modals
            setInterval(initMapNow, 1000);
        })();
    </script>
    @endpush
</div>
