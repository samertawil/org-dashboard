<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{$heading}}</flux:heading>
            <flux:subheading>{{ __('Enter the details for the teaching group below.') }}</flux:subheading>
        </div>
        
        <flux:button 
            href="{{ route('teaching.group.index') }}" 
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
                <flux:input type="text" wire:model="name" :placeholder="__('Enter group name')" />
                <flux:error name="name" />
            </flux:field>

            {{-- Activity --}}
            <flux:select wire:model="activity_id" :label="__('Activity')" placeholder="{{ __('Select Activity') }}">
                 <option value="">{{ __('Select Activity') }}</option>
                @foreach($activities as $activity)
                    <option value="{{ $activity->id }}">{{ $activity->name ?? 'Activity '.$activity->id }}</option>
                @endforeach
            </flux:select>
            <flux:error name="activity_id" />

             {{-- Student Group --}}
            <flux:select wire:model="student_groups_id" :label="__('Student Group')" placeholder="{{ __('Select Student Group') }}">
                 <option value="">{{ __('Select Student Group') }}</option>
                @foreach($student_groups as $sg)
                    <option value="{{ $sg->id }}">{{ $sg->name }}</option>
                @endforeach
            </flux:select>
             <flux:error name="student_groups_id" />

            {{-- Partner --}}
            <flux:select wire:model="partner_id" :label="__('Partner Institution')" placeholder="{{ __('Select Partner') }}">
                 <option value="">{{ __('Select Partner') }}</option>
                @foreach($partners as $partner)
                    <option value="{{ $partner->id }}">{{ $partner->name ?? 'Partner '.$partner->id }}</option>
                @endforeach
            </flux:select>
             <flux:error name="partner_id" />
             
             {{-- Status --}}
              <flux:select wire:model="status" :label="__('Group Status')" placeholder="{{ __('Select Status') }}">
                 <option value="">{{ __('Select Status') }}</option>
                @foreach($statuses as $st)
                    <option value="{{ $st->id }}">{{ $st->name ?? $st->status_name }}</option>
                @endforeach
            </flux:select>
             <flux:error name="status" />

             {{-- Cost USD --}}
            <flux:field>
                <flux:label badge="Required">{{ __('Cost (USD)') }}</flux:label>
                <flux:input type="number" step="0.01" wire:model="cost_usd" />
                <flux:error name="cost_usd" />
            </flux:field>

             {{-- Cost NIS --}}
            <flux:field>
                <flux:label badge="Required">{{ __('Cost (NIS)') }}</flux:label>
                <flux:input type="number" step="0.01" wire:model="cost_nis" />
                <flux:error name="cost_nis" />
            </flux:field>
            
          


            {{-- Moderator Information Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg">{{ __('Moderator Details') }}</flux:heading>
            </div>

            <flux:field>
                <flux:label>{{ __('Moderator Name') }}</flux:label>
                <flux:input type="text" wire:model="Moderator" :placeholder="__('Enter moderator name')" />
                <flux:error name="Moderator" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Moderator Phone') }}</flux:label>
                <flux:input type="text" wire:model="Moderator_phone" :placeholder="__('000-000-000')" />
                <flux:error name="Moderator_phone" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Moderator Email') }}</flux:label>
                <flux:input type="email" wire:model="Moderator_email" :placeholder="__('example@domain.com')" />
                <flux:error name="Moderator_email" />
            </flux:field>

             {{-- Notes --}}
             <flux:field class="md:col-span-2 lg:col-span-3">
                <flux:label>{{ __('Notes') }}</flux:label>
                <flux:textarea wire:model="notes" :placeholder="__('Enter notes')" />
                <flux:error name="notes" />
            </flux:field>


            {{-- System Settings Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg">{{ __('System Settings') }}</flux:heading>
            </div>

            {{-- Activation --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Activation Status') }}</flux:label>
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
