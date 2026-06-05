<div class="h-full flex flex-col gap-6" x-data="{
    activeImageIndex: 0,
    isCarouselOpen: false,
    zoomLevel: 1,
    touchStartX: 0,
    touchEndX: 0,
    images: [
        @foreach($attachments as $attachment)
            @php
                $ext = $attachment['extension'] ?? pathinfo($attachment['path'], PATHINFO_EXTENSION);
                $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                $url = Storage::url($attachment['path']);
            @endphp
            @if($isImage)
                { url: '{{ $url }}', name: '{{ addslashes($attachment['name'] ?? '') }}' },
            @endif
        @endforeach
    ],
    openCarousel(url) {
        let index = this.images.findIndex(img => img.url === url);
        if (index !== -1) {
            this.activeImageIndex = index;
            this.isCarouselOpen = true;
            this.zoomLevel = 1;
            this.$dispatch('modal-show', { name: 'carousel-modal' });
        }
    },
    nextImage() {
        if (this.images.length === 0) return;
        this.activeImageIndex = (this.activeImageIndex + 1) % this.images.length;
        this.zoomLevel = 1;
    },
    prevImage() {
        if (this.images.length === 0) return;
        this.activeImageIndex = (this.activeImageIndex - 1 + this.images.length) % this.images.length;
        this.zoomLevel = 1;
    },
    zoomIn() {
        if (this.zoomLevel < 3) this.zoomLevel = parseFloat((this.zoomLevel + 0.25).toFixed(2));
    },
    zoomOut() {
        if (this.zoomLevel > 0.5) this.zoomLevel = parseFloat((this.zoomLevel - 0.25).toFixed(2));
    },
    resetZoom() {
        this.zoomLevel = 1;
    },
    handleTouchStart(e) {
        this.touchStartX = e.changedTouches[0].screenX;
    },
    handleTouchEnd(e) {
        this.touchEndX = e.changedTouches[0].screenX;
        this.handleSwipe();
    },
    handleSwipe() {
        let threshold = 50;
        if (this.touchEndX < this.touchStartX - threshold) {
            this.nextImage();
        }
        if (this.touchEndX > this.touchStartX + threshold) {
            this.prevImage();
        }
    }
}">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-zinc-500 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-zinc-800 dark:hover:text-zinc-300 transition-colors">
                    {{ __('Dashboard') }}
                </a>
                <flux:icon icon="chevron-right" size="xs" />
                <span class="text-zinc-800 dark:text-zinc-200 font-medium">{{ __('Unified Gallery') }}</span>
            </div>
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white">{{ __('All Attachments') }}</h2>
        </div>

        <div class="flex items-center gap-3">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search files..." icon="magnifying-glass"
                class="w-full md:w-64" />
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="flex-1 flex overflow-hidden gap-6">
        {{-- Sidebar Filters --}}
        <div class="lg:w-64 flex-shrink-0 hidden lg:flex flex-col gap-6 overflow-y-auto pr-2">

            {{-- Source Filter --}}
            <div class="flex flex-col gap-2">
                <flux:heading level="3" size="sm" class="mb-2 px-2">{{ __('Source') }}</flux:heading>
                <button wire:click="$set('filterSource', '')"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterSource === '' ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                    <div class="flex items-center gap-2">
                        <flux:icon icon="squares-2x2" size="sm" />
                        {{ __('All Sources') }}
                    </div>
                </button>
                @canany(['activity.index', 'activity.create', 'manager.reports.all'])
                    <button wire:click="$set('filterSource', 'activity')"
                        class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterSource === 'activity' ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                        <div class="flex items-center gap-2">
                            <flux:icon icon="briefcase" size="sm" />
                            {{ __('Activities') }}
                        </div>
                    </button>
                @endcanany
                @canany(['purchase_request.index', 'purchase_request.create', 'manager.reports.all'])
                    <button wire:click="$set('filterSource', 'purchase_request')"
                        class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterSource === 'purchase_request' ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                        <div class="flex items-center gap-2">
                            <flux:icon icon="shopping-cart" size="sm" />
                            {{ __('Purchase Requests') }}
                        </div>
                    </button>
                @endcanany
                @canany(['educational-activity-detail.index', 'educational-activity-detail.create', 'manager.reports.all'])
                    <button wire:click="$set('filterSource', 'educational_activity')"
                        class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterSource === 'educational_activity' ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                        <div class="flex items-center gap-2">
                            <flux:icon icon="academic-cap" size="sm" />
                            {{ __('Educational Activity') }}
                        </div>
                    </button>
                @endcanany
                @canany(['educational-activity-detail.index', 'educational-activity-detail.create', 'manager.reports.all'])
                    <button wire:click="$set('filterSource', 'subject_learning')"
                        class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterSource === 'subject_learning' ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                        <div class="flex items-center gap-2">
                            <flux:icon icon="academic-cap" size="sm" />
                            {{ __('Subject Learning') }}
                        </div>
                    </button>
                @endcanany
            </div>

            {{-- Type Filter --}}
            <div class="flex flex-col gap-2">
                <flux:heading level="3" size="sm" class="mb-2 px-2">{{ __('File Type') }}</flux:heading>

                <button wire:click="$set('filterType', '')"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterType === '' ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                    <div class="flex items-center gap-2">
                        <flux:icon icon="squares-2x2" size="sm" />
                        {{ __('All Types') }}
                    </div>
                </button>

                @foreach ($allStatuses as $status)
                    <button wire:click="$set('filterType', '{{ $status->id }}')"
                        class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterType == $status->id ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                        <div class="flex items-center gap-2">
                            @if (\Illuminate\Support\Str::contains(strtolower($status->status_name), ['image', 'photo', 'picture']))
                                <flux:icon icon="photo" size="sm" />
                            @elseif(\Illuminate\Support\Str::contains(strtolower($status->status_name), ['pdf', 'doc', 'file']))
                                <flux:icon icon="document-text" size="sm" />
                            @else
                                <flux:icon icon="folder" size="sm" />
                            @endif
                            {{ $status->status_name }}
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Grid --}}
        <div class="flex-1 h-full overflow-y-auto pr-2 pb-20">
            @if ($attachments->isEmpty())
                <div
                    class="flex flex-col items-center justify-center h-64 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-xl bg-zinc-50 dark:bg-zinc-900/50">
                    <div class="flex flex-col items-center text-center p-6">
                        <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-full mb-3">
                            <flux:icon icon="document-magnifying-glass" class="size-6 text-zinc-400" />
                        </div>
                        <h3 class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('No files found') }}</h3>
                        <p class="mt-1 text-sm text-zinc-500">{{ __('Try adjusting your search or filters.') }}</p>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach ($attachments as $attachment)
                        <div wire:key="att-{{ $attachment['id'] }}"
                            class="group relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all hover:border-zinc-300 dark:hover:border-zinc-600 flex flex-col">

                            {{-- Preview Area --}}
                            <div
                                class="h-24 w-full bg-zinc-100 dark:bg-zinc-900 relative overflow-hidden flex items-center justify-center">
                                @php
                                    $isImage = in_array(strtolower($attachment['extension']), [
                                        'jpg',
                                        'jpeg',
                                        'png',
                                        'gif',
                                        'webp',
                                        'svg',
                                    ]);
                                @endphp

                                <a @if($isImage) href="#" @click.prevent="openCarousel('{{ Storage::url($attachment['path']) }}')" @else href="{{ Storage::url($attachment['path']) }}" target="_blank" @endif
                                    class="w-full h-full flex items-center justify-center group-hover:opacity-90 transition-opacity"
                                    style="width: 150px; height: 150px;">
                                    @if ($isImage)
                                        <img src="{{ Storage::url($attachment['path']) }}"
                                            alt="{{ $attachment['name'] }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="flex flex-col items-center gap-2 text-zinc-400">
                                            @php
                                                $icon = match (strtolower($attachment['extension'])) {
                                                    'pdf' => 'document-text',
                                                    'doc', 'docx' => 'document-text',
                                                    'xls', 'xlsx', 'csv' => 'chart-bar',
                                                    'zip', 'rar' => 'archive-box',
                                                    default => 'document',
                                                };
                                            @endphp
                                            <flux:icon :icon="$icon" class="size-12" />
                                            <span
                                                class="text-xs font-mono uppercase">{{ $attachment['extension'] }}</span>
                                        </div>
                                    @endif
                                </a>
                            </div>

                            {{-- Footer Info --}}
                            <div class="p-3 border-t border-zinc-100 dark:border-zinc-800 flex flex-col gap-1">
                                <h4 class="text-sm font-medium text-zinc-900 dark:text-gray-100 truncate"
                                    title="{{ $attachment['name'] }}">{{ $attachment['name'] }}</h4>
                                <div class="flex flex-col space-y-1 text-xs text-zinc-500">
                                    <div class="">
                                        <span title="{{ $attachment['source_title'] }}"
                                            class="truncate max-w-[100px]">{{ $attachment['source_title'] }}</span>
                                        <span>{{ \Carbon\Carbon::parse($attachment['uploaded_at'])->format('M d, Y') }}</span>
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 whitespace-nowrap truncate max-w-[120px]"
                                            title="{{ $attachment['source'] }}">
                                            {{ $attachment['source'] }}
                                        </span>
                                    </div>
                                    @if (!empty($attachment['group_name']))
                                        <div class="flex items-center">
                                            <flux:icon icon="users" size="xs" class="mr-1" />
                                            <span title="Group"
                                                class="truncate">{{ $attachment['group_name'] }}</span>
                                        </div>
                                    @endif
                                    @if (!empty($attachment['period_start']))
                                        <div class="flex items-center">
                                            <flux:icon icon="calendar" size="xs" class="mr-1" />
                                            <span title="Period Start"
                                                class="truncate">{{ $attachment['period_start'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $attachments->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Carousel Modal --}}
    <flux:modal name="carousel-modal" class="w-full max-w-3xl" variant="bare" @close="isCarouselOpen = false; zoomLevel = 1">
        <div class="relative flex flex-col items-center justify-between min-h-[75vh] md:min-h-[70vh] p-5 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-2xl overflow-hidden"
             @keydown.window.escape="$dispatch('modal-close', { name: 'carousel-modal' })"
             @keydown.window.arrow-right="isCarouselOpen && nextImage()"
             @keydown.window.arrow-left="isCarouselOpen && prevImage()">
            
            {{-- Header Area --}}
            <div class="w-full flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/60 pb-3 z-[1000]">
                <div class="flex flex-col gap-0.5 max-w-[60%]">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400 font-mono tracking-wider" x-text="(activeImageIndex + 1) + ' / ' + images.length"></span>
                    <h3 class="text-zinc-900 dark:text-white font-semibold text-base truncate" x-text="images[activeImageIndex].name || '{{ __('Image Preview') }}'"></h3>
                </div>

                {{-- Action Controls --}}
                <div class="flex items-center gap-1.5">
                    <button type="button" @click="zoomOut()" :disabled="zoomLevel <= 0.5"
                            class="p-2 text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white disabled:opacity-30 disabled:hover:text-zinc-500 transition-colors bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/50 dark:hover:bg-zinc-700/50 rounded-lg border border-zinc-200 dark:border-zinc-800 cursor-pointer"
                            title="{{ __('Zoom Out') }}">
                        <flux:icon icon="minus" class="size-4" />
                    </button>
                    
                    <button type="button" @click="zoomIn()" :disabled="zoomLevel >= 3"
                            class="p-2 text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white disabled:opacity-30 disabled:hover:text-zinc-500 transition-colors bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/50 dark:hover:bg-zinc-700/50 rounded-lg border border-zinc-200 dark:border-zinc-800 cursor-pointer"
                            title="{{ __('Zoom In') }}">
                        <flux:icon icon="plus" class="size-4" />
                    </button>

                    <button type="button" @click="resetZoom()" x-show="zoomLevel !== 1"
                            class="px-2 py-1 text-xs font-medium text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition-colors bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/50 dark:hover:bg-zinc-700/50 rounded-lg border border-zinc-200 dark:border-zinc-800 cursor-pointer"
                            title="{{ __('Reset Zoom') }}">
                        <span x-text="Math.round(zoomLevel * 100) + '%'"></span>
                    </button>

                    <a :href="images[activeImageIndex].url" download
                       class="p-2 text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition-colors bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/50 dark:hover:bg-zinc-700/50 rounded-lg border border-zinc-200 dark:border-zinc-800 cursor-pointer flex items-center justify-center"
                       title="{{ __('Download') }}">
                        <flux:icon icon="arrow-down-tray" class="size-4" />
                    </a>

                    <button type="button" @click="$dispatch('modal-close', { name: 'carousel-modal' })" 
                            class="p-2 text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300 transition-colors bg-red-50 hover:bg-red-100 dark:bg-red-955/20 dark:hover:bg-red-955/40 rounded-lg border border-red-200 dark:border-red-900/50 cursor-pointer"
                            title="{{ __('Close') }}">
                        <flux:icon icon="x-mark" class="size-4" />
                    </button>
                </div>
            </div>

            {{-- Central Frame --}}
            <div class="relative flex-1 w-full flex items-center justify-center overflow-hidden my-4"
                 x-on:touchstart="handleTouchStart($event)"
                 x-on:touchend="handleTouchEnd($event)">
                
                {{-- Left Navigation --}}
                <button type="button" x-show="images.length > 1" @click="prevImage()" 
                        class="absolute left-2 text-zinc-700 hover:text-zinc-900 dark:text-white/70 dark:hover:text-white transition-all p-2.5 bg-zinc-100/90 hover:bg-zinc-200/90 dark:bg-zinc-800/60 dark:hover:bg-zinc-700/80 rounded-full border border-zinc-200 dark:border-zinc-800 shadow-md cursor-pointer pointer-events-auto z-[1000] hover:scale-105 active:scale-95">
                    <flux:icon icon="chevron-left" class="size-5" />
                </button>

                {{-- Right Navigation --}}
                <button type="button" x-show="images.length > 1" @click="nextImage()" 
                        class="absolute right-2 text-zinc-700 hover:text-zinc-900 dark:text-white/70 dark:hover:text-white transition-all p-2.5 bg-zinc-100/90 hover:bg-zinc-200/90 dark:bg-zinc-800/60 dark:hover:bg-zinc-700/80 rounded-full border border-zinc-200 dark:border-zinc-800 shadow-md cursor-pointer pointer-events-auto z-[1000] hover:scale-105 active:scale-95">
                    <flux:icon icon="chevron-right" class="size-5" />
                </button>

                {{-- Image display --}}
                <div class="relative w-full h-[40vh] flex items-center justify-center select-none overflow-hidden transition-all duration-300">
                    <template x-if="images.length > 0">
                        <img :src="images[activeImageIndex].url" 
                             :alt="images[activeImageIndex].name" 
                             class="max-w-full max-h-full object-contain rounded-lg shadow-md transition-transform duration-200 ease-out"
                             :style="'transform: scale(' + zoomLevel + ')'"
                             loading="lazy">
                    </template>
                </div>
            </div>

            {{-- Bottom Thumbnails Strip --}}
            <div class="w-full border-t border-zinc-100 dark:border-zinc-800/60 pt-3 z-[1000]" x-show="images.length > 1">
                <div class="flex items-center justify-center gap-2 overflow-x-auto py-1 px-4 max-w-full no-scrollbar">
                    <template x-for="(img, idx) in images" :key="idx">
                        <button type="button" @click="activeImageIndex = idx; zoomLevel = 1" 
                                class="relative flex-shrink-0 size-12 rounded-lg overflow-hidden border transition-all cursor-pointer hover:opacity-100"
                                :class="activeImageIndex === idx ? 'border-blue-500 ring-2 ring-blue-500 scale-105 opacity-100' : 'border-zinc-200 dark:border-zinc-800 opacity-60 hover:border-zinc-300 dark:hover:border-zinc-700'">
                            <img :src="img.url" :alt="img.name" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>
            
        </div>
    </flux:modal>
</div>
