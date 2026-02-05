<div class="p-6">
    <flux:heading size="xl" class="mb-6">{{ __('Activity Gallery') }} <span class="text-sm text-zinc-400">({{ $slides->count() }})</span></flux:heading>

    @if ($images->isEmpty())
        <div class="text-center py-12">
            <flux:icon icon="photo" variant="outline" class="w-12 h-12 mx-auto text-zinc-400 mb-3" />
            <p class="text-zinc-500">{{ __('No images found in the gallery.') }}</p>
        </div>
    @else
    
        <style>
            .masonry-grid {
                column-count: 1;
                column-gap: 1rem;
            }
            @media (min-width: 640px) {
                .masonry-grid { column-count: 2; }
            }
            @media (min-width: 768px) {
                .masonry-grid { column-count: 3; }
            }
            @media (min-width: 1024px) {
                .masonry-grid { column-count: 4; }
            }
            @media (min-width: 1280px) {
                .masonry-grid { column-count: 5; }
            }
            
            /* Prevent items from splitting across columns */
            .masonry-item {
                break-inside: avoid;
                margin-bottom: 1rem;
            }
        </style>

        <div class="w-full max-w-[1400px] mx-auto px-4">
            
            {{-- Masonry Grid Container --}}
            <div class="masonry-grid">
                @foreach($slides as $slide)
                    <div class="masonry-item group relative rounded-xl overflow-hidden shadow-lg bg-zinc-800 break-words">
                        {{-- Image --}}
                        <img src="{{ $slide['url'] }}" 
                             alt="{{ $slide['activity_name'] }}" 
                             class="w-full h-auto object-cover transition-transform duration-700 group-hover:scale-105"
                             loading="lazy">

                        {{-- Overlay (Visible on Hover) --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6">
                            
                            {{-- Content --}}
                            <div class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300">
                                <span class="inline-block bg-blue-600/90 text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wide mb-2">
                                    {{ $slide['sector'] }}
                                </span>
                                <h3 class="text-lg font-bold text-white leading-tight mb-1">
                                    {{ $slide['activity_name'] }}
                                </h3>
                                <div class="text-zinc-300 text-xs flex items-center gap-3">
                                    <div class="flex items-center gap-1">
                                        <flux:icon icon="hashtag" class="w-3 h-3 opacity-70" />
                                        <span>{{ $slide['activity_id'] }}</span>
                                    </div>
                                    <div class="w-px h-3 bg-white/20"></div>
                                    <div class="flex items-center gap-1">
                                        <flux:icon icon="calendar" class="w-3 h-3 opacity-70" />
                                        <span>{{ $slide['start_date'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination Links --}}
            <div class="mt-8 flex justify-center">
                {{ $images->links() }}
            </div>
        </div>
    @endif
</div>
