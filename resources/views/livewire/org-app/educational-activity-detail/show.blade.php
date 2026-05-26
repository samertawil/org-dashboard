<div class="flex flex-col gap-6">
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
                            'jpg',
                            'jpeg',
                            'png',
                            'gif',
                            'webp',
                            'svg',
                        ]);
                    @endphp
                    <div
                        class="relative group border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden flex flex-col items-center bg-zinc-50 dark:bg-zinc-900/50 p-2">
                        @if ($isImage)
                            <a href="{{ $fileUrl }}" target="_blank"
                                class="w-full h-24 flex items-center justify-center overflow-hidden rounded bg-white dark:bg-zinc-800">
                                <img src="{{ $fileUrl }}" alt="{{ $item['name'] }}"
                                    class="max-h-full max-w-full object-contain">
                            </a>
                        @else
                            <a href="{{ $fileUrl }}" target="_blank"
                                class="w-full h-24 flex flex-col items-center justify-center rounded bg-white dark:bg-zinc-800 text-zinc-400 group-hover:text-blue-500">
                                <flux:icon name="document" class="size-10" />
                                <span
                                    class="text-[10px] font-bold uppercase mt-1">{{ $item['extension'] ?? 'FILE' }}</span>
                            </a>
                        @endif
                        <span class="text-xs text-zinc-600 dark:text-zinc-400 truncate w-full text-center mt-2 px-1"
                            title="{{ $item['name'] }}">{{ $item['name'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
