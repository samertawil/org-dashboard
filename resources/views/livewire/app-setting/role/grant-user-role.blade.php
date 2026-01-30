

<div class="flex flex-col gap-6">


    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading ?? 'User Roles List' }}</flux:heading>
            <flux:subheading>{{ $subheading ?? 'Enter the details for your new user roles below.' }}</flux:subheading>
        </div>

        <flux:button href="{{route('user.index')}}" wire:navigate variant="primary" icon="list-bullet">
            {{ __('Users List') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Create Form Section --}}
    <flux:card>
        <form wire:submit="store" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- User Email --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">User Email</flux:label>
                <flux:input type="text"   disabled  placeholder="{{$this->email}}" class="md:col-span-2" />
                <flux:error name="email" />
            </flux:field>

         
            <div class="md:col-span-2 flex flex-col gap-4 mt-4">
                <flux:card>
                    <flux:heading level="3" size="lg">Roles</flux:heading>
                    <div class="flex flex-col gap-2 mt-2">
                        @foreach ($roles_group as $role_group)
                            <flux:field>
                                <flux:checkbox
                                    value="{{ $role_group->id }}"
                                    wire:model.live='rolesId'
                                    id="role-{{ $role_group->id }}"
                                    label="{{ $role_group->name }}"
                                >
                                </flux:checkbox>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ">
                                    {{ implode(', ', $role_group->abilities_description) }}
                                </p>
                                <br>
                            </flux:field>
                        @endforeach
                    </div>
                </flux:card>
            </div>

            {{-- Submit Button --}}
            <div class="md:col-span-2 flex items-center justify-end gap-2">
                <flux:button type="submit" variant="primary" icon="plus">
                    Apply Role
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
