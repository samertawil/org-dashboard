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
                    <flux:input wire:model="activity_name" type="text" :placeholder="__('Enter activity name...')" />
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
