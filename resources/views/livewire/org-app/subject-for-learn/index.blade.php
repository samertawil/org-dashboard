<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Current Subjects') }}</flux:heading>
            <flux:subheading>{{ __('Manage subjects for learning.') }}</flux:subheading>
        </div>
        @can('object.create')
        <flux:button href="{{ route('subject.create') }}" wire:navigate variant="primary" icon="plus">
            {{ __('Add Subject') }}
        </flux:button>
        @endcan
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 relative">
            <flux:input wire:model.live="search" :placeholder="__('Search by name...')" icon="magnifying-glass" />
            <div wire:loading wire:target="search" class="absolute right-6 top-1/2 -translate-y-1/2">
                <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
            </div>
        </div>

        @if ($search)
            <div class="p-4 flex items-center justify-end border-b border-zinc-200 dark:border-zinc-700">
                <flux:button wire:click="$set('search', '');" variant="ghost" size="sm" icon="x-mark">
                    {{ __('Clear Filters') }}
                </flux:button>
            </div>
        @endif

        <div class="overflow-x-auto">
            {{-- Table --}}
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th wire:click="sortBy('name')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer transition-colors hover:text-zinc-700 dark:hover:text-zinc-200">
                            <div class="flex items-center gap-1">
                                {{ __('Name') }}
                                <flux:icon
                                    name="{{ $sortField === 'name' ? ($sortDirection === 'asc' ? 'chevron-up' : 'chevron-down') : 'chevron-up-down' }}"
                                    class="size-3" />
                            </div>
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Type') }}</th>

                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('From/To Age') }}</th>

                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Description') }}</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Items') }}</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Status') }}</th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->subjects as $subject)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $subject->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $subject->type?->status_name ?? '-' }}</td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $subject->from_age ?? '-' }}/{{ $subject->to_age ?? '-' }}</td>

                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300 truncate max-w-xs">
                                {{ \Illuminate\Support\Str::limit($subject->description, 30) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                <flux:badge size="sm" color="zinc">{{ $subject->subjects_attchments_count }}
                                </flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php $statusEnum = \App\Enums\GlobalSystemConstant::tryFrom($subject->activation); @endphp
                                <flux:badge color="{{ $subject->activation == 1 ? 'green' : 'zinc' }}" size="sm">
                                    {{ $statusEnum?->label() ?? '-' }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:modal.trigger name="attachments-modal">
                                        <div class="relative">
                                            <flux:button wire:click="selectSubject({{ $subject->id }})"
                                                variant="ghost" size="sm" icon="paper-clip"
                                                style="{{ $subject->subjects_attchments_count > 0 ? 'color: #3b82f6 !important;' : '' }}" />
                                            @if ($subject->subjects_attchments_count > 0)
                                                <span
                                                    class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-blue-500 ring-1 ring-white dark:ring-zinc-900"></span>
                                            @endif
                                        </div>
                                    </flux:modal.trigger>
                                    @can('subject.create')
                                        <flux:button href="{{ route('subject.edit', $subject->id) }}" wire:navigate
                                            variant="ghost" size="sm" icon="pencil-square" />
                                        <flux:button wire:click="delete({{ $subject->id }})"
                                            wire:confirm="{{ __('Are you sure?') }}" variant="ghost" size="sm"
                                            icon="trash" class="text-red-500 hover:text-red-600" />
                                    @endcan

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-zinc-500">
                                {{ __('No subjects found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $this->subjects->links() }}
        </div>
    </div>

    {{-- Attachments Modal --}}
    <flux:modal name="attachments-modal" class="md:w-[600px]">
        <div class="space-y-6">
            <flux:heading level="2" size="lg">{{ __('Attachments For') }}: {{ $selectedSubject?->name }}
            </flux:heading>

            @if ($selectedSubject)
                {{-- Existing Attachments --}}
                <div class="space-y-2">
                    <flux:heading size="md">{{ __('Existing Files') }}</flux:heading>
                    <div class="grid grid-cols-1 gap-2">
                        @foreach ($attachments as $index => $attachment)
                            <div wire:key="attachment-{{ $attachment['id'] ?? $index }}"
                                class="flex items-center justify-between p-2 border rounded-lg border-zinc-200 dark:border-zinc-700">
                                <div class="flex items-center gap-2">
                                    <flux:icon icon="document" size="sm" class="text-zinc-400" />
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-medium">{{ \Illuminate\Support\Str::limit(basename($attachment['attchment_path'] ?? ''), 25) }}</span>
                                        <span class="text-xs text-zinc-500">{{ $attachment['notes'] ?? '' }}</span>
                                        <span class="text-xs text-blue-500 dark:text-blue-400 mt-0.5">
                                            {{ $attachment['attachment_type']['status_name'] ?? '' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @can('subject.index')
                                        <flux:button href="{{ asset('storage/' . ($attachment['attchment_path'] ?? '')) }}"
                                            target="_blank" variant="ghost" size="sm" icon="eye" />
                                    @endcan
                                    @can('subject.create')
                                        <flux:button wire:click="deleteAttachment({{ $attachment['id'] }})"
                                            wire:confirm="Are you sure?" variant="ghost" size="sm" icon="trash"
                                            class="text-red-500" />
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Upload New --}}
                <div class="space-y-4">
                    @can('object.create')
                        <div class="flex items-center justify-between">
                            <flux:heading size="md">{{ __('Upload New') }}</flux:heading>
                            <flux:button wire:click="addAttachment" variant="ghost" size="sm" icon="plus" />
                        </div>
                    @endcan
                    @foreach ($newAttachments as $index => $item)
                        <div wire:key="new-attachment-{{ $index }}"
                            class="p-4 border rounded-lg border-zinc-200 dark:border-zinc-700 space-y-4">
                            <flux:input type="file" wire:model="newAttachments.{{ $index }}.file"
                                :label="__('Choose File')" />
                            <flux:select wire:model="newAttachments.{{ $index }}.attchment_type"
                                :label="__('Type')">
                                <option value="">{{ __('Select Type') }}</option>
                                @foreach ($this->allStatuses->where('p_id_sub', config('appConstant.attchment_types')) as $s)
                                    <option value="{{ $s->id }}">{{ $s->status_name }}</option>
                                @endforeach
                            </flux:select>
                            @can('subject.create')
                                <div class="flex items-end gap-2">
                                    <flux:input wire:model="newAttachments.{{ $index }}.notes"
                                        :label="__('Notes')" class="flex-1" />
                                    <flux:button wire:click="removeNewAttachment({{ $index }})" variant="ghost"
                                        icon="trash" class="text-red-500" />
                                </div>
                            @endcan
                        </div>
                    @endforeach

                    @if (!empty($newAttachments))
                        <div class="flex justify-end">
                            <flux:button wire:click="saveAttachments" variant="primary" icon="cloud-arrow-up">
                                {{ __('Save All New Files') }}
                            </flux:button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </flux:modal>
</div>
