<x-layouts::app :title="__('Dashboard')">

 
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700 overflow-hidden">
                @php
                    // $data = \App\Models\EventAssignee::with('employee')
                    //     ->whereIn('status', ['pending', 'clarification_needed'])
                    //     ->whereHas('employee', function ($query) {
                    //         $query->where('user_id', auth()->id());
                    //     })
                    //     ->get();

                        $data = \App\Reposotries\EventAssigneeRepo::eventAssignees();

                @endphp
                @if ($data->isEmpty())
                    <div
                        class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                        <x-placeholder-pattern
                            class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
                    </div>
                @else
                    <div class="flex   items-center  ">
                        <p class="p-4 text-sm text-neutral-500">Tasks assigned to you.</p>

                        <flux:button href="{{ route('my.tasks') }}" wire:navigate variant="ghost">
                            <span class="text-blue-600 font-semibold"> {{ __('Go Now') }}</span>
                        </flux:button>
                    </div>
                @endif


            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div
                class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern
                    class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div
            class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts::app>
