<div class="h-full flex flex-col gap-6">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2 text-zinc-500">
                <a href="{{ route('activity.index') }}" wire:navigate
                    class="hover:text-zinc-900 dark:hover:text-white transition-colors">
                    {{ __('Activities') }}
                </a>
                <flux:icon icon="chevron-right" size="xs" />
                <span class="text-zinc-900 dark:text-white font-medium">{{ $activity->name }}</span>
            </div>
            <flux:heading level="1" size="xl">{{ __('Activity Gallery') }}</flux:heading>
        </div>

        <div class="flex items-center gap-3">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search files..." icon="magnifying-glass"
                class="w-full md:w-64" />

            <flux:modal.trigger name="upload-modal">
                <flux:button variant="primary" icon="plus">{{ __('Upload File') }}</flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="flex-1 flex flex-col lg:flex-row gap-6 h-full overflow-hidden">

        {{-- Sidebar Filters (Optional but nice for "File Manager" feel) --}}
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

        {{-- Mobile Filter Select --}}
        <div class="lg:hidden">
            <flux:select wire:model.live="filterType" placeholder="{{ __('Filter by type...') }}">
                <option value="">{{ __('All Files') }}</option>
                @foreach ($this->allStatuses as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </flux:select>
        </div>

        {{-- Grid Area --}}
        <div class="flex-1 h-full overflow-y-auto pr-2 pb-20"> <!-- Added padding bottom for scroll space -->

            @if (session()->has('message'))
                <div class="mb-4">
                    <x-auth-session-status class="w-full" :status="session('message')" />
                </div>
            @endif

            @if ($this->attachments->isEmpty())
                <div
                    class="flex flex-col items-center justify-center h-64 border-2 border-dashed border-zinc-200 dark:border-zinc-700 rounded-xl bg-zinc-50 dark:bg-zinc-900/50">
                    <div class="flex flex-col items-center text-center p-6">
                        <div class="p-3 bg-zinc-100 dark:bg-zinc-800 rounded-full mb-3">
                            <flux:icon icon="document-plus" class="size-6 text-zinc-400" />
                        </div>
                        <h3 class="text-sm font-medium text-zinc-900 dark:text-white">{{ __('No files found') }}</h3>
                        <p class="mt-1 text-sm text-zinc-500">{{ __('Upload a new file to get started.') }}</p>
                        <flux:modal.trigger name="upload-modal">
                            <flux:button variant="ghost" class="mt-4" size="sm" icon="plus">
                                {{ __('Upload File') }}</flux:button>
                        </flux:modal.trigger>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-3 md:grid-cols-5 lg:grid-cols-6 xl:grid-cols-8 gap-3">
                    @foreach ($this->attachments as $attachment)
                        <div wire:key="attachment-{{ $attachment->id }}"
                            class="group relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all hover:border-zinc-300 dark:hover:border-zinc-600 flex flex-col">

                            {{-- Preview Area --}}
                            <div class="h-24 w-full bg-black relative overflow-hidden flex items-center justify-center">
                                @if (in_array(strtolower(pathinfo($attachment->attchment_path, PATHINFO_EXTENSION)), [
                                        'jpg',
                                        'jpeg',
                                        'png',
                                        'gif',
                                        'webp',
                                        'svg',
                                    ]))
                                    <div  style="width: 170px; height: 150px;">
                                        <a href="{{ asset('storage/' . $attachment->attchment_path) }}" target="_blank"  title="{{ __('View') }}">  <img src="{{ asset('storage/' . $attachment->attchment_path) }}"
                                            alt="{{ $attachment->notes }}" class="w-full h-full object-contain"
                                            loading="lazy"></a>
                                      
                                    </div>
                                 
                                @else
                                    {{-- File Icon Placeholder --}}
                                    <div
                                        class="flex flex-col items-center gap-2 text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300 transition-colors">
                                        @php
                                            $ext = strtolower(
                                                pathinfo($attachment->attchment_path, PATHINFO_EXTENSION),
                                            );
                                            $icon = match ($ext) {
                                                'pdf' => 'document-text',
                                                'doc', 'docx' => 'document-text',
                                                'xls', 'xlsx', 'csv' => 'document-chart-bar',
                                                'zip', 'rar' => 'archive-box',
                                                default => 'document',
                                            };
                                        @endphp
                                     <div  style="width: 170px; height: 150px;" class= "flex  justify-center items-center">

                                        <a href="{{ asset('storage/' . $attachment->attchment_path) }}" target="_blank"
                                            
                                            title="{{ __('View') }}">  <span class="text-lg" style="color:blue; font-weight:bold;">{{ $ext }}</span>
                                           
                                        </a>
                                    </div>
                                    </div>
                                @endif

                                {{-- Hover Actions Overlay --}}
                                {{-- <div
                                    class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2 backdrop-blur-[2px]">
                                    <a href="{{ asset('storage/' . $attachment->attchment_path) }}" target="_blank"
                                        class="p-2 bg-white/90 dark:bg-zinc-800/90 rounded-full hover:bg-white dark:hover:bg-zinc-700 text-zinc-900 dark:text-white transition-colors"
                                        title="{{ __('View') }}">
                                        <flux:icon icon="eye" size="sm" />
                                    </a>
                                    <button wire:click="deleteAttachment({{ $attachment->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this file?') }}"
                                        class="p-2 bg-white/90 dark:bg-zinc-800/90 rounded-full hover:bg-red-50 dark:hover:bg-red-900/30 text-red-500 hover:text-red-600 transition-colors"
                                        title="{{ __('Delete') }}">
                                        <flux:icon icon="trash" size="sm" />
                                    </button>
                                </div>   --}}
                            </div>

                            {{-- Footer Info --}}
                            <div class="p-3 border-t border-zinc-100 dark:border-zinc-800 flex flex-col gap-0.5">
                                <h4 class="text-sm font-medium text-zinc-900 dark:text-gray-100 truncate"
                                    title="{{ $attachment->notes }}">
                                    {{ $attachment->notes ?: basename($attachment->attchment_path) }}
                                </h4>
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-xs text-zinc-500">{{ $attachment->attachmentType->status_name ?? 'File' }}</span>
                                    <span
                                        class="text-[10px] text-zinc-400">{{ $attachment->created_at->format('M d, Y') }}</span>
                                    <button wire:click="deleteAttachment({{ $attachment->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this file?') }}"
                                        class="text-zinc-400 hover:text-red-600 dark:hover:text-red-500 transition-colors"
                                        title="{{ __('Delete') }}">
                                        <flux:icon icon="trash" size="xs" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Upload Modal --}}
    <flux:modal name="upload-modal" class="w-full md:w-[500px]">
        <div class="space-y-6">
            <flux:heading level="2" size="lg">{{ __('Upload File') }}</flux:heading>

            <div class="space-y-4">
                <flux:input type="file" wire:model="uploadFile" :label="__('Choose File')" />

                <flux:select wire:model="uploadType" :label="__('Type')">
                    <option value="">{{ __('Select Type') }}</option>
                    {{-- Getting statuses via a computed property or injecting them would be cleaner --}}
                    @foreach (\App\Reposotries\StatusRepo::statuses()->where('p_id_sub', config('appConstant.attchment_types')) as $s)
                        <option value="{{ $s->id }}">{{ $s->status_name }}</option>
                    @endforeach
                </flux:select>

                <flux:input wire:model="uploadNotes" :label="__('Notes / Custom Name')"
                    placeholder="e.g. Site Visit Photo" />
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
</div>
