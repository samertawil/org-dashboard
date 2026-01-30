<div class="flex flex-col gap-6">
 
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading ?? 'Update Roles' }}</flux:heading>
            <flux:subheading>{{ $subheading ?? 'Enter the details for your new Role below.' }}</flux:subheading>
        </div>

        <flux:button href="{{ route('role.index') }}" wire:navigate variant="primary" icon="list-bullet">
            {{ __('Roles List') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Create Form Section --}}
    <flux:card>
        <form wire:submit="update" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Role Name --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">Role Name</flux:label>
                <flux:input type="text" wire:model="name" :placeholder="__('New Role Name')" class="md:col-span-2" />
                <flux:error name="name" />
            </flux:field>

            <div class="md:col-span-2 flex flex-col gap-4 mt-4">
                <div class="flex items-center justify-between">
                    <flux:heading level="3" size="md">Abilities</flux:heading>
                    <flux:button type="button" wire:click="toggleAll" size="sm" variant="ghost">Select All</flux:button>
                </div>


                @foreach ($abilities_module as $module)
                    <flux:card class="mt-4" wire:key="module-{{ $module->module_id }}">
                        <div class="flex items-center justify-between mb-2">
                            <flux:heading level="4" size="sm">{{ $module->module_name->name ?? '' }}</flux:heading>
                            <flux:button type="button" wire:click="toggleModule({{ $module->module_id }})" size="sm" variant="ghost">Select Module</flux:button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">

                    @foreach ($abilities->where('module_id', $module->module_id) as $ability)
                            <flux:field wire:key="ability-{{ $ability->id }}">
                                <flux:checkbox
                                    id="ability-{{ $ability->id }}"
                                    wire:model.live='abilitiesId'
                                    value="{{ $ability->ability_name }}"
                                    label="{{ $ability->ability_description }}"
                                    :disabled="$ability->activation == '0'"
                                />
                            </flux:field>
                            @endforeach
                        </div>
                    </flux:card>
                @endforeach
            </div>


{{-- Submit Button --}}
<div class="md:col-span-2 flex items-center justify-end gap-2">
    <flux:button type="submit" variant="primary" icon="plus">
        Update Role
    </flux:button>
</div>

</form>
</flux:card>
</div>
