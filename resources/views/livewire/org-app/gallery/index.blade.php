<div class="h-full flex flex-col gap-6">
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
                <button wire:click="$set('filterSource', 'activity')"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterSource === 'activity' ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                    <div class="flex items-center gap-2">
                        <flux:icon icon="briefcase" size="sm" />
                        {{ __('Activities') }}
                    </div>
                </button>
                <button wire:click="$set('filterSource', 'purchase_request')"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterSource === 'purchase_request' ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                    <div class="flex items-center gap-2">
                        <flux:icon icon="shopping-cart" size="sm" />
                        {{ __('Purchase Requests') }}
                    </div>
                </button>
                <button wire:click="$set('filterSource', 'subject_learning')"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ $filterSource === 'subject_learning' ? 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}">
                    <div class="flex items-center gap-2">
                        <flux:icon icon="academic-cap" size="sm" />
                        {{ __('Subject Learning') }}
                    </div>
                </button>
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
                <div class="flex flex-col items-center justify-center h-64 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-xl bg-zinc-50 dark:bg-zinc-900/50">
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

                            {{-- Source Badge --}}
                             <div class="absolute top-2 left-2 z-10">
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-medium bg-zinc-100/90 dark:bg-zinc-800/90 backdrop-blur-sm text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 shadow-sm">
                                    {{ $attachment['source'] }}
                                </span>
                            </div> 

                            {{-- Preview Area --}}
                            <div class="h-24 w-full bg-zinc-100 dark:bg-zinc-900 relative overflow-hidden flex items-center justify-center">
                                @php
                                    $isImage = in_array(strtolower($attachment['extension']), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                @endphp

                                <a href="{{ Storage::url($attachment['path']) }}" target="_blank"  class="w-full h-full flex items-center justify-center group-hover:opacity-90 transition-opacity" style="width: 150px; height: 150px;" > 
                                    @if ($isImage)
                                        <img src="{{ Storage::url($attachment['path']) }}" alt="{{ $attachment['name'] }}"
                                            class="w-full h-full object-cover">
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
                                            <span class="text-xs font-mono uppercase">{{ $attachment['extension'] }}</span>
                                        </div>
                                    @endif
                                </a>
                            </div>

                            {{-- Footer Info --}}
                            <div class="p-3 border-t border-zinc-100 dark:border-zinc-800 flex flex-col gap-1">
                                <h4 class="text-sm font-medium text-zinc-900 dark:text-gray-100 truncate"
                                    title="{{ $attachment['name'] }}">
                                    {{ $attachment['name'] }}
                                </h4>
                                <div class="flex items-center justify-between text-xs text-zinc-500">
                                    <span title="{{ $attachment['source_title'] }}" class="truncate max-w-[100px]">
                                        {{ $attachment['source_title'] }}
                                    </span>
                                    <span>
                                        {{ \Carbon\Carbon::parse($attachment['uploaded_at'])->format('M d, Y') }}
                                    </span>
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
</div>
