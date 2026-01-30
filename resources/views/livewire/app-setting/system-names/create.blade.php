<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{$heading ?? 'Systems List'}}</flux:heading>
            <flux:subheading>{{$subheading ?? 'Enter the details for your new System Names below.'}}</flux:subheading>
        </div>
        
        <flux:button 
            href="{{ route('system.names.index') }}" 
            wire:navigate 
            variant="primary"
            icon="list-bullet"
        >
            {{ __('Systems List') }}
        </flux:button>
    </div>

    <x-auth-session-status class="text-center" :status="session('message')" />

    <form wire:submit="store" class="flex flex-col gap-6">
        <!-- System Name -->
        <flux:input
            wire:model="system_name"
            :label="__('System Name')"
            type="text"
            required
            autofocus
            autocomplete="off"
            :placeholder="__('Enter system name')"
        />

        <!-- Description -->
        <flux:textarea
            wire:model="description"
            :label="__('Description')"
            rows="4"
            :placeholder="__('Enter description (optional)')"
        />

        <div class="flex items-center justify-end gap-2">
            <flux:button type="submit" variant="primary">
                {{ __('Create System Name') }}
            </flux:button>
        </div>
    </form>
</div>
