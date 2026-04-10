<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">  {{ $heading }}</flux:heading>
            <flux:subheading>{{ __('Add a new subject regarding learning.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('subject.index') }}" wire:navigate variant="ghost" icon="list-bullet">{{ __('List') }}</flux:button>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Name') }}</flux:label>
                <flux:input wire:model="name" type="text" placeholder="Subject Name"   style="height:auto "  />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Type') }}</flux:label>
                <flux:select wire:model="type_id" placeholder="Select Type">
                     <option value="">{{ __('None') }}</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="type_id" />
            </flux:field>

            <flux:field>
                <flux:label >{{ __('From Age') }}</flux:label>
                <flux:input wire:model="from_age" type="number" placeholder="From Age" />
                <flux:error name="from_age" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('To Age') }}</flux:label>
                <flux:input wire:model="to_age" type="text" placeholder="To Age" />
                <flux:error name="to_age" />
            </flux:field>

            <flux:field class="md:col-span-2">
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="description" placeholder="Description..." rows="3" />
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

            <div class="md:col-span-2 flex items-center justify-end gap-2 mt-4">
                <flux:button href="{{ route('subject.index') }}" wire:navigate variant="ghost">{{ __('Cancel') }}</flux:button>
                <flux:button type="submit" variant="primary" icon="{{ $type === 'save' ? 'plus' : 'check' }}">
                    {{ $heading }}
                </flux:button> 
            </div>  
        </form>
        @include('layouts._show_all_input_error') 
    </div>
</div>

