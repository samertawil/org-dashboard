<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
        </div>
        <flux:button href="{{ route('currency.index') }}" wire:navigate variant="ghost">
            {{ __('Back to List') }}
        </flux:button>
    </div>

    <form wire:submit="{{ $type }}" class="grid grid-cols-1 gap-6">
        <flux:card class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-2">
                <flux:label>{{ __('Exchange Date') }}</flux:label>
                <flux:input wire:model="exchange_date" type="date" />
                <flux:error name="exchange_date" />
            </div>
            
            <div class="flex flex-col gap-2">
                <flux:label>{{ __('Currency Value') }}</flux:label>
                <flux:input wire:model="currency_value" type="number" step="0.01" />
                <flux:error name="currency_value" />
            </div>
        </flux:card>

        <div class="flex justify-end">
            <flux:button type="submit" variant="primary">
                {{ __('Save') }}
            </flux:button>
        </div>
    </form>
</div>
