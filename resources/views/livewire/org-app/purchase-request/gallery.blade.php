<div class="h-full flex flex-col gap-6">
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

            <flux:modal.trigger name="upload-modal">
                <flux:button variant="primary" icon="plus">{{ __('Upload File') }}</flux:button>
            </flux:modal.trigger>
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
                        <flux:modal.trigger name="upload-modal">
                            <flux:button variant="ghost" class="mt-4" size="sm" icon="plus">
                                {{ __('Upload File') }}</flux:button>
                        </flux:modal.trigger>
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

                                <a href="{{ Storage::url($attachment['path']) }}" style="width: 170px; height: 150px;" loading="lazy"
                                    target="_blank"
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
                                    <button wire:click="deleteAttachment({{ $index }})"
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
                <flux:input type="file" wire:model="uploadFiles" :label="__('Choose Files')" multiple />

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
</div>
