<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{$heading}}</flux:heading>
            <flux:subheading>{{$subheading ?? __('Enter the details for the student group below.')}}</flux:subheading>
        </div>
        
        <flux:button 
            href="{{ route('student.group.index') }}" 
            wire:navigate 
            variant="ghost"
            icon="list-bullet"
        >
            {{ __('Group List') }}
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
                <flux:input type="text" wire:model="name" :placeholder="__('Enter group name')" disabled />
                <flux:error name="name" />
            </flux:field>

            {{-- Max Students --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Max Students') }}</flux:label>
                <flux:input type="number" wire:model="max_students" min="0" />
                <flux:error name="max_students" />
            </flux:field>

            {{-- Min Students --}}
            <flux:field>
                <flux:label>{{ __('Min Students') }}</flux:label>
                <flux:input type="number" wire:model="min_students" min="0" />
                <flux:error name="min_students" />
            </flux:field>

            {{-- Region --}}
            <flux:select
                wire:model.live="region_id"
                :label="__('Region')"
            >
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Region') }}</option>
                @foreach($regions as $region)
                    {{-- Assuming region has id and name --}}
                    <option value="{{ $region->id }}">{{ $region->region_name }}</option>
                @endforeach
            </flux:select>

             {{-- City --}}
             <flux:select
                wire:model.live="city_id"
                :label="__('City')"
            >
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select City') }}</option>
                @foreach($cities as $city)
                    {{-- Assuming city has id and name --}}
                    <option value="{{ $city->id }}">{{ $city->city_name }}</option>
                @endforeach
            </flux:select>
            
            {{-- Status/Category (status_id) --}}
             {{-- <flux:select
                wire:model="status_id"
                :label="__('Group Status/Category')"
            >
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Status') }}</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </flux:select> --}}


            {{-- Moderator Information Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg">{{ __('Moderator Details') }}</flux:heading>
            </div>

            {{-- Moderator Name --}}
            <flux:field>
                <flux:label>{{ __('Moderator Name') }}</flux:label>
                <flux:input type="text" wire:model="Moderator" :placeholder="__('Enter moderator name')" />
                <flux:error name="Moderator" />
            </flux:field>

            {{-- Moderator Phone --}}
            <flux:field>
                <flux:label>{{ __('Moderator Phone') }}</flux:label>
                <flux:input type="text" wire:model="Moderator_phone" :placeholder="__('000-000-000')" />
                <flux:error name="Moderator_phone" />
            </flux:field>

            {{-- Moderator Email --}}
            <flux:field>
                <flux:label>{{ __('Moderator Email') }}</flux:label>
                <flux:input type="email" wire:model="Moderator_email" :placeholder="__('example@domain.com')" />
                <flux:error name="Moderator_email" />
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
