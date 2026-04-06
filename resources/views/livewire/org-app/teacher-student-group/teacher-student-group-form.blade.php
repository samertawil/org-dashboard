<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{$heading}}</flux:heading>
            <flux:subheading>{{ __('Enter the details for the teacher-student group assignment below.') }}</flux:subheading>
        </div>
        
        <flux:button 
            href="{{ route('teacher-student-groups.index') }}" 
            wire:navigate 
            variant="ghost"
            icon="list-bullet"
        >
            {{ __('Assignments List') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Form Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <form wire:submit="{{$type}}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            {{-- Basic Information Header --}}
            <div class="md:col-span-2 border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-2">
                <flux:heading size="lg">{{ __('Assignment Information') }}</flux:heading>
            </div>

            {{-- Teacher --}}
            <flux:select wire:model="teacher_id" :label="__('Teacher')" placeholder="{{ __('Select Teacher') }}">
                <option value="">{{ __('Select Teacher') }}</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->user_id }}">{{ $employee->user->name ?? 'User '.$employee->user_id }}</option>
                @endforeach
            </flux:select>
            <flux:error name="teacher_id" />

             {{-- Student Group --}}
            <flux:select wire:model="student_group_id" :label="__('Student Group')" placeholder="{{ __('Select Student Group') }}">
                 <option value="">{{ __('Select Student Group') }}</option>
                @foreach($student_groups as $sg)
                    <option value="{{ $sg->id }}">{{ $sg->name }}</option>
                @endforeach
            </flux:select>
             <flux:error name="student_group_id" />

            {{-- Submit Button --}}
            <div class="md:col-span-2 flex items-center justify-end gap-2 mt-6">
                <flux:button type="submit" variant="primary" icon="{{ $type === 'save' ? 'plus' : 'check' }}">
                    {{ $heading }}
                </flux:button>
            </div>
            @include('layouts._show_all_input_error')
        </form>
    </div>
</div>
