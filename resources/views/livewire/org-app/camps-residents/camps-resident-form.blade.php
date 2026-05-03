<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ $subheading ?? __('Enter the details for the camp resident below.') }}</flux:subheading>
        </div>
        @can('displacement.camps.index')
        <span title="{{ __('Return to camp residents list') }}" class="w-full sm:w-auto">
            <flux:button href="{{ route('camps.residents.index') }}" wire:navigate variant="ghost" icon="list-bullet" class="w-full">
                {{ __('Residents List') }}
            </flux:button>
        </span>
        @endcan
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Form Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <form wire:submit="{{$type}}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            {{-- Basic Information Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-2">
                <flux:heading size="lg">{{ __('Basic Information') }}</flux:heading>
            </div>

            {{-- Full Name --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Full Name') }}</flux:label>
                <flux:input type="text" wire:model="full_name" :placeholder="__('Enter full name')" />
                <flux:error name="full_name" />
            </flux:field>

            {{-- Identity Number --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Identity Number') }}</flux:label>
                <flux:input type="number" wire:model="identity_number" />
                <flux:error name="identity_number" />
            </flux:field>

            {{-- Birth Date --}}
            <flux:field>
                <flux:label>{{ __('Birth Date') }}</flux:label>
                <flux:input type="date" wire:model="birth_date" />
                <flux:error name="birth_date" />
            </flux:field>

            {{-- Gender --}}
            <flux:select wire:model="gender" :label="__('Gender')">
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Gender') }}</option>
                @foreach($genderData as $genderOption)
                <option value="{{ $genderOption['value'] }}">{{ $genderOption['label'] }} {{ $genderOption['icon'] }}</option>
            @endforeach
            </flux:select>

            {{-- Displacement Camp --}}
            <flux:select wire:model="displacement_camp_id" :label="__('Displacement Camp')">
                 <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Camp') }}</option>
                @foreach($displacementCamps as $camp)
                    <option value="{{ $camp->id }}">{{ $camp->name }}</option>
                @endforeach
            </flux:select>
            
            {{-- Resident Type (Status) --}}
             <flux:select wire:model="resident_type" :label="__('Resident Type')">
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Type') }}</option>
                @foreach($residentTypes as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </flux:select>


            {{-- Additional Detail Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg">{{ __('Contact Details') }}</flux:heading>
            </div>

            {{-- Phone --}}
            <flux:field>
                <flux:label>{{ __('Phone Number') }}</flux:label>
                <flux:input type="text" wire:model="phone" :placeholder="__('000-000-000')" />
                <flux:error name="phone" />
            </flux:field>


            {{-- System Settings Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg">{{ __('System Settings') }}</flux:heading>
            </div>

            {{-- Activation --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Status') }}</flux:label>
                <flux:select wire:model="activation">
                    @foreach($activations as $a)
                        <option value="{{ $a['value'] }}">{{ $a['label'] }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="activation" />
            </flux:field>

            {{-- Submit Button --}}
            <div class="md:col-span-2 lg:col-span-3 flex flex-col sm:flex-row items-center justify-end gap-2 mt-6">
                <span title="{{ $type === 'save' ? __('Create the resident record') : __('Update the resident record') }}" class="w-full sm:w-auto">
                    <flux:button type="submit" variant="primary" icon="{{ $type === 'save' ? 'plus' : 'check' }}" class="w-full">
                        {{ $heading }}
                    </flux:button>
                </span>
            </div>
            <div class="md:col-span-2 lg:col-span-3 flex justify-end w-full text-end">
                <div class="flex flex-col items-end gap-2">
                    @include('layouts._show_all_input_error')
                    <x-auth-session-status class="{{ session('type') == 'error' ? 'text-red-500' : '' }}"
                        :status="session('message')" />
                </div>
            </div>
        </form>
    </div>
</div>
