<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{$heading}}</flux:heading>
            <flux:subheading>{{$subheading ?? __('Enter the details for the activity beneficiary below.')}}</flux:subheading>
        </div>
        @can('activity.beneficiaries.index')
        <flux:button href="{{ route('activity.beneficiaries.index') }}" wire:navigate variant="ghost" icon="list-bullet">
            {{ __('Beneficiaries List') }}
        </flux:button>
        @endcan
    </div>


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
            
            {{-- Activity --}}
            <flux:select wire:model="activity_id" :label="__('Activity')" badge="Required" badgeColor="text-red-600">
                 <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Activity') }}</option>
                @foreach($activities as $activity)
                    <option value="{{ $activity->id }}">{{ $activity->title ?? $activity->name ?? 'Activity ' . $activity->id }}</option>
                @endforeach
            </flux:select>

            {{-- Displacement Camp --}}
            <flux:select wire:model="displacement_camps_id" :label="__('Displacement Camp')">
                 <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Camp (Optional)') }}</option>
                @foreach($displacementCamps as $camp)
                    <option value="{{ $camp->id }}">{{ $camp->name }}</option>
                @endforeach
            </flux:select>
            
            {{-- Receipt Details Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg">{{ __('Receipt Details') }}</flux:heading>
            </div>

            {{-- Receipt Date --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Receipt Date') }}</flux:label>
                <flux:input type="date" wire:model="receipt_date" />
                <flux:error name="receipt_date" />
            </flux:field>

            {{-- Receive Method (Status) --}}
             <flux:select wire:model="receive_method" :label="__('Receive Method')">
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Method') }}</option>
                @foreach($receiptMethods as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </flux:select>
            
            {{-- Receipt By Name --}}
            <flux:field>
                <flux:label>{{ __('Received By') }}</flux:label>
                <flux:input type="text" wire:model="receive_by_name" :placeholder="__('Name if not beneficiary')" />
                <flux:error name="receive_by_name" />
            </flux:field>

            {{-- Contact Details Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg">{{ __('Contact Details') }}</flux:heading>
            </div>

            {{-- Phone --}}
            <flux:field>
                <flux:label>{{ __('Phone Number') }}</flux:label>
                <flux:input type="text" wire:model="phone" :placeholder="__('000-000-000')" />
                <flux:error name="phone" />
            </flux:field>

            {{-- Submit Button --}}
            <div class="md:col-span-2 lg:col-span-3 flex items-center justify-end gap-2 mt-6">
                <flux:button type="submit" variant="primary" icon="{{ $type === 'save' ? 'plus' : 'check' }}">
                    {{ $heading }}
                </flux:button>
            </div>
            <div class="md:col-span-2 lg:col-span-3 flex justify-end w-full text-end">
                <div class="flex flex-col items-end gap-2">
                    @include('layouts._show_all_input_error')
                    <x-auth-session-status class="text-center" :status="session('message')" />
                </div>
            </div>
        </form>
    </div>
</div>
