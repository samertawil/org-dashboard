<div class="flex flex-col gap-6" x-data="{
    activeImageIndex: 0,
    isCarouselOpen: false,
    zoomLevel: 1,
    touchStartX: 0,
    touchEndX: 0,
    images: [
        @if(!empty($detail->attchments))
            @foreach($detail->attchments as $item)
                @php
                    $ext = strtolower($item['extension'] ?? '');
                    $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp','svg']);
                @endphp
                @if($isImg)
                    { url: '{{ asset('storage/' . $item['path']) }}', name: '{{ addslashes($item['name'] ?? '') }}' },
                @endif
            @endforeach
        @endif
    ],
    openCarousel(url) {
        let index = this.images.findIndex(img => img.url === url);
        if (index !== -1) {
            this.activeImageIndex = index;
            this.isCarouselOpen = true;
            this.zoomLevel = 1;
            this.$dispatch('modal-show', { name: 'detail-lightbox' });
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
        if (this.touchEndX < this.touchStartX - threshold) { this.nextImage(); }
        if (this.touchEndX > this.touchStartX + threshold) { this.prevImage(); }
    }
}">
    @if (!$this->isModal)
        <div class="flex justify-between items-center">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>

            <div class="flex gap-2">
                <flux:button href="{{ route('educational-activity-detail.gallery', $detail->id) }}" wire:navigate
                    variant="ghost" icon="photo" class="text-green-600">
                    {{ __('Gallery') }}
                </flux:button>
                <flux:button href="{{ route('educational-activity-detail.edit', $detail->id) }}" wire:navigate
                    variant="ghost" icon="pencil-square" class="text-blue-600">
                    {{ __('Edit') }}
                </flux:button>
                <flux:button href="{{ route('educational-activity-detail.index') }}" wire:navigate variant="ghost"
                    icon="list-bullet">
                    {{ __('List') }}
                </flux:button>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
        <dl class="grid grid-cols-1 {{ $this->isModal ? '' : 'sm:grid-cols-2' }} gap-x-6 gap-y-8">
            <div class="{{ $this->isModal ? 'col-span-1' : 'sm:col-span-1' }}">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Activity Name') }}</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">
                    {{ $detail->educationalActivity?->activity_name }}</dd>
            </div>

            <div class="{{ $this->isModal ? 'col-span-1' : 'sm:col-span-1' }}">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Consistent') }}</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $detail->consistent ?? '-' }}</dd>
            </div>

            <div class="{{ $this->isModal ? 'col-span-1' : 'sm:col-span-1' }}">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Status') }}</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $detail->status?->status_name ?? '-' }}</dd>
            </div>

            <div class="{{ $this->isModal ? 'col-span-1' : 'sm:col-span-1' }}">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Replaced Activity') }}</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $detail->replaced_activity ?? '-' }}</dd>
            </div>

            <div class="{{ $this->isModal ? 'col-span-1' : 'sm:col-span-1' }}">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Reason') }}</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white">{{ $detail->replaced_reason ?? '-' }}</dd>
            </div>

            <div class="{{ $this->isModal ? 'col-span-1' : 'sm:col-span-2' }}">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('What Learned') }}</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white whitespace-pre-wrap">
                    {{ $detail->what_learned ?? '-' }}</dd>
            </div>

            <div class="{{ $this->isModal ? 'col-span-1' : 'sm:col-span-2' }}">
                <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Teacher Report Detail') }}</dt>
                <dd class="mt-1 text-sm text-zinc-900 dark:text-white whitespace-pre-wrap">
                    {{ $detail->teacher_report_detail ?? '-' }}</dd>
            </div>
        </dl>
    </div>

    @if (!empty($detail->attchments))
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-sm font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Attachments') }}</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @foreach ($detail->attchments as $item)
                    @php
                        $fileUrl = asset('storage/' . $item['path']);
                        $isImage = in_array(strtolower($item['extension'] ?? ''), [
                            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',
                        ]);
                    @endphp
                    <div
                        class="relative group border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden flex flex-col items-center bg-white dark:bg-zinc-900/50 shadow-sm hover:shadow-md transition-all hover:border-zinc-300 dark:hover:border-zinc-600">
                        @if ($isImage)
                            <a href="#" @click.prevent="openCarousel('{{ $fileUrl }}')"
                                class="w-full h-28 flex items-center justify-center overflow-hidden bg-zinc-50 dark:bg-zinc-800 cursor-pointer">
                                <img src="{{ $fileUrl }}" alt="{{ $item['name'] }}"
                                    class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity bg-white/80 dark:bg-zinc-800/80 rounded-full p-2 shadow-lg backdrop-blur-sm">
                                        <flux:icon name="magnifying-glass-plus" class="size-4 text-zinc-700 dark:text-zinc-300" />
                                    </div>
                                </div>
                            </a>
                        @else
                            <a href="{{ $fileUrl }}" target="_blank"
                                class="w-full h-28 flex flex-col items-center justify-center bg-zinc-50 dark:bg-zinc-800 text-zinc-400 group-hover:text-blue-500 transition-colors">
                                <flux:icon name="document" class="size-10" />
                                <span class="text-[10px] font-bold uppercase mt-1">{{ $item['extension'] ?? 'FILE' }}</span>
                            </a>
                        @endif
                        <div class="p-2 w-full border-t border-zinc-100 dark:border-zinc-800">
                            <span class="text-xs text-zinc-600 dark:text-zinc-400 truncate block w-full text-center"
                                title="{{ $item['name'] }}">{{ $item['name'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Glassmorphic Lightbox Modal --}}
    <flux:modal name="detail-lightbox" class="w-full max-w-3xl" variant="bare" @close="isCarouselOpen = false; zoomLevel = 1">
        <div class="relative flex flex-col items-center justify-between min-h-[75vh] md:min-h-[70vh] p-5 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-2xl overflow-hidden"
             @keydown.window.escape="$dispatch('modal-close', { name: 'detail-lightbox' })"
             @keydown.window.arrow-right="isCarouselOpen && nextImage()"
             @keydown.window.arrow-left="isCarouselOpen && prevImage()">
            
            {{-- Header Area --}}
            <div class="w-full flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/60 pb-3 z-[1000]">
                <div class="flex flex-col gap-0.5 max-w-[60%]">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400 font-mono tracking-wider" x-text="(activeImageIndex + 1) + ' / ' + images.length"></span>
                    <h3 class="text-zinc-900 dark:text-white font-semibold text-base truncate" x-text="images[activeImageIndex]?.name || '{{ __('Image Preview') }}'"></h3>
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

                    <a :href="images[activeImageIndex]?.url" download
                       class="p-2 text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white transition-colors bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800/50 dark:hover:bg-zinc-700/50 rounded-lg border border-zinc-200 dark:border-zinc-800 cursor-pointer flex items-center justify-center"
                       title="{{ __('Download') }}">
                        <flux:icon icon="arrow-down-tray" class="size-4" />
                    </a>

                    <button type="button" @click="$dispatch('modal-close', { name: 'detail-lightbox' })" 
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

