<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{$heading ?? 'Status List'}}</flux:heading>
            <flux:subheading>{{$subheading ?? 'Enter the details for your new Status below.'}}</flux:subheading>
        </div>
        
        <flux:button 
            href="{{ route('status.index') }}" 
            wire:navigate 
            variant="primary"
            icon="list-bullet"
        >
            {{ __('Status List') }}
        </flux:button>
    </div>


    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Create Form Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
      
        
        <form wire:submit="{{$type}}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      
            {{-- Status Name --}}
         
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">Status Name</flux:label>
                <flux:input type="text"  wire:model="status_name" :placeholder="__('Enter status name')"  class="md:col-span-2" />
                <flux:error name="status_name" />
            </flux:field>
            {{-- Parent Status --}}
            <flux:select
                wire:model="p_id_sub"
                :label="__('Parent Status')"
               
            >
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('None (Root Status)') }}</option>
                @foreach($parentStatuses as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->status_name }}</option>
                @endforeach
            </flux:select>

            <flux:select
            wire:model="c_id_sub"
            :label="__('Child Status')"
           
        >
            <option value="" class="text-gray-500 placeholder-gray-500">{{ __('None (Root Status)') }}</option>
            @foreach($childStatuses as $child)
                <option value="{{ $child->id }}">{{ $child->status_name }}</option>
            @endforeach
        </flux:select>

            {{-- System Name --}}
            <flux:select
                wire:model="used_in_system_id"
                :label="__('System Name')"
                :placeholder="__('Select system (optional)')"
            >
                <option value="">{{ __('Select System') }}</option>
                @foreach($systemNames as $system)
                    <option value="{{ $system->id }}">{{ $system->system_name }}</option>
                @endforeach
            </flux:select>

            {{-- Description --}}
            <flux:textarea
                wire:model="description"
                :label="__('Description')"
                :placeholder="__('Enter description (optional)')"
                rows="3"
                class="md:col-span-2"
            />

            {{-- Submit Button --}}
            <div class="md:col-span-2 flex items-center justify-end gap-2">
                <flux:button type="submit" variant="primary" icon="plus">
                    {{ $heading }}
                </flux:button>
            </div>
        </form>
    </div>

     
</div>
