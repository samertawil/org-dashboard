<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{$heading}}</flux:heading>
            <flux:subheading>{{$subheading ?? __('Enter the details for the partner institution below.')}}</flux:subheading>
        </div>
        
        <flux:button 
            href="{{ route('partner.index') }}" 
            wire:navigate 
            variant="ghost"
            icon="list-bullet"
        >
            {{ __('Partner List') }}
        </flux:button>
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

            {{-- Name --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Name') }}</flux:label>
                <flux:input type="text" wire:model="name" :placeholder="__('Enter institution name')" />
                <flux:error name="name" />
            </flux:field>

            {{-- Manager Name --}}
            <flux:field>
                <flux:label>{{ __('Manager Name') }}</flux:label>
                <flux:input type="text" wire:model="manager_name" :placeholder="__('Enter manager name')" />
                <flux:error name="manager_name" />
            </flux:field>

            {{-- Type --}}
            <flux:select
                wire:model="type_id"
                :label="__('Type')"
              
            >
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Type') }}</option>
              
                @foreach($partnerTypes as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </flux:select>

            {{-- Contact Information Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg">{{ __('Contact Details') }}</flux:heading>
            </div>

            {{-- Phone --}}
            <flux:field>
                <flux:label>{{ __('Phone') }}</flux:label>
                <flux:input type="text" wire:model="phone" :placeholder="__('000-000-000')" />
                <flux:error name="phone" />
            </flux:field>

            {{-- Email --}}
            <flux:field>
                <flux:label>{{ __('Email') }}</flux:label>
                <flux:input type="email" wire:model="email" :placeholder="__('example@domain.com')" />
                <flux:error name="email" />
            </flux:field>

            {{-- Website --}}
            <flux:field>
                <flux:label>{{ __('Website') }}</flux:label>
                <flux:input type="text" wire:model="website" :placeholder="__('https://example.com')" />
                <flux:error name="website" />
            </flux:field>

             {{-- Location --}}
             

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
             <flux:field class="md:col-span-2 lg:col-span-3">
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:textarea wire:model="description" :placeholder="__('Enter description')" />
                <flux:error name="description" />
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
            <div class="md:col-span-2 lg:col-span-3 flex items-center justify-end gap-2 mt-6">
                <flux:button type="submit" variant="primary" icon="{{ $type === 'save' ? 'plus' : 'check' }}">
                    {{ $heading }}
                </flux:button>
            </div>
            @include('layouts._show_all_input_error')
        </form>
    </div>
</div>
