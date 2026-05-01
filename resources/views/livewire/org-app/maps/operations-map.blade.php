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
    
    {{-- Browser Print --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-browser-print/2.0.2/leaflet.browser.print.min.js"></script>

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

                <div class="flex items-center gap-4">
                    @php $totalCount = $this->allCamps->count() + $this->allActivities->count(); @endphp
                    
                    @if(!$isDashboard)
                        <div class="flex items-center gap-2 mr-4 border-r border-zinc-200 dark:border-zinc-700 pr-4">
                            <flux:button id="heatmap-toggle" icon="fire" variant="subtle" size="sm" onclick="window.toggleHeatmap()">
                                {{ __('Heatmap') }}
                            </flux:button>
                            <flux:button icon="variable" variant="subtle" size="sm" onclick="alert('قم باستخدام أيقونة المسطرة في أعلى يسار الخريطة للبدء في قياس المسافات والمساحات بدقة.')">
                                {{ __('Measure') }}
                            </flux:button>
                            <flux:button icon="printer" variant="subtle" size="sm" onclick="window.printMap()">
                                {{ __('Print') }}
                            </flux:button>
                            <flux:button icon="document-arrow-down" variant="subtle" size="sm" onclick="window.exportMapData()">
                                {{ __('Export') }}
                            </flux:button>
                        </div>
                    @endif

                    <flux:badge color="indigo" class="px-3 py-1 font-bold">{{ $totalCount }} {{ __('Locations') }}</flux:badge>

                    <flux:radio.group wire:model.live="filterType" variant="segmented" size="sm">
                        <flux:radio value="all" label="{{ __('All') }}" />
                        <flux:radio value="camps" label="{{ __('Camps') }}" />
                        <flux:radio value="activities" label="{{ __('Activities') }}" />
                    </flux:radio.group>
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
        <div class="rounded-2xl border-2 border-zinc-200 dark:border-zinc-700 shadow-lg overflow-hidden" 
             wire:ignore style="height: {{ $isDashboard ? '250px' : '700px' }}; width: 100%; position: relative;">
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

                // Icons
                function getSectorIcon(sectorId) {
                    var icon = '📦', color = '#6366f1';
                    switch(parseInt(sectorId)) {
                        case 1: icon = '🍎'; color = '#ef4444'; break;
                        case 2: icon = '💊'; color = '#10b981'; break;
                        case 31: icon = '💧'; color = '#0ea5e9'; break;
                        case 70: icon = '🏠'; color = '#f59e0b'; break;
                        case 67: icon = '🛡️'; color = '#8b5cf6'; break;
                        case 55: icon = '📚'; color = '#ec4899'; break;
                    }
                    return L.divIcon({
                        className: '',
                        html: '<div class="sector-icon" style="background-color:'+color+'; width:32px; height:32px;">'+icon+'</div>',
                        iconSize: [32, 32], iconAnchor: [16, 32], popupAnchor: [0, -32]
                    });
                }

                var campIcon = L.divIcon({
                    className: '',
                    html: '<div class="sector-icon" style="background-color:#3b82f6; width:36px; height:36px; border-radius:10px;">⛺</div>',
                    iconSize: [36, 36], iconAnchor: [18, 36], popupAnchor: [0, -36]
                });

                window.toggleHeatmap = function() {
                    isHeatmapActive = !isHeatmapActive;
                    var btn = document.getElementById('heatmap-toggle');
                    if (isHeatmapActive) {
                        if (markerLayer) map.removeLayer(markerLayer);
                        drawHeatmap();
                        btn.style.backgroundColor = '#fee2e2';
                    } else {
                        if (heatmapLayer) map.removeLayer(heatmapLayer);
                        drawMarkers();
                        btn.style.backgroundColor = '';
                    }
                };

                window.printMap = function() {
                    if (map && map.browserPrint) {
                        map.browserPrint.print();
                    } else {
                        // Fallback to standard print
                        window.print();
                    }
                };

                window.exportMapData = function() {
                    var camps = JSON.parse(document.getElementById('camps-data').value || '[]');
                    var activities = JSON.parse(document.getElementById('activities-data').value || '[]');
                    
                    var csvRows = [];
                    csvRows.push(['Type', 'Name', 'Latitude', 'Longitude'].join(','));
                    
                    camps.forEach(c => {
                        if(c.latitude) csvRows.push(['Camp', '"' + c.name + '"', c.latitude, c.longitudes].join(','));
                    });
                    activities.forEach(a => {
                        if(a.latitude) csvRows.push(['Activity', '"' + a.name + '"', a.latitude, a.longitudes].join(','));
                    });
                    
                    var csvContent = "\ufeff" + csvRows.join("\n"); // Add BOM for Arabic support
                    var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    var url = URL.createObjectURL(blob);
                    var link = document.createElement("a");
                    link.setAttribute("href", url);
                    link.setAttribute("download", "map_operations_data_" + new Date().toISOString().slice(0,10) + ".csv");
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                };

                function drawHeatmap() {
                    if (!map || typeof L.heatLayer === 'undefined') {
                        console.warn('Heatmap plugin not loaded');
                        return;
                    }
                    if (heatmapLayer) map.removeLayer(heatmapLayer);

                    var activities = JSON.parse(document.getElementById('activities-data').value || '[]');
                    var pts = [];

                    activities.forEach(a => { 
                        if(a.latitude && a.longitudes) pts.push([parseFloat(a.latitude), parseFloat(a.longitudes), 0.9]); 
                    });

                    console.log('Drawing Heatmap with ' + pts.length + ' points');
                    
                    heatmapLayer = L.heatLayer(pts, {
                        radius: 35,
                        blur: 20,
                        maxZoom: 10,
                        gradient: {0.4: 'blue', 0.65: 'lime', 1: 'red'}
                    }).addTo(map);
                }

                window.moveMapToV2 = function(selectEl) {
                    if (isDashboardMode || !selectEl.value) return;
                    if (isHeatmapActive) window.toggleHeatmap();
                    var opt = selectEl.querySelector('option[value="' + selectEl.value + '"]');
                    var lat = parseFloat(opt.getAttribute('data-lat')), lng = parseFloat(opt.getAttribute('data-lng'));
                    if (lat && lng) {
                        map.setView([lat, lng], 18);
                        if (markerLayer && markerLayer.eachLayer) {
                            markerLayer.eachLayer(l => { if(l.getLatLng && l.getLatLng().lat == lat) l.openPopup(); });
                        }
                    } else {
                        if (confirm('تحديد الموقع يدوياً؟')) {
                            isPicking = true; pendingId = selectEl.value; pendingName = opt.text;
                            document.getElementById('operations-map-full').style.cursor = 'crosshair';
                        }
                    }
                };

                function startMap() {
                    var container = document.getElementById('operations-map-full');
                    if (!container || map || typeof L === 'undefined') return;

                    try {
                        var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
                        var sat = L.tileLayer('http://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',{maxZoom:20, subdomains:['mt0','mt1','mt2','mt3']});
                        
                        map = L.map(container, { layers: [osm] }).setView(isDashboardMode ? [31.4, 34.35] : [31.3547, 34.3088], isDashboardMode ? 10 : 11);
                        L.control.layers({"OSM": osm, "Satellite": sat}).addTo(map);

                        // Initialize Measure Tool (Not on Dashboard)
                        if (!isDashboardMode && typeof L.Control.Measure !== 'undefined') {
                            new L.Control.Measure({ 
                                position:'topleft', 
                                primaryLengthUnit:'kilometers',
                                secondaryLengthUnit: 'meters',
                                primaryAreaUnit: 'sqmeters',
                                activeColor: '#f43f5e',
                                completedColor: '#10b981',
                                localization: 'ar', // Attempt Arabic
                                captureZIndex: 10000
                            }).addTo(map);
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
                    } catch (e) { console.error('Map Error:', e); }
                }

                function drawMarkers() {
                    if (!map || isHeatmapActive) return;
                    if (markerLayer) map.removeLayer(markerLayer);
                    
                    markerLayer = (!isDashboardMode && typeof L.markerClusterGroup !== 'undefined') 
                                ? L.markerClusterGroup() : L.featureGroup();

                    var camps = JSON.parse(document.getElementById('camps-data').value || '[]');
                    var activities = JSON.parse(document.getElementById('activities-data').value || '[]');

                    camps.forEach(c => {
                        if (c.latitude) {
                            var m = L.marker([c.latitude, c.longitudes], isDashboardMode ? {} : {icon:campIcon})
                                     .bindPopup('⛺ ' + c.name);
                            markerLayer.addLayer(m);
                        }
                    });

                    activities.forEach(a => {
                        if (a.latitude) {
                            var m = L.marker([a.latitude, a.longitudes], isDashboardMode ? {} : {icon:getSectorIcon(a.sector_id)})
                                     .bindPopup('📦 ' + a.name);
                            if (isDashboardMode) m.on('add', function() { if(this._icon) this._icon.style.filter="hue-rotate(140deg)"; });
                            markerLayer.addLayer(m);
                        }
                    });

                    map.addLayer(markerLayer);
                }

                setInterval(startMap, 1000);
                document.addEventListener('livewire:navigated', () => { map = null; startMap(); });
            })();
        </script>
    </div>
</div>
