<div>
    {{-- Force Leaflet Assets --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        .leaflet-container { z-index: 10 !important; }
        .custom-search-results { z-index: 99999 !important; position: absolute !important; }
        [x-cloak] { display: none !important; }
    </style>

    {{-- Data Sources for Search --}}
    <input type="hidden" id="camps-search-source" value="{{ json_encode($this->campsList) }}">
    <input type="hidden" id="activities-search-source" value="{{ json_encode($this->activitiesList) }}">

    <div class="flex flex-col space-y-4 relative">
        {{-- Header Section --}}
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm space-y-4 relative z-[10000]">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                        <flux:icon name="map" class="size-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <flux:heading size="xl">{{ __('Operations Map Dashboard') }}</flux:heading>
                        <flux:subheading>{{ __('Gaza Strip Operations View') }}</flux:subheading>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <flux:radio.group wire:model.live="filterType" variant="segmented" size="sm">
                        <flux:radio value="all" label="{{ __('All') }}" />
                        <flux:radio value="camps" label="{{ __('Camps') }}" />
                        <flux:radio value="activities" label="{{ __('Activities') }}" />
                    </flux:radio.group>
                </div>
            </div>

            {{-- Search Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-zinc-100 dark:border-zinc-700 pt-4">
                {{-- Search Camps --}}
                <div x-data="{ 
                    query: '', 
                    open: false, 
                    items: [],
                    init() { this.items = JSON.parse(document.getElementById('camps-search-source').value || '[]') },
                    get filtered() {
                        if (!this.query) return [];
                        return this.items.filter(i => i.name.toLowerCase().includes(this.query.toLowerCase())).slice(0, 10)
                    },
                    select(item) {
                        this.query = item.name;
                        this.open = false;
                        window.goToLocation(item.latitude, item.longitudes, item.name);
                    }
                }" class="relative">
                    <flux:label>{{ __('Go to Camp') }}</flux:label>
                    <div class="relative mt-1">
                        <input type="text" x-model="query" @focus="open = true" @click.outside="open = false"
                            placeholder="{{ __('Search Camps...') }}"
                            class="w-full px-3 pr-9 py-2 text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div x-show="open && filtered.length > 0" x-cloak 
                         class="custom-search-results mt-1 w-full max-h-60 overflow-auto bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-2xl py-1">
                        <template x-for="item in filtered" :key="item.id">
                            <button @click="select(item)" class="w-full text-right px-4 py-2 text-sm hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-zinc-700 dark:text-zinc-300 flex justify-between items-center">
                                <span x-text="item.name"></span>
                                <span x-show="!item.latitude" class="text-[10px] text-red-500 font-bold">No GPS</span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Search Activities --}}
                <div x-data="{ 
                    query: '', 
                    open: false, 
                    items: [],
                    init() { this.items = JSON.parse(document.getElementById('activities-search-source').value || '[]') },
                    get filtered() {
                        if (!this.query) return [];
                        return this.items.filter(i => i.display_name.toLowerCase().includes(this.query.toLowerCase())).slice(0, 10)
                    },
                    select(item) {
                        this.query = item.display_name;
                        this.open = false;
                        window.goToLocation(item.latitude, item.longitudes, item.name);
                    }
                }" class="relative">
                    <flux:label>{{ __('Go to Activity') }}</flux:label>
                    <div class="relative mt-1">
                        <input type="text" x-model="query" @focus="open = true" @click.outside="open = false"
                            placeholder="{{ __('Search Activities...') }}"
                            class="w-full px-3 pr-9 py-2 text-sm rounded-lg border border-zinc-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div x-show="open && filtered.length > 0" x-cloak 
                         class="custom-search-results mt-1 w-full max-h-60 overflow-auto bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg shadow-2xl py-1">
                        <template x-for="item in filtered" :key="item.id">
                            <button @click="select(item)" class="w-full text-right px-4 py-2 text-sm hover:bg-emerald-50 dark:hover:bg-emerald-900/30 text-zinc-700 dark:text-zinc-300 flex justify-between items-center">
                                <span x-text="item.display_name"></span>
                                <span x-show="!item.latitude" class="text-[10px] text-red-500 font-bold">No GPS</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Map Container --}}
        <div class="rounded-2xl border-2 border-zinc-200 dark:border-zinc-700 shadow-lg overflow-hidden bg-white dark:bg-zinc-900 z-[1]" 
             wire:ignore style="height: 700px; width: 100%; position: relative;">
            <div id="operations-map-full" style="height: 100%; width: 100%;"></div>
        </div>

        {{-- Map Data Bridge --}}
        <input type="hidden" id="camps-map-data" value="{{ json_encode($this->allCamps) }}">
        <input type="hidden" id="activities-map-data" value="{{ json_encode($this->allActivities) }}">

        <script>
            (function() {
                var map = null;
                var markerGroup = null;

                function startMap() {
                    var container = document.getElementById('operations-map-full');
                    if (!container || map) return;
                    if (typeof L === 'undefined') return;

                    try {
                        var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
                        map = L.map(container, { layers: [osm] }).setView([31.3547, 34.3088], 11);
                        drawMarkers();
                    } catch (e) { console.error(e); }
                }

                function drawMarkers() {
                    if (!map) return;
                    if (markerGroup) map.removeLayer(markerGroup);
                    markerGroup = L.featureGroup();
                    
                    var camps = JSON.parse(document.getElementById('camps-map-data').value || '[]');
                    var activities = JSON.parse(document.getElementById('activities-map-data').value || '[]');

                    camps.forEach(function(c) {
                        if (c.latitude && c.longitudes) {
                            L.marker([parseFloat(c.latitude), parseFloat(c.longitudes)])
                                .bindPopup('<div style="text-align:right; direction:rtl; color:black;"><b>⛺ ' + c.name + '</b></div>')
                                .addTo(markerGroup);
                        }
                    });

                    activities.forEach(function(a) {
                        if (a.latitude && a.longitudes) {
                            var marker = L.marker([parseFloat(a.latitude), parseFloat(a.longitudes)])
                                .bindPopup('<div style="text-align:right; direction:rtl; color:black;"><b>📦 ' + a.name + '</b></div>');
                            marker.on('add', function() { if(this._icon) this._icon.style.filter = "hue-rotate(140deg)"; });
                            markerGroup.addLayer(marker);
                        }
                    });

                    markerGroup.addTo(map);
                }

                window.goToLocation = function(lat, lng, name) {
                    if (lat && lng) {
                        var latF = parseFloat(lat);
                        var lngF = parseFloat(lng);
                        map.setView([latF, lngF], 18);
                        markerGroup.eachLayer(function(marker) {
                            if (Math.abs(marker.getLatLng().lat - latF) < 0.0001 && Math.abs(marker.getLatLng().lng - lngF) < 0.0001) {
                                marker.openPopup();
                            }
                        });
                    } else {
                        alert('عذراً، هذا الموقع لا يتوفر له إحداثيات GPS.');
                    }
                };

                setInterval(startMap, 1000);
                document.addEventListener('livewire:navigated', function() { map = null; startMap(); });
            })();
        </script>
    </div>
</div>
