<div class="flex flex-col gap-6">
    {{-- Header Section --}}

    <div class="flex items-center justify-between">
        <flux:heading level="1" size="xl">{{ $heading ?? 'Modules Of Role List ' }}&nbsp;</flux:heading>
        <flux:button href="#" wire:navigate icon="list-bullet">
            {{ __('Modules List') }}
        </flux:button>
    </div>


    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Create Form Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">{{ __('Create New Status') }}</h2>

        <form wire:submit="store" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- modul Name --}}

            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">Module Name</flux:label>
                <flux:input type="text" wire:model="name" :placeholder="__('Enter Modules name')"
                    class="md:col-span-2" />
                <flux:error name="name" />
            </flux:field>

            
            {{-- active --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">Module Description</flux:label>
                <flux:select wire:model="active">
                    <option value="" class="text-gray-500 dark:text-gray-600">{{ __('Choose Module') }}</option>
                    @foreach (\App\Enums\GlobalSystemConstant::options()->where('type', 'status') as $const)
                        <flux:select.option value="{{ $const['value'] }}">{{ $const['label'] }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="active" />
            </flux:field>

            {{-- ability_description --}}

            <flux:field>
                <flux:label>Description</flux:label>
                <flux:textarea wire:model="description" :placeholder="__('Enter description')" rows="3"
                    class="md:col-span-2" />
                <flux:error name="description" />
            </flux:field>

            {{-- Submit Button --}}
            <div class="md:col-span-2 flex items-center justify-end gap-2">
                <flux:button type="submit" variant="primary" icon="plus">
                    Create Module
                </flux:button>
            </div>
        </form>
    </div>


</div>
