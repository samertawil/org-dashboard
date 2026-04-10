<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ $subheading ?? __('Enter the details for the student below.') }}</flux:subheading>
        </div>
        @can('student.index')
            <flux:button href="{{ route('student.index') }}" wire:navigate variant="ghost" icon="list-bullet">
                {{ __('Student List') }}
            </flux:button>
        @endcan
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />
  
    {{-- Form Section --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <form wire:submit="{{ $type }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- Basic Information Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-2">
                <flux:heading size="lg">{{ __('Basic Information') }}</flux:heading>
            </div>

            {{-- Identity Number --}}
            <div class="flex items-end gap-2 col-span-full">
                <flux:field class="flex-1">
                    <flux:label badge="Required" badgeColor="text-red-600">{{ __('Identity Number') }}</flux:label>
                    <flux:input type="number" wire:model="identity_number" />
                    <flux:error name="identity_number" />
                </flux:field>

                <flux:button wire:click.prevent="getData" variant="ghost" icon="magnifying-glass" class="mb-[2px]" />
            </div>

            {{-- Full Name --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Full Name') }}</flux:label>
                <flux:input type="text" wire:model="full_name" :placeholder="__('Enter full name')" />
                <flux:error name="full_name" />
            </flux:field>

            {{-- Birth Date --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Birth Date') }}</flux:label>
                <flux:input type="date" wire:model="birth_date" />
                <flux:error name="birth_date" />
            </flux:field>

            {{-- Gender --}}
            <flux:select wire:model="gender" :label="__('Gender')">
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Gender') }}</option>
                @foreach ($genders as $g)
                    <option value="{{ $g['value'] }}">{{ $g['label'] }}</option>
                @endforeach
            </flux:select>

            {{-- Education Point/Center --}}
            <flux:select wire:model="student_groups_id" :label="__('Education Point/Center')">
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Group') }}</option>
                @foreach ($studentGroups as $group)
                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                @endforeach
            </flux:select>

            {{-- Enrollment Type --}}
            <flux:select wire:model="enrollment_type" :label="__('Enrollment Type')">
                <option value="sat_mon_wed">{{ __('Saturday / Monday / Wednesday') }}</option>
                <option value="sun_tue_thu">{{ __('Sunday / Tuesday / Thursday') }}</option>
                <option value="full_week">{{ __('Full Week') }}</option>
            </flux:select>

            {{-- Status --}}
            <flux:select wire:model="status_id" :label="__('Status')">
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Status') }}</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->id }}">
                        {{ $status->status_name }}{{ $status->description ? '  - ' . $status->description : '' }}
                    </option>
                @endforeach
            </flux:select>

            {{-- Notes --}}
            <flux:field class="md:col-span-2 lg:col-span-3">
                <flux:label>{{ __('Notes') }}</flux:label>
                <flux:textarea wire:model="notes" :placeholder="__('Enter notes')" spellcheck="true" />
                <flux:error name="notes" />
            </flux:field>


            {{-- System Settings Header --}}
            <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                <flux:heading size="lg">{{ __('System Settings') }}</flux:heading>
            </div>
            {{-- Activation --}}
            <flux:field>
                <flux:label badge="Required" badgeColor="text-red-600">{{ __('Status') }}</flux:label>
                <flux:select wire:model="activation">
                    @foreach ($activations as $a)
                        <option value="{{ $a['value'] }}">{{ $a['label'] }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="activation" />
            </flux:field>
            {{-- Survey Questions Header --}}
            @if (count($this->surveyquestions) > 0)
                <div class="md:col-span-2 lg:col-span-3 border-b border-zinc-100 dark:border-zinc-700 pb-2 mt-4 mb-2">
                    <flux:heading size="md">{{ __('Survey Questions') }}</flux:heading>
                </div>

                @if(!empty($relations_data))
                    <div class="md:col-span-2 lg:col-span-3 mb-4 mt-2 bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <flux:radio.group wire:model.live="selected_relation" label="{{ __('Select Relation for Answers') }}">
                            <div class="flex flex-col gap-2 mt-2">
                                @foreach ($relations_data as $index => $relation)
                                    <flux:radio value="{{ $index }}" label="{{ $relation['fullNameArabic'] ?? '' }} - {{ $relation['relationTypeName'] ?? '' }} ({{ $relation['relationIdentityNumber'] ?? '' }})" />
                                @endforeach
                            </div>
                        </flux:radio.group>
                    </div>
                @endif

                @foreach ($this->surveyquestions as $index => $q)
                    <flux:field class="col-span-full">

                        <flux:label>{{ $index + 1 }} - {{ $q->question_ar_text }}</flux:label>
                        @if ($q->answer_input_type == 2)
                            @if ($q->answer_options && count($q->answer_options) > 0)
                                <flux:select wire:model="answer.{{ $q->id }}">
                                    <option value="" class="text-gray-500 placeholder-gray-500">
                                        {{ __('Select answer') }}</option>
                                    @foreach ($q->answer_options as $option)
                                        @if (is_array($option) && isset($option['value']) && isset($option['label']))
                                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                        @elseif(!is_array($option))
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endif
                                    @endforeach
                                </flux:select>
                            @else
                                <flux:input type="text" wire:model="answer.{{ $q->id }}"
                                    :placeholder="__('Enter answer')" />
                                <flux:error name="answer.{{ $q->id }}" />
                            @endif
                        @else
                            <flux:input type="text" wire:model="answer.{{ $q->id }}"
                                :placeholder="__('Enter answer')" />
                            <flux:error name="answer.{{ $q->id }}" />
                        @endif


                    </flux:field>
                @endforeach
            @endif

            {{-- Submit Button --}}

            <div class="md:col-span-2 lg:col-span-3 flex items-center justify-end gap-2 mt-6">
                <flux:button type="submit" variant="primary" icon="{{ $type === 'save' ? 'plus' : 'check' }}">
                    {{ $heading }}
                </flux:button>
            </div>

            <div class="md:col-span-2 lg:col-span-3 flex justify-end w-full text-end">
                <div class="flex flex-col items-end gap-2">
                    @include('layouts._show_all_input_error')
                    {{-- <x-auth-session-status class="{{ session('type') == 'error' ? 'text-red-500' : '' }}"
                        :status="session('message')" /> --}}
                </div>
            </div>
        </form>
    </div>
</div>
