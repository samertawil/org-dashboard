<div class="{{ $isDashboard ? '' : 'container mx-auto' }}">
    {{-- Leaflet Core --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>

    {{-- MarkerCluster --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js"></script>

    {{-- Heatmap --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js"></script>
    
    {{-- Browser Print (Fixed URL) --}}
    <script src="https://cdn.jsdelivr.net/npm/leaflet-browser-print@1.0.6/dist/leaflet.browser.print.min.js"></script>

    {{-- Measure --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-measure@3.1.0/dist/leaflet-measure.css">
    <script src="https://cdn.jsdelivr.net/npm/leaflet-measure@3.1.0/dist/leaflet-measure.js"></script>

    <style>
        .leaflet-container { z-index: 5 !important; }
        .sector-icon {
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            color: white; font-size: 14px; font-weight: bold;
        }
        .map-viewport-container {
            height: 450px;
        }
        @media (min-width: 640px) {
            .map-viewport-container {
                height: {{ $isDashboard ? '250px' : '700px' }};
            }
        }
    </style>

    <div class="space-y-4">
        {{-- Header --}}
        <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm space-y-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                        <flux:icon name="map" class="size-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <flux:heading size="xl">{{ __('Operations Map Dashboard') }}</flux:heading>
                            @if($isDashboard)
                                <flux:button icon="arrows-pointing-out" variant="subtle" size="sm" href="{{ route('operations.map') }}" />
                            @else
                                <flux:button style="color:red !important;" icon="home" variant="subtle" size="sm" href="{{ route('dashboard') }}" />
                            @endif
                        </div>
                        <flux:subheading>{{ __('Gaza Strip Operations View') }}</flux:subheading>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-center sm:justify-end gap-3 w-full sm:w-auto">
                    @php $totalCount = $this->allCamps->count() + $this->allActivities->count(); @endphp
                    
                    @if(!$isDashboard)
                        <div class="flex flex-wrap items-center justify-center gap-2 sm:mr-4 sm:border-r border-zinc-200 dark:border-zinc-700 sm:pr-4">
                            <flux:button id="heatmap-toggle" icon="fire" variant="subtle" size="sm" onclick="window.toggleHeatmap()">
                                <span class="hidden sm:inline">{{ __('Heatmap') }}</span>
                            </flux:button>
                            <flux:button icon="variable" variant="subtle" size="sm" onclick="alert('قم باستخدام أيقونة المسطرة في أعلى يسار الخريطة للبدء في قياس المسافات والمساحات بدقة.')">
                                <span class="hidden sm:inline">{{ __('Measure') }}</span>
                            </flux:button>
                            <flux:button icon="printer" variant="subtle" size="sm" onclick="window.printMap()">
                                <span class="hidden sm:inline">{{ __('Print') }}</span>
                            </flux:button>
                            <flux:button icon="document-arrow-down" variant="subtle" size="sm" onclick="window.exportMapData()">
                                <span class="hidden sm:inline">{{ __('Export') }}</span>
                            </flux:button>
                        </div>
                    @endif

                    <div class="flex items-center gap-3">
                        <flux:badge color="indigo" class="px-3 py-1 font-bold">{{ $totalCount }}</flux:badge>

                        <flux:radio.group wire:model.live="filterType" variant="segmented" size="sm" class="flex-1">
                            <flux:radio value="all" label="{{ __('All') }}" />
                            <flux:radio value="camps" label="{{ __('Camps') }}" />
                            <flux:radio value="activities" label="{{ __('Activities') }}" />
                        </flux:radio.group>
                    </div>
                </div>
            </div>

            @if(!$isDashboard)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-zinc-100 dark:border-zinc-700 pt-4">
                    <flux:select label="{{ __('Go to Camp') }}" searchable onchange="window.moveMapToV2(this)">
                        <option value="">{{ __('Select a Camp...') }}</option>
                        @foreach ($this->campsList as $camp)
                            <option value="{{ $camp->id }}" data-lat="{{ $camp->latitude }}" data-lng="{{ $camp->longitudes }}">
                                {{ $camp->name }} {{ !$camp->latitude ? ' (No GPS)' : '' }}
                            </option>
                        @endforeach
                    </flux:select>

                    <flux:select label="{{ __('Go to Activity') }}" searchable onchange="window.moveMapToV2(this)">
                        <option value="">{{ __('Select an Activity...') }}</option>
                        @foreach ($this->activitiesList as $activity)
                            <option value="{{ $activity->id }}" data-lat="{{ $activity->latitude }}" data-lng="{{ $activity->longitudes }}">
                                {{ $activity->display_name }} {{ !$activity->latitude ? ' (No GPS)' : '' }}
                            </option>
                        @endforeach
                    </flux:select>
                </div>
            @endif
        </div>

        {{-- Map Viewport --}}
        <div class="rounded-2xl border-2 border-zinc-200 dark:border-zinc-700 shadow-lg overflow-hidden relative map-viewport-container" 
             wire:ignore>
            <div id="operations-map-full" style="height: 100%; width: 100%; z-index: 1;"></div>
        </div>

        {{-- Data Bridge --}}
        <input type="hidden" id="camps-data" value="{{ json_encode($this->allCamps) }}">
        <input type="hidden" id="activities-data" value="{{ json_encode($this->allActivities) }}">

        <script>
            (function() {
                var map = null;
                var markerLayer = null;
                var heatmapLayer = null;
                var isHeatmapActive = false;
                var isPicking = false;
                var pendingId = null;
                var pendingName = '';
                var isDashboardMode = @js($isDashboard);

                // Simple Classic Pin Shapes (SVG)
                var createPin = function(color) {
                    return L.divIcon({
                        className: '',
                        html: `
                            <div style="position: relative; width: 34px; height: 34px;">
                                <svg viewBox="0 0 384 512" style="width: 100%; height: 100%; filter: drop-shadow(0 2px 2px rgba(0,0,0,0.4));">
                                    <path fill="${color}" d="M172.268 501.67C26.97 291.031 0 269.413 0 192 0 85.961 85.961 0 192 0s192 85.961 192 192c0 77.413-26.97 99.031-172.268 309.67-9.535 13.774-29.93 13.773-39.464 0z"/>
                                    <circle fill="white" cx="192" cy="192" r="60"/>
                                </svg>
                            </div>`,
                        iconSize: [34, 44], iconAnchor: [17, 44], popupAnchor: [0, -44]
                    });
                };

                var campIcon = createPin('#3b82f6'); // Blue Pin
                var activityDefaultIcon = createPin('#ef4444'); // Red Pin

                function getSectorIcon(sectorId) {
                    var color = '#ef4444';
                    switch(parseInt(sectorId)) {
                        case 1: color = '#ef4444'; break;
                        case 2: color = '#10b981'; break;
                        case 31: color = '#0ea5e9'; break;
                        case 70: color = '#f59e0b'; break;
                        case 67: color = '#8b5cf6'; break;
                        case 55: color = '#ec4899'; break;
                    }
                    return createPin(color);
                }

                window.toggleHeatmap = function() {
                    isHeatmapActive = !isHeatmapActive;
                    var btn = document.getElementById('heatmap-toggle');
                    if (isHeatmapActive) {
                        if (markerLayer) map.removeLayer(markerLayer);
                        drawHeatmap();
                        if(btn) btn.style.backgroundColor = '#fee2e2';
                    } else {
                        if (heatmapLayer) map.removeLayer(heatmapLayer);
                        drawMarkers();
                        if(btn) btn.style.backgroundColor = '';
                    }
                };

                window.printMap = function() {
                    if (map && map.browserPrint) {
                        map.browserPrint.print();
                    } else {
                        window.print();
                    }
                };

                window.exportMapData = function() {
                    try {
                        var camps = JSON.parse(document.getElementById('camps-data').value || '[]');
                        var activities = JSON.parse(document.getElementById('activities-data').value || '[]');
                        var csvRows = [['Type', 'Name', 'Latitude', 'Longitude'].join(',')];
                        camps.forEach(c => { if(c.latitude) csvRows.push(['Camp', '"' + c.name + '"', c.latitude, c.longitudes].join(',')); });
                        activities.forEach(a => { if(a.latitude) csvRows.push(['Activity', '"' + a.name + '"', a.latitude, a.longitudes].join(',')); });
                        var csvContent = "\ufeff" + csvRows.join("\n");
                        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                        var url = URL.createObjectURL(blob);
                        var link = document.createElement("a");
                        link.setAttribute("href", url);
                        link.setAttribute("download", "map_operations_data_" + new Date().toISOString().slice(0,10) + ".csv");
                        link.click();
                    } catch(e) { console.error('Export Error:', e); }
                };

                function drawHeatmap() {
                    if (!map || typeof L.heatLayer === 'undefined') return;
                    if (heatmapLayer) map.removeLayer(heatmapLayer);
                    try {
                        var activities = JSON.parse(document.getElementById('activities-data').value || '[]');
                        var pts = [];
                        activities.forEach(a => { 
                            var lat = parseFloat(a.latitude), lng = parseFloat(a.longitudes);
                            if(!isNaN(lat) && !isNaN(lng)) pts.push([lat, lng, 0.9]); 
                        });
                        heatmapLayer = L.heatLayer(pts, { radius: 35, blur: 20, maxZoom: 10, gradient: {0.4: 'blue', 0.65: 'lime', 1: 'red'} }).addTo(map);
                    } catch(e) { console.error('Heatmap Error:', e); }
                }

                window.moveMapToV2 = function(selectEl) {
                    if (!map || !selectEl.value) return;
                    if (isHeatmapActive) window.toggleHeatmap();
                    var opt = selectEl.querySelector('option[value="' + selectEl.value + '"]');
                    var lat = parseFloat(opt.getAttribute('data-lat')), lng = parseFloat(opt.getAttribute('data-lng'));
                    if (!isNaN(lat) && !isNaN(lng)) {
                        map.setView([lat, lng], 18);
                        if (markerLayer && markerLayer.eachLayer) {
                            markerLayer.eachLayer(l => { 
                                if(l.getLatLng && Math.abs(l.getLatLng().lat - lat) < 0.0001) l.openPopup(); 
                            });
                        }
                    } else if (confirm('تحديد الموقع يدوياً؟')) {
                        isPicking = true; pendingId = selectEl.value; pendingName = opt.text;
                        document.getElementById('operations-map-full').style.cursor = 'crosshair';
                    }
                };

                function startMap() {
                    var container = document.getElementById('operations-map-full');
                    if (!container || typeof L === 'undefined') return;
                    if (container._leaflet_id && map) return;
                    if (container._leaflet_id && !map) { container._leaflet_id = null; }

                    try {
                        var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
                        var sat = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',{maxZoom:20, subdomains:['mt0','mt1','mt2','mt3']});
                        map = L.map(container, { layers: [osm] }).setView(isDashboardMode ? [31.4, 34.35] : [31.3547, 34.3088], isDashboardMode ? 10 : 11);
                        L.control.layers({"OSM": osm, "Satellite": sat}).addTo(map);

                        if (!isDashboardMode && typeof L.Control.Measure !== 'undefined') {
                            new L.Control.Measure({ position:'topleft', primaryLengthUnit:'kilometers', secondaryLengthUnit: 'meters', primaryAreaUnit: 'sqmeters', activeColor: '#f43f5e', completedColor: '#10b981', localization: 'ar', captureZIndex: 10000 }).addTo(map);
                        }
                        if (typeof L.browserPrint !== 'undefined') { L.browserPrint({position:'topleft'}).addTo(map); }

                        map.on('click', function(e) {
                            if (isPicking && pendingId && confirm('اعتماد هذا الموقع لـ ' + pendingName + '؟')) {
                                @this.updateActivityCoordinates(pendingId, e.latlng.lat, e.latlng.lng).then(() => {
                                    alert('تم الحفظ'); isPicking = false; 
                                    document.getElementById('operations-map-full').style.cursor = '';
                                });
                            }
                        });

                        drawMarkers();
                        setTimeout(() => map.invalidateSize(), 500);
                        window.refreshMapMarkers = drawMarkers;
                    } catch (e) { console.error('Map Init Error:', e); }
                }

                function drawMarkers() {
                    if (!map || isHeatmapActive) return;
                    if (markerLayer) map.removeLayer(markerLayer);
                    try {
                        markerLayer = (!isDashboardMode && typeof L.markerClusterGroup !== 'undefined') ? L.markerClusterGroup() : L.featureGroup();
                        
                        var campsInput = document.getElementById('camps-data');
                        var activitiesInput = document.getElementById('activities-data');
                        
                        var camps = JSON.parse(campsInput ? campsInput.value : '[]');
                        var activities = JSON.parse(activitiesInput ? activitiesInput.value : '[]');

                        console.log('Drawing Markers:', { camps: camps.length, activities: activities.length });

                        camps.forEach(c => {
                            try {
                                var lat = parseFloat(c.latitude), lng = parseFloat(c.longitudes);
                                if (!isNaN(lat) && !isNaN(lng)) {
                                    var m = L.marker([lat, lng], {icon:campIcon}).bindPopup('⛺ ' + c.name);
                                    markerLayer.addLayer(m);
                                }
                            } catch(e) { console.warn('Skipping camp marker due to error', e); }
                        });

                        activities.forEach(a => {
                            try {
                                var lat = parseFloat(a.latitude), lng = parseFloat(a.longitudes);
                                if (!isNaN(lat) && !isNaN(lng)) {
                                    var icon = isDashboardMode ? activityDefaultIcon : getSectorIcon(a.sector_id);
                                    var m = L.marker([lat, lng], {icon:icon}).bindPopup('📦 ' + a.name);
                                    markerLayer.addLayer(m);
                                }
                            } catch(e) { console.warn('Skipping activity marker due to error', e); }
                        });
                        map.addLayer(markerLayer);
                    } catch(e) { console.error('Marker Drawing Error:', e); }
                }

                setInterval(startMap, 1000);
                
                const initListener = () => {
                    if (window.Livewire) {
                        Livewire.on('refreshMarkers', () => { if (window.refreshMapMarkers) window.refreshMapMarkers(); });
                    }
                };
                if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initListener); } else { initListener(); }

                document.addEventListener('livewire:navigated', () => { 
                    if (map) { map.remove(); map = null; }
                    startMap(); 
                });
            })();
        </script>
    </div>
</div>
