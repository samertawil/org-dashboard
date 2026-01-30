<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading ?? 'Create New Role' }}</flux:heading>
            <flux:subheading>{{ $subheading ?? 'Enter the details for your new Role below.' }}</flux:subheading>
        </div>

        <flux:button href="{{ route('role.index') }}" wire:navigate variant="primary" icon="list-bullet">
            {{ __('Roles List') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    @include('layouts._alert-session')

    {{-- Create Form Section --}}
    <flux:card>
        <form wire:submit="store" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Role Name --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">Role Name</flux:label>
                <flux:input type="text" wire:model="name" :placeholder="__('New Role Name')" class="md:col-span-2" />
                <flux:error name="name" />
            </flux:field>

            {{-- Abilities Section --}}
            <div class="md:col-span-2 flex flex-col gap-4 mt-4">
                <div class="flex items-center justify-between">
                    <flux:heading level="3" size="md">Abilities</flux:heading>
                    <flux:button type="button" wire:click="toggleAll" size="sm" variant="ghost">Select All
                    </flux:button>
                </div>
                @foreach ($abilities_module as $module)
                    <flux:card>
                        <div
                            class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                            <flux:heading level="4" size="sm">{{ $module->module_name->name ?? '' }}
                            </flux:heading>
                            <flux:button type="button" wire:click="toggleModule({{ $module->module_id }})"
                                size="sm" variant="ghost">Select Module</flux:button>
                        </div>
                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($abilities->where('module_id', $module->module_id) as $ability)
                                <flux:checkbox wire:key="ability-{{ $ability->id }}" id="{{ $ability->id }}"
                                    wire:model.live='abilitiesId' value="{{ $ability->ability_name }}"
                                    label="{{ $ability->ability_description }}"
                                    :disabled="$ability->activation == '0'" />
                            @endforeach
                        </div>
                    </flux:card>
                @endforeach
            </div>

            {{-- Submit Button --}}
            <div class="md:col-span-2 flex items-center justify-end gap-2">
                <flux:button type="submit" variant="primary" icon="plus">
                    Create Role
                </flux:button>
            </div>

            @include('layouts._show_all_input_error')

        </form>
    </flux:card>
</div>
