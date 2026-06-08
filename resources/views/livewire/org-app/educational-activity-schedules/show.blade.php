<div class="flex flex-col gap-6" x-data="{
    activeImageIndex: 0,
    isCarouselOpen: false,
    zoomLevel: 1,
    touchStartX: 0,
    touchEndX: 0,
    images: [
        @if ($schedule->educationalActivity && !empty($schedule->educationalActivity->attchments)) @foreach ($schedule->educationalActivity->attchments as $item)
                @php
                    $ext = strtolower($item['extension'] ?? '');
                    $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp','svg']);
                @endphp
                @if ($isImg)
                    { url: '{{ asset('storage/' . $item['path']) }}', name: '{{ addslashes($item['name'] ?? '') }}' }, @endif
        @endforeach
        @endif
    ],
    openCarousel(url) {
        let index = this.images.findIndex(img => img.url === url);
        if (index !== -1) {
            this.activeImageIndex = index;
            this.isCarouselOpen = true;
            this.zoomLevel = 1;
            this.$dispatch('modal-show', { name: 'schedule-lightbox' });
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
    @if (!$isModal)
        <div class="flex items-start justify-between">
            <div class="flex flex-col gap-1">
                <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
                <flux:subheading>{{ __('View educational activity schedule details') }}</flux:subheading>
            </div>
            <div class="flex gap-2">
                @can('educational-activity-schedules.create')
                    <flux:button href="{{ route('educational-activity-schedules.edit', $schedule) }}" wire:navigate
                        variant="primary" icon="pencil-square">
                        {{ __('Edit') }}
                    </flux:button>
                @endcan
                <flux:button href="{{ route('educational-activity-schedules.index') }}" wire:navigate variant="ghost"
                    icon="arrow-left">
                    {{ __('Back to List') }}
                </flux:button>
            </div>
        </div>
    @else
        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
            <div class="flex flex-col gap-1">
                <flux:heading level="1" size="lg">{{ __('Schedule Details') }}</flux:heading>
            </div>

        </div>
    @endif




    {{-- Educational Activity Detail (Report) --}}
    @if ($schedule->educationalActivity)
        <flux:card class="bg-zinc-50 dark:bg-zinc-900/40 border border-zinc-300/80 dark:border-zinc-700">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">

                    <flux:heading size="lg" style="color: blueviolet;">
                        {{ __('Educational Activity Report ') }}
                    </flux:heading>

                </div>
                @can('update', $schedule->educationalActivity)
                    <flux:button href="{{ route('educational-activity-detail.edit', $schedule->educationalActivity->id) }}"
                        wire:navigate variant="ghost" size="sm" icon="pencil-square" class="text-blue-600">
                        {{ __('Edit Report') }}
                    </flux:button>
                @endcan
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Consistent') }}</p>
                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                        {{ $schedule->educationalActivity->consistent ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Status') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">
                        @if ($schedule->educationalActivity->status)
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-zinc-100 dark:bg-zinc-700/50 text-zinc-700 dark:text-zinc-300 border border-zinc-200/50 dark:border-zinc-700/30">
                                {{ $schedule->educationalActivity->status->status_name }}
                            </span>
                        @else
                            —
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Replaced Activity') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">
                        {{ $schedule->educationalActivity->replaced_activity ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Reason') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">
                        {{ $schedule->educationalActivity->replaced_reason ?? '—' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('What Learned') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap">
                        {{ $schedule->educationalActivity->what_learned ?? '—' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Teacher Report Detail') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap">
                        {{ $schedule->educationalActivity->teacher_report_detail ?? '—' }}</p>
                </div>

                @if (!empty($schedule->educationalActivity->attchments))
                    <div class="md:col-span-2 mt-4">
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-3">
                            {{ __('Attachments') }}</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach ($schedule->educationalActivity->attchments as $item)
                                @php
                                    $fileUrl = asset('storage/' . $item['path']);
                                    $isImage = in_array(strtolower($item['extension'] ?? ''), [
                                        'jpg',
                                        'jpeg',
                                        'png',
                                        'gif',
                                        'webp',
                                        'svg',
                                    ]);
                                @endphp
                                <div
                                    class="relative group border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden flex flex-col items-center bg-white dark:bg-zinc-900/50 shadow-sm hover:shadow-md transition-all hover:border-zinc-300 dark:hover:border-zinc-600">
                                    @if ($isImage)
                                        <a href="#" @click.prevent="openCarousel('{{ $fileUrl }}')"
                                            class="w-full h-28 flex items-center justify-center overflow-hidden bg-zinc-50 dark:bg-zinc-800 cursor-pointer">
                                            <img src="{{ $fileUrl }}" alt="{{ $item['name'] }}"
                                                class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300"
                                                loading="lazy">
                                            <div
                                                class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                                                <div
                                                    class="opacity-0 group-hover:opacity-100 transition-opacity bg-white/80 dark:bg-zinc-800/80 rounded-full p-2 shadow-lg backdrop-blur-sm">
                                                    <flux:icon name="magnifying-glass-plus"
                                                        class="size-4 text-zinc-700 dark:text-zinc-300" />
                                                </div>
                                            </div>
                                        </a>
                                    @else
                                        <a href="{{ $fileUrl }}" target="_blank"
                                            class="w-full h-28 flex flex-col items-center justify-center bg-zinc-50 dark:bg-zinc-800 text-zinc-400 group-hover:text-blue-500 transition-colors">
                                            <flux:icon name="document" class="size-10" />
                                            <span
                                                class="text-[10px] font-bold uppercase mt-1">{{ $item['extension'] ?? 'FILE' }}</span>
                                        </a>
                                    @endif
                                    <div class="p-2 w-full border-t border-zinc-100 dark:border-zinc-800">
                                        <span
                                            class="text-xs text-zinc-600 dark:text-zinc-400 truncate block w-full text-center"
                                            title="{{ $item['name'] }}">{{ $item['name'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </flux:card>
    @else
        <div class="flex items-center gap-2  ">
            <flux:icon name="x-circle" class="size-5 text-red-500" />
            <flux:heading size="lg" style="color: red;">{{ __('No Report Uploaded Yet') }}</flux:heading>
        </div>
    @endif

    <div class="border p-2 rounded-lg mt-4">
        {{-- Activity Information --}}
        <flux:card class="m-2">
            <div class="flex items-center gap-2 mb-4">
                <flux:icon name="calendar-days" class="size-5 text-blue-500" />
                <flux:heading size="lg">{{ __('Activity Information') }}</flux:heading>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Activity Name') }}</p>
                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $schedule->activityNameStatus?->status_name ?? $schedule->activity_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Main Activity') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->activity?->name ?? '—' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Description') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->activity_description ?? '—' }}
                    </p>
                </div>
            </div>
        </flux:card>

        {{-- Classification --}}
        <flux:card class="m-2">
            <div class="flex items-center gap-2 mb-4">
                <flux:icon name="tag" class="size-5 text-purple-500" />
                <flux:heading size="lg">{{ __('Classification') }}</flux:heading>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Activity Domain') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">
                        {{ $schedule->activityDomain?->status_name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Target Category') }}</p>
                    @if ($schedule->target_category === 'work_team')
                        <flux:badge color="blue">{{ __('Work Team') }}</flux:badge>
                    @elseif($schedule->target_category === 'children')
                        <flux:badge color="green">{{ __('Children') }}</flux:badge>
                    @else
                        <p class="text-sm text-zinc-400">—</p>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Assigned Groups') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">
                        {{ $schedule->periodGroups?->status_name ?? '—' }}
                    </p>
                    <span
                        class="text-sm text-zinc-700 dark:text-zinc-300">({{ $schedule->periodGroups?->description ?? '—' }})</span>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Student Point') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->group?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Responsible Employee') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->employee?->full_name ?? '—' }}
                    </p>
                </div>
            </div>
        </flux:card>

        {{-- Time Period --}}
        <flux:card class="m-2">
            <div class="flex items-center gap-2 mb-4">
                <flux:icon name="clock" class="size-5 text-green-500" />
                <flux:heading size="lg">{{ __('Time Period') }}</flux:heading>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Day') }}
                    </p>
                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $schedule->day_name_attribute }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Start Date & Time') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">
                        {{ $schedule->period_start?->format('Y-m-d h:i A') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('End Date & Time') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">
                        {{ $schedule->period_end?->format('Y-m-d h:i A') ?? '—' }}</p>
                </div>
            </div>
        </flux:card>


        {{-- Additional Info --}}
        <flux:card class="m-2">
            <div class="flex items-center gap-2 mb-4">
                <flux:icon name="information-circle" class="size-5 text-zinc-500" />
                <flux:heading size="lg">{{ __('Additional Information') }}</flux:heading>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Notes') }}
                    </p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->notes ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Sort Order') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->sort_order }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Status') }}
                    </p>
                    @if ($schedule->activation)
                        <flux:badge color="green">{{ __('Active') }}</flux:badge>
                    @else
                        <flux:badge color="red">{{ __('Inactive') }}</flux:badge>
                    @endif
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Created By') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $schedule->createdBy?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-1">
                        {{ __('Created At') }}</p>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">
                        {{ $schedule->created_at?->format('Y-m-d H:i') ?? '—' }}</p>
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Glassmorphic Lightbox Modal --}}
    <flux:modal name="schedule-lightbox" class="w-full max-w-3xl" variant="bare"
        @close="isCarouselOpen = false; zoomLevel = 1">
        <div class="relative flex flex-col items-center justify-between min-h-[75vh] md:min-h-[70vh] p-5 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-2xl overflow-hidden"
            @keydown.window.escape="$dispatch('modal-close', { name: 'schedule-lightbox' })"
            @keydown.window.arrow-right="isCarouselOpen && nextImage()"
            @keydown.window.arrow-left="isCarouselOpen && prevImage()">

            {{-- Header Area --}}
            <div
                class="w-full flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800/60 pb-3 z-[1000]">
                <div class="flex flex-col gap-0.5 max-w-[60%]">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400 font-mono tracking-wider"
                        x-text="(activeImageIndex + 1) + ' / ' + images.length"></span>
                    <h3 class="text-zinc-900 dark:text-white font-semibold text-base truncate"
                        x-text="images[activeImageIndex]?.name || '{{ __('Image Preview') }}'"></h3>
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

                    <button type="button" @click="$dispatch('modal-close', { name: 'schedule-lightbox' })"
                        class="p-2 text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300 transition-colors bg-red-50 hover:bg-red-100 dark:bg-red-955/20 dark:hover:bg-red-955/40 rounded-lg border border-red-200 dark:border-red-900/50 cursor-pointer"
                        title="{{ __('Close') }}">
                        <flux:icon icon="x-mark" class="size-4" />
                    </button>
                </div>
            </div>

            {{-- Central Frame --}}
            <div class="relative flex-1 w-full flex items-center justify-center overflow-hidden my-4"
                x-on:touchstart="handleTouchStart($event)" x-on:touchend="handleTouchEnd($event)">

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
                <div
                    class="relative w-full h-[40vh] flex items-center justify-center select-none overflow-hidden transition-all duration-300">
                    <template x-if="images.length > 0">
                        <img :src="images[activeImageIndex].url" :alt="images[activeImageIndex].name"
                            class="max-w-full max-h-full object-contain rounded-lg shadow-md transition-transform duration-200 ease-out"
                            :style="'transform: scale(' + zoomLevel + ')'" loading="lazy">
                    </template>
                </div>
            </div>

            {{-- Bottom Thumbnails Strip --}}
            <div class="w-full border-t border-zinc-100 dark:border-zinc-800/60 pt-3 z-[1000]"
                x-show="images.length > 1">
                <div class="flex items-center justify-center gap-2 overflow-x-auto py-1 px-4 max-w-full no-scrollbar">
                    <template x-for="(img, idx) in images" :key="idx">
                        <button type="button" @click="activeImageIndex = idx; zoomLevel = 1"
                            class="relative flex-shrink-0 size-12 rounded-lg overflow-hidden border transition-all cursor-pointer hover:opacity-100"
                            :class="activeImageIndex === idx ? 'border-blue-500 ring-2 ring-blue-500 scale-105 opacity-100' :
                                'border-zinc-200 dark:border-zinc-800 opacity-60 hover:border-zinc-300 dark:hover:border-zinc-700'">
                            <img :src="img.url" :alt="img.name" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>

        </div>
    </flux:modal>
</div>
