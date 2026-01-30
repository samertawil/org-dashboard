<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading ?? 'Create New Ability' }}</flux:heading>
            <flux:subheading>{{ $subheading ?? 'Enter the details for your new Ability below.' }}</flux:subheading>
        </div>

        <flux:button href="{{route('ability.index')}}" wire:navigate variant="primary" icon="list-bullet">
            {{ __('Abilities List') }}
        </flux:button>
    </div>


    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Create Form Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        

        <form wire:submit="{{$type}}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- ability Name --}}

            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">Ability Name</flux:label>
                <flux:input type="text" wire:model="ability_name" :disabled="$type == 'update'" :placeholder="__('Enter Ability name')"
                    class="md:col-span-2" />
                <flux:error name="ability_name" />
            </flux:field>

            {{-- ability_description --}}

            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">Ability Description</flux:label>
                <flux:input wire:model="ability_description" :placeholder="__('Enter description')"
                    class="md:col-span-2" />
                <flux:error name="ability_description" />
            </flux:field>


            {{-- module_id --}}
            <flux:field>
                <div>
                    <div class="flex-1 mb-2">    
                            <flux:label badge="Required" badgeColor="text-red-600" >Module Name</flux:label>
                            <flux:select wire:model="module_id" :placeholder="__('Select Module Name')">
                                <option value="">{{ __('Choose Module') }}</option>
                                @foreach ($this->ModuleNames as $module)
                                    <option value="{{ $module->id }}">{{ $module->name }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="module_id" />
                       

                    </div>
                    <flux:modal.trigger name="create-module-modal">
                        <flux:button icon="plus" variant="primary" size="sm" tooltip="Add New Module">
                            Add Module
                        </flux:button>
                    </flux:modal.trigger>
                </div>
            </flux:field>

            {{-- Description --}}

            <flux:field>
                <flux:label class="mt-5">Description</flux:label>
                <flux:textarea wire:model="description" :placeholder="__('Enter description')" rows="3"
                    class="md:col-span-2" />
                <flux:error name="description" />
            </flux:field>

            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">Activation</flux:label>
                <flux:select wire:model="activation">
                    <option value="" class="text-gray-500 dark:text-gray-600">{{ __('Choose') }}</option>
                    @foreach (\App\Enums\GlobalSystemConstant::options()->where('type', 'status') as $const)
                        <flux:select.option value="{{ $const['value'] }}">{{ $const['label'] }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="activation" />
            </flux:field>


            {{-- Submit Button --}}
            <div class="md:col-span-2 flex items-center justify-end gap-2">
                <flux:button type="submit" variant="primary" icon="plus">
                    {{ $heading }}
                </flux:button>
            </div>
        </form>
    </div>


    {{-- Create Module Modal --}}
    <flux:modal name="create-module-modal" class="md:w-96">
        <div>
            <flux:heading size="lg">Create New Module</flux:heading>
            <flux:subheading>Add a new module to the system</flux:subheading>
        </div>

        <div class="mt-4">
            @livewire('app-setting.role-module-name.create')
        </div>
    </flux:modal>

</div>
