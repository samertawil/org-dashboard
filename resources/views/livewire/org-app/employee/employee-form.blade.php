<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{$heading}}</flux:heading>
            <flux:subheading>{{$subheading ?? __('Enter the details for the employee below.')}}</flux:subheading>
        </div>
        
        <flux:button 
            href="{{ route('employee.index') }}" 
            wire:navigate 
            variant="ghost"
            icon="list-bullet"
        >
            {{ __('Employee List') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Form Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <form wire:submit="{{$type}}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            {{-- Personal Information Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-2">
                <flux:heading size="lg">{{ __('Personal Information') }}</flux:heading>
            </div>

            {{-- Employee Number --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Employee Number') }}</flux:label>
                <flux:input type="text" wire:model="employee_number" :placeholder="__('E.g. EMP-001')" />
                <flux:error name="employee_number" />
            </flux:field>

            {{-- Full Name --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Full Name') }}</flux:label>
                <flux:input type="text" wire:model="full_name" :placeholder="__('Enter full name')" />
                <flux:error name="full_name" />
            </flux:field>

            {{-- Gender --}}
         
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">Gender</flux:label>
                <flux:select
                wire:model="gender"
                
            >
            <option value="" default>{{ __('Select gender') }}</option>
                @foreach($genders as $g)
                    <option value="{{ $g['value'] }}">{{ $g['label'] }}</option>
                @endforeach
            </flux:select>
                <flux:error name="gender" />
            </flux:field>

            {{-- Date of Birth --}}
            <flux:field>
                <flux:label>{{ __('Date of Birth') }}</flux:label>
                <flux:input type="date" wire:model="date_of_birth" />
                <flux:error name="date_of_birth" />
            </flux:field>

            {{-- Marital Status --}}
            <flux:select
                wire:model="marital_status"
                :label="__('Marital Status')"
            >
                <option value="" default>{{ __('Select Marital Status') }}</option>
                @foreach($this->allStatuses->where('p_id_sub', config('appConstant.maritalStatuses')) as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </flux:select>

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

            {{-- Regions --}}
            <flux:select
                wire:model="regions"
                :label="__('Region/City')"
              
            >
                <option value="" default>{{ __('Select Region/City') }}</option>
                @foreach($this->allStatuses->where('p_id_sub', config('appConstant.regions')) as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </flux:select>

            {{-- Employment Information Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg">{{ __('Employment Details') }}</flux:heading>
            </div>

            {{-- Department --}}
            <flux:select
                wire:model="department_id"
                :label="__('Department')"      
            >
                <option value="" default>{{ __('Select Department') }}</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </flux:select>

            {{-- Position --}}
            <flux:select
                wire:model="position"
                :label="__('Position')"
                :placeholder="__('Select position')"
            >
                <option value="">{{ __('Select Position') }}</option>
                @foreach($this->allStatuses->where('p_id_sub', config('appConstant.positions_in_organization') ) as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </flux:select>

            {{-- Type of Hire --}}
            <flux:select
                wire:model="type_of_employee_hire"
                :label="__('Hire Type')"
            
            >
                <option value="">{{ __('Select Type') }}</option>
                @foreach($this->allStatuses->where('p_id_sub', config('appConstant.hire_types'))  as $status)
                    <option value="{{ $status->id }}">{{ $status->status_name }}</option>
                @endforeach
            </flux:select>

            {{-- Date of Joining --}}
            <flux:field>
                <flux:label>{{ __('Date of Joining') }}</flux:label>
                <flux:input type="date" wire:model="date_of_joining" />
                <flux:error name="date_of_joining" />
            </flux:field>

            {{-- System User Integration Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg">{{ __('System Settings') }}</flux:heading>
            </div>

            {{-- User Account --}}
            <flux:select
                wire:model="user_id"
                :label="__('Linked User Account')"
                :placeholder="__('Select user')"
            >
                <option value="">{{ __('No user linked') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </flux:select>

            {{-- Activation --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">Status</flux:label>
            <flux:select
                wire:model="activation"
            >
                @foreach($activations as $a)
                    <option value="{{ $a['value'] }}">{{ $a['label'] }}</option>
                @endforeach
            </flux:select>
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
