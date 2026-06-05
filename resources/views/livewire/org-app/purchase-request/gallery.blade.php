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
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2 text-zinc-500">
                <a href="{{ route('purchase_request.index') }}" wire:navigate
                    class="hover:text-zinc-900 dark:hover:text-white transition-colors">
                    {{ __('Purchase Requests') }}
                </a>
                <flux:icon icon="chevron-right" size="xs" />
                <span class="text-zinc-900 dark:text-white font-medium">{{ $purchaseRequisition->request_number }}</span>
            </div>
            <flux:heading level="1" size="xl">{{ __('Attachments Gallery') }}</flux:heading>
        </div>

        <div class="flex items-center gap-3">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search files..." icon="magnifying-glass"
                class="w-full md:w-64" />
                @can('purchase_request.create')
            <flux:modal.trigger name="upload-modal">
                <flux:button variant="primary" icon="plus">{{ __('Upload File') }}</flux:button>
            </flux:modal.trigger>
            @endcan
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="flex-1 flex overflow-hidden gap-6">
        {{-- Sidebar Filters --}}
        <div class="lg:w-64 flex-shrink-0 hidden lg:flex flex-col gap-2">
            <flux:heading level="3" size="sm" class="mb-2 px-2">{{ __('Filters') }}</flux:heading>

            <button wire:click="$set('filterType', '')"
                class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterType === '' ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                <div class="flex items-center gap-2">
                    <flux:icon icon="squares-2x2" size="sm" />
                    {{ __('All Files') }}
                </div>
            </button>

            @foreach ($this->allStatuses as $status)
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

        {{-- Grid --}}
        <div class="flex-1 h-full overflow-y-auto pr-2 pb-20">
            @if ($attachments->isEmpty())
                <div
                    class="flex flex-col items-center justify-center h-64 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-xl bg-zinc-50 dark:bg-zinc-900/50">
                    <div class="flex flex-col items-center text-center p-6">
                        <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-full mb-3">
                            <flux:icon icon="document-plus" class="size-6 text-zinc-400" />
                        </div>
                        <h3 class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('No files found') }}</h3>
                        <p class="mt-1 text-sm text-zinc-500">{{ __('Upload a new file to get started.') }}</p>
                        @can('purchase_request.create')
                        <flux:modal.trigger name="upload-modal">
                            <flux:button variant="ghost" class="mt-4" size="sm" icon="plus">
                                {{ __('Upload File') }}</flux:button>
                        </flux:modal.trigger>    
                        @endcan
                       
                    </div>
                </div>
            @else
                <div class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-3">
                    @foreach ($attachments as $index => $attachment)
                        <div wire:key="attachment-{{ $index }}"
                            class="group relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all hover:border-zinc-300 dark:hover:border-zinc-600 flex flex-col">

                            {{-- Preview Area --}}
                            <div
                                class="h-24 w-full bg-zinc-100 dark:bg-zinc-900 relative overflow-hidden flex items-center justify-center">
                                @php
                                    $ext =
                                        $attachment['extension'] ?? pathinfo($attachment['path'], PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                @endphp

                                <a @if($isImage) href="#" @click.prevent="openCarousel('{{ Storage::url($attachment['path']) }}')" @else href="{{ Storage::url($attachment['path']) }}" target="_blank" @endif style="width: 170px; height: 150px;"
                                    loading="lazy"
                                    class="w-full h-full flex items-center justify-center group-hover:opacity-90 transition-opacity">
                                    @if ($isImage)
                                        <img src="{{ Storage::url($attachment['path']) }}"
                                            alt="{{ $attachment['name'] }}" class="w-full h-full object-contain">
                                    @else
                                        <div class="flex flex-col items-center gap-2 text-zinc-400">
                                            @php
                                                $icon = match (strtolower($ext)) {
                                                    'pdf' => 'document-text',
                                                    'doc', 'docx' => 'document-text',
                                                    'xls', 'xlsx', 'csv' => 'chart-bar',
                                                    'zip', 'rar' => 'archive-box',
                                                    default => 'document',
                                                };
                                            @endphp
                                            <flux:icon :icon="$icon" class="size-10" />
                                            <span class="text-xs font-mono uppercase">{{ $ext }}</span>
                                        </div>
                                    @endif
                                </a>
                            </div>

                            {{-- Footer Info --}}
                            <div class="p-3 border-t border-zinc-100 dark:border-zinc-800 flex flex-col gap-0.5">
                                <h4 class="text-sm font-medium text-zinc-900 dark:text-gray-100 truncate"
                                    title="{{ $attachment['name'] }}">
                                    {{ $attachment['name'] }}
                                </h4>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] text-zinc-400">
                                        {{ isset($attachment['uploaded_at']) ? \Carbon\Carbon::parse($attachment['uploaded_at'])->format('M d, Y') : '' }}
                                    </span>
                                    @can('purchase_request.create')
                                        <button wire:click="deleteAttachment({{ $index }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this file?') }}"
                                            class="text-zinc-400 hover:text-red-600 dark:hover:text-red-500 transition-colors"
                                            title="{{ __('Delete') }}">
                                            <flux:icon icon="trash" size="xs" />
                                        </button>
                                    @endcan

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @can('purchase_request.create')
        {{-- Upload Modal --}}
        <flux:modal name="upload-modal" class="w-full md:w-[500px]">

            <div class="space-y-6">
                <flux:heading level="2" size="lg">{{ __('Upload File') }}</flux:heading>

                <div class="space-y-4">
                    <div
                        x-data="{ isUploading: false, progress: 0 }"
                        x-on:livewire-upload-start="isUploading = true"
                        x-on:livewire-upload-finish="isUploading = false"
                        x-on:livewire-upload-error="isUploading = false"
                        x-on:livewire-upload-progress="progress = $event.detail.progress"
                    >
                        <flux:input type="file" wire:model="uploadFiles" :label="__('Choose Files')" multiple />
                        
                        <div x-show="isUploading" class="mt-3">
                            <div class="w-full bg-zinc-200 dark:bg-zinc-700 h-2 rounded-full overflow-hidden">
                                <div class="bg-blue-600 h-full rounded-full transition-all duration-150" x-bind:style="'width: ' + progress + '%'"></div>
                            </div>
                            <div class="flex justify-between text-[10px] text-zinc-500 mt-1">
                                <span>{{ __('Uploading...') }}</span>
                                <span x-text="progress + '%'"></span>
                            </div>
                        </div>
                    </div>

                    <flux:input wire:model="uploadNotes" :label="__('Display Name (Optional)')"
                        placeholder="e.g. Invoice #123" />
                </div>

                <div class="flex justify-end gap-2 mt-2">
                    <flux:button variant="ghost" x-on:click="$dispatch('modal-close', { name: 'upload-modal' })">
                        {{ __('Cancel') }}</flux:button>
                    <flux:button wire:click="saveUploadedFile" variant="primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveUploadedFile">{{ __('Upload') }}</span>
                        <span wire:loading wire:target="saveUploadedFile">{{ __('Uploading...') }}</span>
                    </flux:button>
                </div>
            </div>

        </flux:modal>
    @endcan

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
