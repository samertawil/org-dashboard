<div class="flex flex-col gap-6">
    @if (!$student_group_id)
        <div class="flex items-start justify-between">
            <div class="flex flex-col gap-1">
                <flux:heading level="1" size="xl">{{ __('Teacher Student Groups') }}</flux:heading>
                <flux:subheading>{{ __('Manage the assignments between teachers and student groups.') }}</flux:subheading>
            </div>
            
            <flux:button 
                href="{{ route('teacher-student-groups.create') }}" 
                wire:navigate 
                variant="primary"
                icon="plus"
            >
                {{ __('New Assignment') }}
            </flux:button>
        </div>
    @endif

    {{-- Success and Error Messages --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    <div class="flex items-center gap-4">
        <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="{{ __('Search assignments...') }}" />
    </div>

    {{-- Data Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-zinc-500 bg-zinc-50/50 dark:bg-zinc-800/50 uppercase border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-6 py-3">{{ __('Teacher') }}</th>
                        <th class="px-6 py-3">{{ __('Student Group') }}</th>
                        <th class="px-6 py-3">{{ __('Job Title') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($this->mappings as $mapping)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                {{ $mapping->teacher->user->name ?? 'N/A' }} 
                            </td>
                            <td class="px-6 py-4">
                                {{ $mapping->studentGroup->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $mapping->jobTitle->status_name ?? 'N/A' }}
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button 
                                        href="{{ route('teacher-student-groups.edit', $mapping->id) }}" 
                                        wire:navigate 
                                        size="sm" 
                                        variant="ghost" 
                                        icon="pencil-square"
                                    ></flux:button>
                                    
                                    <flux:modal.trigger :name="'delete-mapping-'.$mapping->id">
                                        <flux:button size="sm" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300"></flux:button>
                                    </flux:modal.trigger>
                                </div>

                                {{-- Delete Confirmation Modal --}}
                                <flux:modal :name="'delete-mapping-'.$mapping->id" class="min-w-[22rem]">
                                    <form wire:submit="delete({{ $mapping->id }})">
                                        <flux:heading size="lg">{{ __('Delete Assignment') }}</flux:heading>
                                        <flux:subheading class="mt-2">{{ __('Are you sure you want to delete this assignment? This action cannot be undone.') }}</flux:subheading>
                                        
                                        <div class="flex gap-2 mt-6">
                                            <flux:spacer />
                                            <flux:modal.close>
                                                <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                                            </flux:modal.close>
                                            <flux:button type="submit" variant="danger">{{ __('Delete') }}</flux:button>
                                        </div>
                                    </form>
                                </flux:modal>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-zinc-500 dark:text-zinc-400">
                                <div class="flex flex-col items-center gap-2">
                                    <p>{{ __('No assignments found.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($this->mappings->hasPages())
            <div class="border-t border-zinc-200 dark:border-zinc-700 p-4">
                {{ $this->mappings->links() }}
            </div>
        @endif
    </div>
</div>
