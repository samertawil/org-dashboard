<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ __('Fill in the basic student data below.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('student.basic-data.index') }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Back to List') }}
        </flux:button>
    </div>

    <x-auth-session-status class="text-center" :status="session('message')" />

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <form wire:submit="save" class="flex flex-col gap-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4 border-b border-zinc-100 dark:border-zinc-700">
                <div>
                    <flux:label>{{ __('Student Name') }}</flux:label>
                    <div class="text-lg font-medium text-zinc-900 dark:text-zinc-100">{{ $student->full_name }}</div>
                </div>
                <div>
                    <flux:label>{{ __('Identity Number') }}</flux:label>
                    <div class="text-lg font-medium text-zinc-900 dark:text-zinc-100">{{ $student->identity_number }}</div>
                </div>
            </div>

            @if (count($this->surveyquestions) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($this->surveyquestions as $index => $q)
                        <flux:field class="col-span-full">
                            <flux:label>{{ $index + 1 }} - {{ $q->question_ar_text }}</flux:label>
                            
                            @if ($q->answer_input_type == 2) {{-- Select/Dropdown --}}
                                @if ($q->answer_options && count($q->answer_options) > 0)
                                    <flux:select wire:model="answer.{{ $q->id }}">
                                        <option value="">{{ __('Select answer') }}</option>
                                        @foreach ($q->answer_options as $option)
                                            @if (is_array($option) && isset($option['value']) && isset($option['label']))
                                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                            @elseif(!is_array($option))
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endif
                                        @endforeach
                                    </flux:select>
                                @else
                                    <flux:input type="text" wire:model="answer.{{ $q->id }}" :placeholder="__('Enter answer')" />
                                @endif
                            @else {{-- Text Input --}}
                                <flux:input type="text" wire:model="answer.{{ $q->id }}" :placeholder="__('Enter answer')" />
                            @endif
                            <flux:error name="answer.{{ $q->id }}" />
                        </flux:field>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-zinc-500">
                    {{ __('No basic data questions found for survey 120.') }}
                </div>
            @endif

            <div class="flex justify-end mt-6">
                <flux:button type="submit" variant="primary" icon="check">
                    {{ __('Save Data') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
