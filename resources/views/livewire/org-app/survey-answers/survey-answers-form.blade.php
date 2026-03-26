<div class="grid grid-cols-1 gap-6">

    @if($type === 'save')
        {{-- Section Selector --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6 overflow-hidden relative">
            <div class="border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-4">
                <flux:heading size="lg">{{ __('Survey Configuration') }}</flux:heading>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:field>
                    <flux:label>{{ __('Select Section') }} <span class="text-red-500">*</span></flux:label>
                    <flux:select wire:model.live='surveyForSection'>
                        <option value="" class="text-gray-500">{{ __('Choose Section...') }}</option>
                        @foreach ($surveyFor as $section)
                            <option value="{{ $section->id }}">{{ $section->status_name ?? __('No Name') }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="surveyForSection" />
                </flux:field>

              
                <flux:field>
                    <flux:label>{{ __('Select Student') }} <span class="text-red-500">*</span></flux:label>
                    <flux:select wire:model.live='account_id'>
                        <option value="" class="text-gray-500">{{ __('Choose Student...') }}</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->identity_number }}">{{ $student->full_name ?? __('No Name') }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="account_id" />
                </flux:field>

            </div>
            
            <div wire:loading wire:target="surveyForSection" class="absolute inset-0 bg-white/50 dark:bg-zinc-800/50 flex items-center justify-center z-10">
                <flux:icon name="arrow-path" class="size-8 animate-spin text-indigo-500" />
            </div>
        </div>

        {{-- Questions Loop --}}
        @if($this->surveyForSection && count($this->questionsBySurveyForSection) > 0)
            <div class="mt-4">
                <div class="border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-4">
                    <flux:heading size="lg">{{ __('Survey Questions') }}</flux:heading>
                </div>
                
                <div class="space-y-6">
                    @foreach($this->questionsBySurveyForSection as $index => $question)
                        <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-6 border border-zinc-200 dark:border-zinc-700 shadow-sm">
                            <flux:field>
                                <flux:label class="text-base font-semibold text-zinc-800 dark:text-zinc-200 mb-4">
                                    {{ $index + 1 }}. {{ $question->question_ar_text ?? $question->question_en_text }}
                                    @if($question->note)
                                        <span class="block text-sm font-normal text-zinc-500 mt-1">{{ $question->note }}</span>
                                    @endif
                                </flux:label>
                                
                                <div class="mt-2">
                                @if($question->answer_input_type == 2 && is_array($question->answer_options))
                                    {{-- Type 2: Multiple Choice / Radio --}}
                                    <flux:radio.group wire:model="answers.{{ $question->id }}" class="flex flex-col gap-3">
                                        @foreach($question->answer_options as $option)
                                            @if(is_array($option) && isset($option['value']) && isset($option['label']))
                                                <flux:radio value="{{ $option['value'] }}" label="{{ $option['label'] }}" />
                                            @elseif(is_string($option))
                                                <flux:radio value="{{ $option }}" label="{{ $option }}" />
                                            @endif
                                        @endforeach
                                    </flux:radio.group>
                                @elseif($question->answer_input_type == 3 && is_array($question->answer_options))
                                    {{-- Type 3: Checkbox Group --}}
                                    <flux:checkbox.group wire:model="answers.{{ $question->id }}" class="flex flex-col gap-3">
                                        @foreach($question->answer_options as $option)
                                            @if(is_array($option) && isset($option['value']) && isset($option['label']))
                                                <flux:checkbox value="{{ $option['value'] }}" label="{{ $option['label'] }}" />
                                            @elseif(is_string($option))
                                                <flux:checkbox value="{{ $option }}" label="{{ $option }}" />
                                            @endif
                                        @endforeach
                                    </flux:checkbox.group>
                                @elseif($question->answer_input_type == 4 && is_array($question->answer_options))
                                    {{-- Type 4: Select Dropdown --}}
                                    <flux:select wire:model="answers.{{ $question->id }}" :placeholder="__('Select Option')">
                                        <option value="">{{ __('Select...') }}</option>
                                         @foreach($question->answer_options as $option)
                                            @if(is_array($option) && isset($option['value']) && isset($option['label']))
                                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                            @elseif(is_string($option))
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endif
                                        @endforeach
                                    </flux:select>
                                @else
                                    {{-- Type 1 or fallback: Text --}}
                                    <flux:textarea wire:model="answers.{{ $question->id }}" rows="3" placeholder="{{ __('Write your answer here...') }}" />
                                @endif
                                </div>
                                <flux:error name="answers.{{ $question->id }}" />
                            </flux:field>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif($this->surveyForSection)
            <div class="flex items-center justify-center p-8 bg-zinc-50 dark:bg-zinc-900 rounded-lg border border-dashed border-zinc-300 dark:border-zinc-700 text-zinc-500">
                {{ __('No questions found for this section.') }}
            </div>
        @endif

    @else
        {{-- Single Edit View for Edit.php --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white dark:bg-zinc-800 p-6 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <flux:field>
                <flux:label>{{ __('Survey No') }} <span class="text-red-500">*</span></flux:label>
                <flux:input wire:model="survey_no" type="number" />
                <flux:error name="survey_no" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Account ID') }}</flux:label>
                <flux:input wire:model="account_id" type="number" />
                <flux:error name="account_id" />
            </flux:field>

            <flux:field class="col-span-1 md:col-span-2">
                <flux:label>{{ __('Question') }}</flux:label>
                <flux:select wire:model="question_id" disabled>
                     @foreach($this->questions as $question)
                        <option value="{{ $question->id }}">{{ $question->question_ar_text ?? $question->question_en_text }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field class="col-span-1 md:col-span-2">
                <flux:label>{{ __('Answer (AR)') }}</flux:label>
                <flux:textarea wire:model="answer_ar_text" rows="3" />
                <flux:error name="answer_ar_text" />
            </flux:field>

            <flux:field class="col-span-1 md:col-span-2">
                <flux:label>{{ __('Answer (EN)') }}</flux:label>
                <flux:textarea wire:model="answer_en_text" rows="3" />
                <flux:error name="answer_en_text" />
            </flux:field>
        </div>
    @endif
</div>
