<div class="flex flex-col gap-6" x-on:modal-show.window="Flux.modal($event.detail.name).show()">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Educational Activity Report') }}</flux:heading>
            <flux:subheading>{{ __('Manage your educational activity reports.') }}</flux:subheading>
        </div>

        @if ($canCreateDetail)
            <flux:button href="{{ route('educational-activity-detail.create') }}" wire:navigate variant="primary"
                icon="plus">
                {{ __('Create New') }}
            </flux:button>
        @endif
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center {{ session('type') == 'error' ? 'text-red-500' : '' }}"
        :status="session('message')" />

    {{-- Search and Table Section --}}
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div
            class="p-4 border-b border-zinc-200 dark:border-zinc-700 flex flex-col sm:flex-row gap-4 items-center relative">
            <div class="  sm:w-48">
                <flux:input type="date" wire:model.live="filterDate" class="w-full" />
            </div>
            <div class="relative flex-1  ">
                <flux:input wire:model.live.debounce.300ms="search" :placeholder="__('Search...')"
                    icon="magnifying-glass" class="w-full" />
                <div wire:loading wire:target="search, filterDate" class="absolute right-6 top-1/2 -translate-y-1/2">
                    <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                </div>
            </div>
        </div>

        @if ($search || $filterDate)
            <div class="p-4 flex items-center justify-end border-b border-zinc-200 dark:border-zinc-700">
                <flux:button wire:click="$set('search', ''); $set('filterDate', '');" variant="ghost" size="sm"
                    icon="x-mark">
                    {{ __('Clear Filters') }}
                </flux:button>
            </div>
        @endif


        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                    {{ __('Showing') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $this->details->firstItem() }}</span>
                    {{ __('to') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $this->details->lastItem() }}</span>
                    {{ __('of') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $this->details->total() }}</span>
                    {{ __('results') }}
                </p>
            </div>
        </div>


        {{-- A. Mobile Card View --}}
        <div class="block md:hidden divide-y divide-zinc-200 dark:divide-zinc-700">
            @forelse ($this->details as $detail)
                <div wire:key="detail-mobile-{{ $detail->id }}"
                    class="p-4 space-y-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                    <div class="flex justify-between items-start gap-4">
                        <div class="flex flex-col">
                            <span
                                class="text-sm font-bold text-zinc-900 dark:text-white">{{ $detail->educationalActivity?->activityNameStatus?->activity_name ?? '—' }}</span>
                            <span class="text-xs text-zinc-500">
                                {{ $detail->educationalActivity?->period_start?->format('Y-m-d') }}
                                <span class="text-indigo-600 dark:text-indigo-400 font-semibold ml-1">
                                    {{ $detail->educationalActivity?->period_start?->format('h:i A') }}
                                </span>
                            </span>
                        </div>
                        @if ($detail->educationalActivity?->periodGroups)
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400 shrink-0">
                                Group : {{ $detail->educationalActivity->periodGroups->status_name }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 gap-3 text-xs">
                        @if ($detail->status)
                            <div>
                                <span
                                    class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Status') }}</span>
                                <div class="text-xs text-zinc-700 dark:text-zinc-205 font-medium leading-tight">
                                    {{ $detail->status->status_name }}
                                </div>
                            </div>
                        @endif
                        @if ($detail->replaced_activity)
                            <div>
                                <span
                                    class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Replaced Activity') }}</span>
                                <div class="text-xs text-zinc-700 dark:text-zinc-205 leading-tight">
                                    {{ $detail->replaced_activity }}
                                </div>
                            </div>
                        @endif
                        <div>
                            <span
                                class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('What Learned') }}</span>
                            <div class="text-xs text-zinc-600 dark:text-zinc-300 leading-tight">
                                {{ str($detail->what_learned)->limit(100) }}
                            </div>
                        </div>
                        <div>
                            <span
                                class="text-[10px] uppercase tracking-wider text-zinc-400 block mb-0.5">{{ __('Teacher Report') }}</span>
                            <div class="text-xs text-zinc-600 dark:text-zinc-300 leading-tight">
                                {{ str($detail->teacher_report_detail)->limit(100) }}
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-800/50">
                        <div class="text-[11px] text-zinc-500">
                            @if ($detail->educationalActivity?->periodGroups?->description)
                                {{ $detail->educationalActivity->periodGroups->description }}
                            @endif
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if ($detail->educationalActivity)
                                @if ($isSuperAdmin || $canViewAllStudents || $detail->educationalActivity->employee_id == $employeeId || in_array($detail->educationalActivity->group_id, $userGroupIds))
                                    <flux:button
                                        x-on:click="$dispatch('open-schedule-details', { id: {{ $detail->educational_activity_id }} })"
                                        size="sm" variant="ghost" icon="eye"
                                        class="text-zinc-500 hover:text-zinc-700" title="{{ __('View') }}" />
                                @endif
                            @endif
                            @if ($canUpdateDetail && !in_array($detail->id, $lockedDetailIds))
                                <flux:button href="{{ route('educational-activity-detail.edit', $detail->id) }}"
                                    wire:navigate size="sm" variant="ghost" icon="pencil-square"
                                    class="text-blue-500 hover:text-blue-700" title="{{ __('Edit') }}" />
                            @endif
                            @if ($canCreateDetail)
                                <flux:button href="{{ route('educational-activity-detail.gallery', $detail->id) }}"
                                    target="_blank" size="sm" variant="ghost" icon="photo"
                                    class="{{ is_array($detail->attchments) && count($detail->attchments) > 0 ? 'text-blue-500 hover:text-blue-700' : '' }}"
                                    style="{{ is_array($detail->attchments) && count($detail->attchments) > 0 ? 'color: #3b82f6 !important;' : '' }}"
                                    title="{{ __('Gallery') }}" />
                            @endif
                            @if ($canDeleteDetail && !in_array($detail->id, $lockedDetailIds))
                                <flux:button wire:click="delete({{ $detail->id }})"
                                    wire:confirm="{{ __('Are you sure you want to delete this record?') }}" size="sm"
                                    variant="ghost" icon="trash" class="text-red-500 hover:text-red-700"
                                    title="{{ __('Delete') }}" />
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-zinc-500 italic">
                    {{ __('No records found.') }}
                </div>
            @endforelse
        </div>

        {{-- B. Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Activity Name') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Activity Date') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Status') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Replaced Activity') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('What Learned') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Teacher Report') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($this->details as $detail)
                        <tr wire:key="detail-{{ $detail->id }}"
                            class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white whitespace-nowrap text-sm">
                                {{ $detail->educationalActivity?->activityNameStatus?->activity_name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="flex flex-col gap-0.5">
                                    <span class="font-medium text-zinc-900 dark:text-white">
                                        {{ $detail->educationalActivity?->period_start?->format('Y-m-d') }}
                                        <span class="text-indigo-600 dark:text-indigo-400 font-semibold ml-1">
                                            {{ $detail->educationalActivity?->period_start?->format('h:i A') }}
                                        </span>
                                    </span>
                                    @if ($detail->educationalActivity?->periodGroups)
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                            Group : {{ $detail->educationalActivity->periodGroups->status_name }}
                                            @if ($detail->educationalActivity->periodGroups->description)
                                                - ({{ $detail->educationalActivity->periodGroups->description }})
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $detail->status?->status_name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $detail->replaced_activity ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ str($detail->what_learned)->limit(50) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ str($detail->teacher_report_detail)->limit(50) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    @if ($detail->educationalActivity)
                                        @if ($isSuperAdmin || $canViewAllStudents || $detail->educationalActivity->employee_id == $employeeId || in_array($detail->educationalActivity->group_id, $userGroupIds))
                                            <flux:button
                                                x-on:click="$dispatch('open-schedule-details', { id: {{ $detail->educational_activity_id }} })"
                                                size="sm" variant="ghost" icon="eye"
                                                class="text-zinc-500 hover:text-zinc-700" title="{{ __('View') }}" />
                                        @endif
                                    @endif
                                    @if ($canUpdateDetail && !in_array($detail->id, $lockedDetailIds))
                                        <flux:button href="{{ route('educational-activity-detail.edit', $detail->id) }}"
                                            wire:navigate size="sm" variant="ghost" icon="pencil-square"
                                            class="text-blue-500 hover:text-blue-700" title="{{ __('Edit') }}" />
                                    @endif
                                    @if ($canCreateDetail)
                                        <flux:button
                                            href="{{ route('educational-activity-detail.gallery', $detail->id) }}"
                                            target="_blank" size="sm" variant="ghost" icon="photo"
                                            class="{{ is_array($detail->attchments) && count($detail->attchments) > 0 ? 'text-blue-500 hover:text-blue-700' : '' }}"
                                            style="{{ is_array($detail->attchments) && count($detail->attchments) > 0 ? 'color: #3b82f6 !important;' : '' }}"
                                            title="{{ __('Gallery') }}" />
                                    @endif
                                    @if ($canDeleteDetail && !in_array($detail->id, $lockedDetailIds))
                                        <flux:button wire:click="delete({{ $detail->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this record?') }}"
                                            size="sm" variant="ghost" icon="trash"
                                            class="text-red-500 hover:text-red-700" title="{{ __('Delete') }}" />
                                    @endif
                                        </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-zinc-500">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon icon="inbox" class="size-8 text-zinc-400" />
                                    <p>{{ __('No records found.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $this->details->links() }}
        </div>
    </div>

    {{-- Details Modal --}}
    <flux:modal name="show-detail-modal" class="md:w-[700px]">
        <div class="flex flex-col gap-6">
            @if ($selectedDetail)
                <div class="flex justify-between items-center border-b border-zinc-100 dark:border-zinc-800 pb-4">
                    <flux:heading level="2" size="lg">{{ __('Educational Activity Detail') }}
                    </flux:heading>
                    <div class="flex gap-2">
                        @if ($canUpdateDetail && !in_array($selectedDetail->id, $lockedDetailIds))
                            <flux:button href="{{ route('educational-activity-detail.edit', $selectedDetail->id) }}"
                                wire:navigate variant="ghost" size="sm" icon="pencil-square"
                                class="text-blue-500 hover:text-blue-700" title="{{ __('Edit') }}">
                                {{ __('Edit') }}
                            </flux:button>
                        @endif
                        @if ($canCreateDetail)
                            <flux:button href="{{ route('educational-activity-detail.gallery', $selectedDetail->id) }}"
                                target="_blank" variant="ghost" size="sm" icon="photo"
                                class="text-indigo-600 hover:text-indigo-700" title="{{ __('Gallery') }}">
                                {{ __('Gallery') }}
                            </flux:button>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <flux:label>{{ __('Activity Name') }}</flux:label>
                        <div class="text-sm text-zinc-900 dark:text-white font-medium mt-1">
                            {{ $selectedDetail->educationalActivity?->activityNameStatus?->activity_name ?? '—' }}
                        </div>
                    </div>

                    <div>
                        <flux:label>{{ __('Activity Date') }}</flux:label>
                        <div class="text-sm text-zinc-900 dark:text-white mt-1">
                            {{ $selectedDetail->educationalActivity?->period_start?->format('Y-m-d') }}
                            <span class="text-indigo-600 dark:text-indigo-400 font-semibold ml-1">
                                {{ $selectedDetail->educationalActivity?->period_start?->format('h:i A') }}
                            </span>
                        </div>
                    </div>

                    @if ($selectedDetail->educationalActivity?->periodGroups)
                        <div>
                            <flux:label>{{ __('Period Group') }}</flux:label>
                            <div class="text-sm text-zinc-900 dark:text-white mt-1">
                                Group : {{ $selectedDetail->educationalActivity->periodGroups->status_name }}
                                @if ($selectedDetail->educationalActivity->periodGroups->description)
                                    - ({{ $selectedDetail->educationalActivity->periodGroups->description }})
                                @endif
                            </div>
                        </div>
                    @endif

                    <div>
                        <flux:label>{{ __('Consistent') }}</flux:label>
                        <div class="text-sm text-zinc-900 dark:text-white mt-1">
                            {{ $selectedDetail->consistent ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <flux:label>{{ __('Status') }}</flux:label>
                        <div class="text-sm text-zinc-900 dark:text-white mt-1">
                            {{ $selectedDetail->status?->status_name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <flux:label>{{ __('Replaced Activity') }}</flux:label>
                        <div class="text-sm text-zinc-900 dark:text-white mt-1">
                            {{ $selectedDetail->replaced_activity ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <flux:label>{{ __('Reason') }}</flux:label>
                        <div class="text-sm text-zinc-900 dark:text-white mt-1">
                            {{ $selectedDetail->replaced_reason ?? '-' }}
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <flux:label>{{ __('What Learned') }}</flux:label>
                        <div
                            class="text-sm text-zinc-900 dark:text-white bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700 whitespace-pre-wrap mt-1">
                            {{ $selectedDetail->what_learned ?? '-' }}
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <flux:label>{{ __('Teacher Report Detail') }}</flux:label>
                        <div
                            class="text-sm text-zinc-900 dark:text-white bg-zinc-50 dark:bg-zinc-900/50 rounded-lg p-3 border border-zinc-200 dark:border-zinc-700 whitespace-pre-wrap mt-1">
                            {{ $selectedDetail->teacher_report_detail ?? '-' }}
                        </div>
                    </div>
                </div>

                {{-- Attachments Section inside View modal --}}
                <div class="border-t border-zinc-100 dark:border-zinc-800 pt-4">
                    <flux:heading level="3" size="md" class="mb-3 flex items-center gap-2">
                        <flux:icon icon="paper-clip" class="size-4 text-zinc-500" />
                        {{ __('Attachments') }}
                        @if (is_array($selectedDetail->attchments) && count($selectedDetail->attchments) > 0)
                            <flux:badge size="sm" color="blue" class="ml-1">
                                {{ count($selectedDetail->attchments) }}</flux:badge>
                        @endif
                    </flux:heading>

                    @if (is_array($selectedDetail->attchments) && count($selectedDetail->attchments) > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach ($selectedDetail->attchments as $index => $attachment)
                                @php
                                    $path = $attachment['path'] ?? '';
                                    $ext = pathinfo($path, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
                                    $attachmentUrl = Storage::url($path);
                                @endphp
                                <div
                                    class="group relative bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden shadow-xs hover:border-zinc-300 dark:hover:border-zinc-700 flex flex-col">
                                    <div
                                        class="h-20 w-full bg-zinc-100 dark:bg-zinc-950 flex items-center justify-center relative overflow-hidden">
                                        <a href="{{ $attachmentUrl }}" target="_blank"
                                            class="w-full h-full flex items-center justify-center hover:opacity-90 transition-opacity">
                                            @if ($isImage)
                                                <img src="{{ $attachmentUrl }}"
                                                    alt="{{ $attachment['name'] ?? 'attachment' }}"
                                                    class="w-full h-full object-contain">
                                            @else
                                                <div class="flex flex-col items-center gap-1 text-zinc-400">
                                                    @php
                                                        $icon = match (strtolower($ext)) {
                                                            'pdf' => 'document-text',
                                                            'doc', 'docx' => 'document-text',
                                                            'xls', 'xlsx', 'csv' => 'chart-bar',
                                                            'zip', 'rar' => 'archive-box',
                                                            default => 'document',
                                                        };
                                                    @endphp
                                                    <flux:icon :icon="$icon" class="size-6" />
                                                    <span
                                                        class="text-[9px] font-mono uppercase">{{ $ext }}</span>
                                                </div>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="p-1.5 border-t border-zinc-100 dark:border-zinc-800 text-center">
                                        <span class="text-[10px] text-zinc-500 truncate block w-full px-1"
                                            title="{{ $attachment['name'] ?? 'file' }}">
                                            {{ $attachment['name'] ?? 'file' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div
                            class="text-sm text-zinc-500 italic p-3 bg-zinc-50 dark:bg-zinc-900/50 rounded-lg border border-dashed border-zinc-200 dark:border-zinc-700 text-center">
                            {{ __('No attachments found.') }}
                        </div>
                    @endif
                </div>

                <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:button x-on:click="$dispatch('modal-close', { name: 'show-detail-modal' })">
                        {{ __('Close') }}</flux:button>
                </div>
            @endif
        </div>
    </flux:modal>
</div>
