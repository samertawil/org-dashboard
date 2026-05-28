<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ __('Enter the details for the Educational Activity below.') }}</flux:subheading>
        </div>

        @if (!$this->isModal)
            <span title="{{ __('Return to list') }}" class="w-full sm:w-auto">
                <flux:button href="{{ route('educational-activity-detail.index') }}" wire:navigate variant="ghost"
                    icon="list-bullet" class="w-full">
                    {{ __('List') }}
                </flux:button>
            </span>
        @endif
    </div>

    <x-auth-session-status class="text-center {{ session('type') == 'error' ? 'text-red-500' : '' }}"
        :status="session('message')" />

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <form wire:submit="{{ isset($type) ? $type : 'save' }}"
            class="grid grid-cols-1 {{ $this->isModal ? '' : 'md:grid-cols-2 lg:grid-cols-3' }} gap-6">

            <div
                class="{{ $this->isModal ? 'col-span-1' : 'md:col-span-2 lg:col-span-3' }} border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-2">
                <flux:heading size="lg" class="text-blue-600 dark:text-blue-400">{{ __('Basic Information') }}
                </flux:heading>
            </div>

            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Educational Activity') }}</flux:label>
                <flux:select wire:model="educational_activity_id">
                    <option value="">{{ __('Select Activity') }}</option>
                    @foreach ($this->activitySchedules as $schedule)
                        <option value="{{ $schedule->id }}"> {{ $schedule->period_start?->format('Y-m-d') }}
                            ({{ $schedule->activity_name }})
                            (مجموعة {{ $schedule->periodGroups->status_name }})

                        </option>
                    @endforeach
                </flux:select>
                <flux:error name="educational_activity_id" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Consistent') }}</flux:label>
                <flux:input type="number" wire:model="consistent" :placeholder="__('Enter consistent value')" />
                <flux:error name="consistent" />
            </flux:field>

            <div class="{{ $this->isModal ? 'col-span-1' : 'md:col-span-2 lg:col-span-3' }}">
                <flux:field>
                    <flux:label>{{ __('What Learned') }}</flux:label>
                    <flux:textarea wire:model="what_learned" :placeholder="__('Describe what was learned')"
                        rows="3" />
                    <flux:error name="what_learned" />
                </flux:field>
            </div>

            <div class="{{ $this->isModal ? 'col-span-1' : 'md:col-span-2 lg:col-span-3' }}">
                <flux:field>
                    <flux:label>{{ __('Teacher Report Detail') }}</flux:label>
                    <flux:textarea wire:model="teacher_report_detail" :placeholder="__('Teacher report details')"
                        rows="3" />
                    <flux:error name="teacher_report_detail" />
                </flux:field>
            </div>

            <div
                class="{{ $this->isModal ? 'col-span-1 flex-col sm:flex-row' : 'md:col-span-2 lg:col-span-3' }} flex items-center justify-end gap-2 mt-6 relative z-0">
                <span title="{{ isset($type) && $type === 'save' ? __('Create') : __('Update') }}">
                    <flux:button type="submit" variant="primary"
                        icon="{{ isset($type) && $type === 'save' ? 'plus' : 'check' }}" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">{{ $heading ?? 'Submit' }}</span>
                        <span wire:loading
                            wire:target="save">{{ isset($type) && $type === 'save' ? __('Saving...') : __('Updating...') }}</span>
                    </flux:button>
                </span>
            </div>

            <div
                class="{{ $this->isModal ? 'col-span-1' : 'md:col-span-2 lg:col-span-3' }} flex justify-end w-full text-end">
                <div class="flex flex-col items-end gap-2">
                    @include('layouts._show_all_input_error')
                </div>
            </div>
        </form>
    </div>
</div>
