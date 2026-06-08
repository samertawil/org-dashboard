<div class="flex flex-col gap-6" x-data x-on:scroll-to-top.window="window.scrollTo({ top: 0, behavior: 'smooth' })">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ __('Manage educational activity schedule details') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('educational-activity-schedules.index') }}" wire:navigate variant="ghost"
            icon="arrow-left">
            {{ __('Back to List') }}
        </flux:button>
    </div>

    <x-auth-session-status class="text-center" :status="session('message')" />

    <form wire:submit="{{ $type }}" class="grid grid-cols-1 gap-6">

        {{-- Section 1: Activity Information --}}
        <flux:card>
            <div class="flex items-center gap-2 mb-4">
                <flux:icon name="calendar-days" class="size-5 text-blue-500" />
                <flux:heading size="lg">{{ __('Activity Information') }}</flux:heading>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">



                <div class="flex flex-col gap-2">
                    <flux:label>{{ __('Student Group') }}</flux:label>
                    <flux:select wire:model.live="group_id">
                        <option value="">-- {{ __('Select Group') }} --</option>
                        @foreach ($studentGroups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="group_id" />
                </div>

                <div class="flex flex-col gap-2 md:col-span-2">
                    <flux:label>{{ __('Activity Name') }} <span class="text-red-500">*</span></flux:label>
                    <div class="relative w-full" x-data="{
                        open: false,
                        search: '',
                        selectedId: @entangle('activity_name'),
                        selectedLabel: '',
                        options: [
                            @foreach ($this->activityNames as $actName)
                                { id: '{{ $actName->id }}', name: '{{ addslashes($actName->status_name) }}' },
                            @endforeach
                        ],
                        get filteredOptions() {
                            if (!this.search) return this.options;
                            return this.options.filter(opt => opt.name.toLowerCase().includes(this.search.toLowerCase()));
                        },
                        select(id, name) {
                            this.selectedId = id;
                            this.selectedLabel = name;
                            this.open = false;
                            this.search = '';
                        },
                        init() {
                            let updateLabel = () => {
                                let selected = this.options.find(opt => opt.id == this.selectedId);
                                this.selectedLabel = selected ? selected.name : '';
                            };
                            updateLabel();
                            this.$watch('selectedId', () => updateLabel());
                        }
                    }" @click.outside="open = false" x-init="init()">
                        
                        <!-- Trigger Button -->
                        <button type="button" @click="open = !open" 
                            class="flex h-10 w-full items-center justify-between rounded-lg border border-zinc-200 border-b-zinc-300/80 bg-white dark:bg-zinc-800 dark:border-zinc-700 px-3 py-2 text-left text-base sm:text-sm leading-[1.375rem] text-zinc-700 dark:text-zinc-300 shadow-sm outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <span x-text="selectedLabel || '-- {{ __('Select Activity Name') }} --'"></span>
                            <svg class="size-4 text-zinc-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" x-transition 
                            class="absolute z-50 mt-1 w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-2 shadow-md max-h-72 flex flex-col"
                            style="display: none;">
                            <!-- Search Field -->
                            <div class="relative mb-2 shrink-0">
                                <input type="text" x-model="search" placeholder="{{ __('Search activity...') }}"
                                    class="w-full rounded-md border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 py-1.5 px-3 text-sm text-zinc-700 dark:text-zinc-300 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                    @keydown.escape="open = false">
                            </div>

                            <!-- Options List -->
                            <div class="overflow-y-auto space-y-1 flex-1">
                                <button type="button" @click="select('', '')"
                                    class="flex w-full items-center rounded-md px-3 py-2 text-left text-sm text-zinc-500 hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                                    -- {{ __('Select Activity Name') }} --
                                </button>
                                <template x-for="opt in filteredOptions" :key="opt.id">
                                    <button type="button" @click="select(opt.id, opt.name)"
                                        class="flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-sm text-zinc-700 dark:text-zinc-300 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/50 dark:hover:text-indigo-400"
                                        :class="selectedId == opt.id ? 'bg-indigo-50 text-indigo-600 font-medium dark:bg-indigo-900/50 dark:text-indigo-400' : ''">
                                        <span x-text="opt.name"></span>
                                        <svg x-show="selectedId == opt.id" class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                </template>
                                <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-sm text-zinc-400 italic">
                                    {{ __('No results found.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <flux:error name="activity_name" />
                </div>

                <div class="flex flex-col gap-2 md:col-span-2">
                    <flux:label>{{ __('Activity Description') }}</flux:label>
                    <flux:textarea wire:model="activity_description" rows="3"
                        :placeholder="__('Detailed description of the activity...')" />
                    <flux:error name="activity_description" />
                </div>

            </div>
        </flux:card>

        {{-- Section 2: Classification --}}
        <flux:card>
            <div class="flex items-center gap-2 mb-4">
                <flux:icon name="tag" class="size-5 text-purple-500" />
                <flux:heading size="lg">{{ __('Classification') }}</flux:heading>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="flex flex-col gap-2">
                    <flux:label>{{ __('Activity Domain') }}</flux:label>
                    <flux:select wire:model="educational_activity_domain">
                        <option value="">-- {{ __('Select Domain') }} --</option>
                        @foreach ($this->allStatuses->where('p_id_sub', config('appConstant.educational_activity_domains')) as $domain)
                            <option value="{{ $domain->id }}">{{ $domain->status_name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="educational_activity_domain" />
                </div>

                <div class="flex flex-col gap-2">
                    <flux:label>{{ __('Target Category') }}</flux:label>
                    <flux:select wire:model="target_category">
                        <option value="">-- {{ __('Select Category') }} --</option>
                        @foreach (\App\Models\ActivitySchedule::TARGET_CATEGORIES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="target_category" />
                </div>

                <div class="flex flex-col gap-2">
                    <flux:label>{{ __('Assigned Groups') }}</flux:label>
                    <flux:select wire:model="educational_period_groups">
                        <option value="">-- {{ __('Select Group') }} --</option>
                        @foreach ($this->assignedGroups->where('p_id_sub', config('appConstant.student_groups')) as $group)
                            <option value="{{ $group->id }}">{{ $group->status_name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="educational_period_groups" />
                </div>

                <div class="flex flex-col gap-2">
                    <flux:label>{{ __('Responsible Employee') }}</flux:label>
                    <flux:select wire:model="employee_id" wire:key="emp-{{ $group_id }}">
                        <option value="">
                            @if (!$group_id)
                                -- {{ __('Select a group first') }} --
                            @else
                                -- {{ __('Select Employee') }} --
                            @endif
                        </option>
                        @foreach ($this->employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="employee_id" />
                </div>

            </div>
        </flux:card>

        {{-- Section 3: Time Period --}}
        <flux:card>
            <div class="flex items-center gap-2 mb-4">
                <flux:icon name="clock" class="size-5 text-green-500" />
                <flux:heading size="lg">{{ __('Time Period') }}</flux:heading>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="flex flex-col gap-2">
                    <flux:label>{{ __('Start Date & Time') }} <span class="text-red-500">*</span></flux:label>
                    <flux:input wire:model="period_start" type="datetime-local" />
                    <flux:error name="period_start" />
                </div>

                <div class="flex flex-col gap-2">
                    <flux:label>{{ __('End Date & Time') }} <span class="text-red-500">*</span></flux:label>
                    <flux:input wire:model="period_end" type="datetime-local" />
                    <flux:error name="period_end" />
                </div>

            </div>
        </flux:card>

        {{-- Section 4: Additional Settings --}}
        <flux:card>
            <div class="flex items-center gap-2 mb-4">
                <flux:icon name="cog-6-tooth" class="size-5 text-zinc-500" />
                <flux:heading size="lg">{{ __('Additional Settings') }}</flux:heading>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="flex flex-col gap-2 md:col-span-2">
                    <flux:label>{{ __('Notes') }}</flux:label>
                    <flux:textarea wire:model="notes" rows="3" :placeholder="__('Any additional notes...')" />
                    <flux:error name="notes" />
                </div>

                <div class="flex flex-col gap-2">
                    <flux:label>{{ __('Sort Order') }}</flux:label>
                    <flux:input wire:model="sort_order" type="number" min="0" />
                    <flux:error name="sort_order" />
                </div>

                <div class="flex flex-col gap-2">
                    <flux:label>{{ __('Status') }} <span class="text-red-500">*</span></flux:label>
                    <flux:select wire:model="activation">
                        <option value="1">{{ __('Active') }}</option>
                        <option value="0">{{ __('Inactive') }}</option>
                    </flux:select>
                    <flux:error name="activation" />
                </div>

            </div>
        </flux:card>

        {{-- Action Buttons --}}
        <div class="flex justify-end gap-3">
            <flux:button href="{{ route('educational-activity-schedules.index') }}" wire:navigate variant="ghost">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary" icon="check">
                {{ __('Save') }}
            </flux:button>
        </div>
        @include('layouts._show_all_input_error')
    </form>
</div>
