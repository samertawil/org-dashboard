<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ __('Configure the details of this educational activity name.') }}</flux:subheading>
        </div>
        <span title="{{ __('Return to activities list') }}" class="w-full sm:w-auto">
            <flux:button href="{{ route('educational-activity-names.index') }}" wire:navigate variant="ghost" icon="list-bullet" class="w-full">
                {{ __('List') }}
            </flux:button>
        </span>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Name --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Activity Name') }}</flux:label>
                <flux:input wire:model="activity_name" type="text" placeholder="{{ __('e.g. Football Class') }}" />
                <flux:error name="activity_name" />
            </flux:field>

            {{-- Domain --}}
            <flux:field>
                <flux:label>{{ __('Activity Domain') }}</flux:label>
                <flux:select wire:model="activity_domain" placeholder="{{ __('Choose Domain...') }}">
                    <option value="">{{ __('None') }}</option>
                    @foreach ($domains as $domain)
                        <option value="{{ $domain->id }}">{{ $domain->status_name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="activity_domain" />
            </flux:field>

            {{-- Available in Active Groups --}}
            <flux:field class="flex items-center gap-3 md:col-span-2 mt-2">
                <flux:checkbox wire:model="available_in_active_groups" label="{{ __('Available in Active Student Groups') }}" />
                <flux:error name="available_in_active_groups" />
            </flux:field>

            {{-- Teachers Checkboxes Grid --}}
            <flux:field class="md:col-span-2">
                <flux:label>{{ __('Assigned Teachers who can teach this activity') }}</flux:label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-2 p-4 bg-zinc-50 dark:bg-zinc-900/40 rounded-xl border border-zinc-200 dark:border-zinc-700 max-h-60 overflow-y-auto">
                    @foreach($employees as $employee)
                        <flux:checkbox wire:model="teachers" value="{{ (string) $employee->id }}" label="{{ $employee->full_name }}" />
                    @endforeach
                </div>
                <flux:error name="teachers" />
            </flux:field>

            {{-- Description --}}
            <flux:field class="md:col-span-2">
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="description" placeholder="{{ __('Provide a brief description of the activity...') }}" rows="3" />
                <flux:error name="description" />
            </flux:field>

            {{-- Activation --}}
            <flux:field>
                <flux:label>{{ __('Status') }}</flux:label>
                <flux:select wire:model="activation">
                    <option value="1">{{ __('Active') }}</option>
                    <option value="0">{{ __('Inactive') }}</option>
                </flux:select>
                <flux:error name="activation" />
            </flux:field>

            {{-- Buttons --}}
            <div class="md:col-span-2 flex flex-col sm:flex-row items-center justify-end gap-2 mt-4">
                <span title="{{ __('Discard changes and return') }}" class="w-full sm:w-auto">
                    <flux:button href="{{ route('educational-activity-names.index') }}" wire:navigate variant="ghost" class="w-full">
                        {{ __('Cancel') }}
                    </flux:button>
                </span>
                <span title="{{ $type === 'save' ? __('Create new activity name') : __('Update activity details') }}" class="w-full sm:w-auto">
                    <flux:button type="submit" variant="primary" icon="{{ $type === 'save' ? 'plus' : 'check' }}" class="w-full">
                        {{ $heading }}
                    </flux:button>
                </span>
            </div>
        </form>
        @include('layouts._show_all_input_error')
    </div>
</div>
