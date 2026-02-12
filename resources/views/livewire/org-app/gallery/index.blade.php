<div class="h-full flex flex-col gap-6 p-6">
    <div class="flex flex-col gap-1">
        <flux:heading level="1" size="xl">{{ __('Activity Gallery') }}</flux:heading>
        <flux:subheading>{{ __('Browse images and files grouped by activity.') }}</flux:subheading>
    </div>

    @if ($activities->isEmpty())
        <div class="flex flex-col items-center justify-center h-64 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-xl bg-zinc-50 dark:bg-zinc-900/50">
            <div class="flex flex-col items-center text-center p-6">
                <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-full mb-3">
                    <flux:icon icon="photo" class="size-6 text-zinc-400" />
                </div>
                <h3 class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('No activities with files found') }}</h3>
            </div>
        </div>
    @else
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-4 gap-2">
            @foreach($activities as $activity)
                <a href="{{ route('activity.gallery', $activity->id) }}" wire:key="activity-card-{{ $activity->id }}" wire:navigate class="group flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all hover:border-zinc-300 dark:hover:border-zinc-600">
                    
                    {{-- Header / Info --}}
                    <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex flex-col gap-1">
                        <div class="flex items-start justify-between">
                            <h3 class="font-medium text-zinc-900 dark:text-white truncate pr-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ $activity->name }}
                            </h3>
                             <flux:badge size="sm" inset="top bottom" :color="$activity->status_info['color']">{{ $activity->status_info['name'] }}</flux:badge>
                        </div>
                        <div class="flex items-center text-xs text-zinc-500 gap-2">
                            <span>{{ $activity->start_date }}</span>
                            <span>&bull;</span>
                            <span>{{ $activity->statusSpecificSector->status_name ?? __('Unknown Sector') }}</span>
                        </div>
                    </div>

                    {{-- Image Preview Grid --}}
                    <div class="flex-1 p-2 bg-zinc-50 dark:bg-zinc-800/50">
                        <div class="grid grid-cols-2 gap-1 h-24">
                            @php
                                $previewAttachments = $activity->attachments->take(4);
                                $count = $previewAttachments->count();
                            @endphp

                            @foreach($previewAttachments as $index => $att)
                                <div class="relative overflow-hidden rounded-lg bg-zinc-200 dark:bg-zinc-700 aspect-square">
                                    @php
                                        $ext = strtolower(pathinfo($att->attchment_path, PATHINFO_EXTENSION));
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                    @endphp

                                    @if($isImage)
                                        <img src="{{ asset('storage/' . $att->attchment_path) }}" 
                                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" 
                                             loading="lazy" alt="Preview">
                                    @else
                                        <div class="flex items-center justify-center w-full h-full text-zinc-400">
                                            @php
                                                $icon = match($ext) {
                                                    'pdf' => 'document-text',
                                                    'doc', 'docx' => 'document-text',
                                                    'xls', 'xlsx', 'csv' => 'document-chart-bar',
                                                    'zip', 'rar' => 'archive-box',
                                                    default => 'document' 
                                                };
                                            @endphp
                                            <flux:icon :name="$icon" size="lg" />
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                             {{-- Fill empty slots if less than 4 --}}
                            @for($i = $count; $i < 4; $i++)
                                <div class="bg-zinc-100 dark:bg-zinc-800/50 rounded-lg"></div>
                            @endfor
                        </div>
                    </div>

                    {{-- Footer / Count --}}
                    <div class="px-4 py-3 bg-white dark:bg-zinc-900 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs text-zinc-500">
                        <div class="flex items-center gap-1">
                            <flux:icon icon="paper-clip" size="xs" />
                            <span>{{ $activity->attachments()->count() }} {{ __('Files') }}</span>
                        </div>
                        <span class="group-hover:translate-x-1 transition-transform duration-300 text-blue-600 dark:text-blue-400 font-medium flex items-center gap-1">
                            {{ __('View Gallery') }}
                            <flux:icon icon="arrow-right" size="xs" />
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8 flex justify-center">
            {{ $activities->links() }}
        </div>
    @endif
</div>
