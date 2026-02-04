<div class="flex flex-col gap-6">
    <div class="flex items-center justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Group Schedule') }}: {{ $group->name }}</flux:heading>
            <flux:subheading>{{ __('View schedule for this group.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('student.group.index') }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Back to Groups') }}
        </flux:button>
    </div>
 
    <!-- Calendar Navigation -->
    <div class="flex items-center justify-between bg-white dark:bg-zinc-800 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm">
        <flux:button wire:click="previousMonth" icon="chevron-left" variant="ghost" />
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $currentMonthName }}</h2>
        <flux:button wire:click="nextMonth" icon="chevron-right" variant="ghost" />
    </div>

    <!-- Calendar Grid -->
    <div class="bg-gray-50/50 dark:bg-zinc-900/50 rounded-xl p-4">
        
        <!-- Days -->
        <div class="flex flex-wrap gap-4">
            @php
                // Calculate width for 7 items per row with gap
                // calc((100% - (6 * 1rem)) / 7)
                $dayClass = 'w-[calc(14.28%-0.86rem)]';
            @endphp
            @foreach($calendar as $day)
                @if($day === null)
                     <div class="{{ $dayClass }} min-h-[140px] rounded-xl"></div>
                @else
                    @php
                        $schedule = $day['schedule'];
                        $isOffDay = $schedule && $schedule->is_off_day;
                        $today = $day['date']->isToday();
                        $hasTime = $schedule && $schedule->start_time;
                    @endphp
                    <div wire:click="{{ $schedule ? 'editSchedule('.$schedule->id.')' : '' }}" @class([
                        $dayClass,
                        'min-h-[140px] p-3 flex flex-col gap-2 rounded-xl border shadow-sm transition-all duration-200 group relative cursor-pointer',
                        'bg-white dark:bg-zinc-800 border-zinc-200 dark:border-zinc-700 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-md' => !$today && !$isOffDay,
                        'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800 ring-1 ring-blue-500/20' => $today,
                        'bg-red-50 dark:bg-red-900/10 border-red-100 dark:border-red-900/30' => $isOffDay,
                    ])>
                        <div class="flex justify-between items-start z-10">
                            <div class="flex items-baseline gap-1">
                                <span @class([
                                    'text-lg font-bold leading-none',
                                    'text-blue-600 dark:text-blue-400' => $today,
                                    'text-zinc-700 dark:text-zinc-300' => !$today && !$isOffDay,
                                    'text-red-600 dark:text-red-400' => $isOffDay,
                                ])>
                                    {{ $day['day'] }}
                                </span>
                                <span class="text-[10px] uppercase font-semibold text-zinc-400">{{ $day['date']->format('D') }}</span>
                            </div>

                            @if($isOffDay)
                                <span class="text-[10px] font-bold text-red-600 bg-red-100 dark:bg-red-900/30 dark:text-red-400 px-2 py-1 rounded-md border border-red-200 dark:border-red-800">OFF</span>
                            @endif
                        </div>



                        <div class="flex-1 flex flex-col justify-end z-10">
                             @if($schedule)
                                 @if(!$isOffDay)
                                    <div class="flex flex-col gap-1">
                                         @if($hasTime)
                                            <div class="flex items-center gap-1.5 text-xs font-semibold text-zinc-900 dark:text-white bg-zinc-100 dark:bg-zinc-700/50 px-2 py-1 rounded-md w-fit">
                                                 <flux:icon name="clock" class="size-3 text-zinc-400" />
                                                 <span>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                            </div>
                                            <div class="px-1">
                                                <span class="text-[10px] font-medium text-zinc-500">{{ $schedule->hours }} {{ __('hrs') }}</span>
                                            </div>
                                         @endif
                                         
                                         @if($schedule->name)
                                             <div class="text-[11px] font-medium text-zinc-700 dark:text-zinc-300 truncate px-1">{{ $schedule->name }}</div>
                                         @endif
                                    </div>
                                 @endif
                                 
                                 @if($schedule->notes)
                                    <div class="mt-2 pt-2 border-t border-dashed border-zinc-200 dark:border-zinc-700/50">
                                        <p class="text-[10px] text-zinc-500 line-clamp-2 leading-relaxed italic">{{ $schedule->notes }}</p>
                                    </div>
                                 @endif
                             @else
                                <div class="h-full flex items-center justify-center">
                                    <span class="text-xs text-zinc-300 dark:text-zinc-600 font-medium dashed border border-transparent group-hover:border-zinc-200 px-2 py-1 rounded">{{ __('No Schedule') }}</span>
                                </div>
                             @endif
                        </div>
                        
                        <!-- Decorative bg -->
                        @if($isOffDay)
                             <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNCIgaGVpZ2h0PSI0IiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxwYXRoIGQ9Ik0xIDNoMXYxSDF6IiBmaWxsPSIjRkNBNUE1IiBmaWxsLW9wYWNpdHk9IjAuMTUiLz48L3N2Zz4=')] opacity-50"></div>
                        @endif



                        @if(empty($isOffDay) && ($day['student_count'] ?? 0) > 0)
                             <!-- View Students Button -->
                             <div class="  bottom-2 right-2 z-[99]">
                                <a 
                                    href="{{ route('student.group.date.students', ['group' => $group->id, 'date' => $day['date']->format('Y-m-d')]) }}"
                                    wire:navigate
                                    wire:click.stop 
                                    class="inline-flex items-center gap-1 bg-green-100 hover:bg-green-200 text-green-700 border border-green-200 px-2 py-1 rounded shadow-sm text-xs font-medium transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-3"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    {{ __('Students') }}
                                </a>
                             </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Edit Schedule Modal -->
    <flux:modal wire:model="showEditModal">
        <div class="p-6">
            <div class="flex flex-col gap-4">
                <div>
                    <flux:heading size="lg">{{ __('Edit Schedule') }}</flux:heading>
                    <flux:subheading>{{ __('Edit schedule details for') }} {{ $edit_date }}</flux:subheading>
                </div>

                <div class="space-y-4">
                     <flux:checkbox wire:model="edit_is_off_day" label="{{ __('Is Off Day') }}" />

                     <div class="grid grid-cols-2 gap-4" x-show="!$wire.edit_is_off_day">
                        <flux:input type="time" wire:model="edit_start_time" label="{{ __('Start Time') }}" />
                        <flux:input type="time" wire:model="edit_end_time" label="{{ __('End Time') }}" />
                     </div>

                     <flux:textarea wire:model="edit_notes" label="{{ __('Notes') }}" />
                </div>

                <div class="flex justify-end gap-2 mt-4">
                    <flux:button wire:click="closeEditModal" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button wire:click="saveSchedule" variant="primary">
                        {{ __('Save Changes') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </flux:modal>
</div>
