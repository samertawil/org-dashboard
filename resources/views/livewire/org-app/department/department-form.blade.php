<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading ?? 'Create Department' }}</flux:heading>
            <flux:subheading>{{ $subheading ?? 'Enter the details for your new Department below.' }}</flux:subheading>
        </div>

        <flux:button href="{{ route('department.index') }}" wire:navigate variant="ghost" icon="list-bullet">
            {{ __('Department List') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Create Form Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <form wire:submit="{{ $type }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Name --}}
            <flux:field class="md:col-span-2">
                <flux:label badge="Required" badgeColor="text-red-600">Department Name</flux:label>
                <flux:input type="text" wire:model.live.lazy="name" :placeholder="__('Enter department name')"
                    autocomplete="off" />
                <flux:error name="name" />
            </flux:field>

            {{-- Location --}}
            {{-- <flux:field class="md:col-span-2">
                <flux:label>Location</flux:label>
                <flux:input type="text" wire:model="location" :placeholder="__('Enter location (optional)')" />
                <flux:error name="location" />
            </flux:field> --}}

            <flux:field class="md:col-span-2">
                <flux:label >Location</flux:label>
                <flux:input type="text" wire:model.live.lazy="location"
                    :placeholder="__('Enter location or select existing')" list="locations-list" autocomplete="off" />
                <datalist id="locations-list">
                    @foreach ($this->locations as $location)
                        <option value="{{ $location['location'] }}">
                    @endforeach
                </datalist>
                <flux:error name="location" />
            </flux:field>

            {{-- Description --}}
            <flux:textarea wire:model="description" :label="__('Description')"
                :placeholder="__('Enter description (optional)')" rows="3" class="md:col-span-2" />
            <flux:error name="description" />

            {{-- Submit Button --}}
            <div class="md:col-span-2 flex items-center justify-end gap-2">
                <flux:button type="submit" variant="primary" icon="{{ $type === 'save' ? 'plus' : 'check' }}">
                    {{ $heading }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
