<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ __('Add a new subject regarding learning.') }}</flux:subheading>
        </div>
        <span title="{{ __('Return to subjects list') }}" class="w-full sm:w-auto">
            <flux:button href="{{ route('subject.index') }}" wire:navigate variant="ghost" icon="list-bullet" class="w-full">
                {{ __('List') }}
            </flux:button>
        </span>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Name') }}</flux:label>
                <flux:input wire:model="name" type="text" placeholder="{{ __('Subject Name') }}" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Type') }}</flux:label>
                <flux:select wire:model="type_id" placeholder="{{ __('Select Type') }}">
                     <option value="">{{ __('None') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="type_id" />
            </flux:field>

            <flux:field>
                <flux:label >{{ __('From Age') }}</flux:label>
                <flux:input wire:model="from_age" type="number" placeholder="{{ __('From Age') }}" />
                <flux:error name="from_age" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('To Age') }}</flux:label>
                <flux:input wire:model="to_age" type="text" placeholder="{{ __('To Age') }}" />
                <flux:error name="to_age" />
            </flux:field>

            <flux:field class="md:col-span-2">
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="description" placeholder="{{ __('Description...') }}" rows="3" />
                <flux:error name="description" />
            </flux:field>
            
            <flux:field>
                <flux:label>{{ __('Status') }}</flux:label>
                <flux:select wire:model="activation">
                    @foreach($activations as $a)
                        <option value="{{ $a['value'] }}">{{ $a['label'] }}</option>
                    @endforeach
                </flux:select>
                 <flux:error name="activation" />
            </flux:field>

            <div class="md:col-span-2 flex flex-col sm:flex-row items-center justify-end gap-2 mt-4">
                <span title="{{ __('Discard changes and return') }}" class="w-full sm:w-auto">
                    <flux:button href="{{ route('subject.index') }}" wire:navigate variant="ghost" class="w-full">
                        {{ __('Cancel') }}
                    </flux:button>
                </span>
                <span title="{{ $type === 'save' ? __('Create new subject') : __('Update subject details') }}" class="w-full sm:w-auto">
                    <flux:button type="submit" variant="primary" icon="{{ $type === 'save' ? 'plus' : 'check' }}" class="w-full">
                        {{ $heading }}
                    </flux:button> 
                </span>
            </div>  
        </form>
        @include('layouts._show_all_input_error') 
    </div>
</div>

