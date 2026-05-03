<div class="flex flex-col gap-6" x-data x-on:scroll-to-top.window="window.scrollTo({ top: 0, behavior: 'smooth' })">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ $subheading ?? __('Enter the details for the student below.') }}</flux:subheading>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            @if($survey_table_id || ($surveyForSection && $batch_no))
                <span title="{{ __('Create a new question entry') }}" class="w-full sm:w-auto">
                    <flux:button wire:click="addQuestion" variant="primary" icon="plus" class="w-full">
                        {{ __('Add Question') }}
                    </flux:button>
                </span>
                <span title="{{ __('Save all changes to questions') }}" class="w-full sm:w-auto">
                    <flux:button wire:click="save" variant="filled" icon="check" class="bg-green-600 hover:bg-green-700 text-white w-full">
                        {{ __('Save Changes') }}
                    </flux:button>
                </span>
            @endif
        </div>
    </div>

    @if($survey_table_id)
    <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800 p-4 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-100 dark:bg-blue-800 rounded-lg shrink-0">
                <flux:icon name="information-circle" class="size-5 text-blue-600 dark:text-blue-300" />
            </div>
            <div>
                <flux:heading size="sm">{{ __('Editing Questions for:') }} {{ \App\Models\SurveyTable::find($survey_table_id)->survey_name ?? '' }}</flux:heading>
                <flux:subheading>{{ __('All questions added here will be linked to this specific survey.') }}</flux:subheading>
            </div>
        </div>
        <span title="{{ __('Return to survey definitions list') }}" class="w-full sm:w-auto">
            <flux:button href="{{ route('survey.index') }}" variant="ghost" size="sm" icon="arrow-left" class="w-full">{{ __('Back to Surveys') }}</flux:button>
        </span>
    </div>
    @endif

    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Section Selector --}}
    @if(!$survey_table_id)
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-4 sm:p-6">
        <div class="border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-4">
            <flux:heading size="lg">{{ __('Survey Section & Batch') }}</flux:heading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <flux:select label="{{ __('Select Section') }}" wire:model.live='surveyForSection'>
                    <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Choose Section...') }}</option>
                    @foreach ($surveyFor->where('p_id_sub',config('appConstant.survey_for')) as $section)
                        <option value="{{ $section->id }}">{{ $section->status_name ?? __('No Section Name') }}</option>
                    @endforeach
                </flux:select>
                <flux:subheading class="mt-3">
                    {{ __('To add a new section, go to Status or') }} 
                    <a href="{{route('status.create')}}" class="text-blue-500 hover:underline">{{__('Press Here')}}</a> 
                    {{ __('and create a child for "Sector For"') }}
                </flux:subheading>
            </div>
            <div>
                <flux:select label="{{ __('Select Batch') }}" wire:model.live='batch_no'>
                    <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Choose Batch...') }}</option>
                    @foreach ($batches as $batch)
                        <option value="{{ $batch->batch_no }}">{{ $batch->batch_no }}</option>
                    @endforeach
                </flux:select>
                <flux:subheading class="mt-3">
                    {{ __('Batch numbers come from') }} 
                    <a href="{{route('student.group.index')}}" class="text-blue-500 hover:underline">{{__('Student Groups')}}</a>
                </flux:subheading>
            </div>
        </div>
    </div>
    @endif

    <div wire:loading wire:target="surveyForSection,batch_no" class="flex justify-center p-4">
        <flux:icon name="arrow-path" class="size-8 animate-spin text-blue-500" />
    </div>

    @if($survey_table_id || ($surveyForSection && $batch_no))
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-4 sm:p-6">
        <div class="border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-6">
            <div class="flex justify-between items-center">
                <flux:heading size="lg">{{ __('Listed Questions') }}</flux:heading>
            </div>
            <flux:subheading class="mt-2">
                {{ __('To add a new Domain, go to Status or') }} 
                <a href="{{ route('status.create') }}" class="text-blue-500 hover:underline">{{ __('Press Here') }}</a> 
                {{ __('and create a child for "Domains of Assessment"') }}
            </flux:subheading>
        </div>

        <div class="space-y-6">
            @forelse($questions as $index => $question)
                <div class="p-5 border rounded-2xl {{ empty($question['id']) ? 'bg-green-50/50 dark:bg-green-900/20 border-green-200 dark:border-green-800 ring-1 ring-green-100 dark:ring-green-900/30' : 'border-zinc-200 dark:border-zinc-700 bg-zinc-50/50 dark:bg-zinc-900/30' }} transition-all hover:shadow-lg relative group">
                    
                    @if(empty($question['id']))
                        <div class="absolute -top-3 left-6 px-3 py-1 bg-green-500 text-white text-[10px] font-bold uppercase tracking-wider rounded-full shadow-sm z-10">
                            {{ __('New Question') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        
                        {{-- Row 1: Text and Type --}}
                        <div class="md:col-span-5">
                            <flux:input type="text" label="{{ __('Question Text (Arabic)') }}"  
                                wire:model="questions.{{ $index }}.question_ar_text" />
                            @error('questions.'.$index.'.question_ar_text') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-3">
                            <flux:select label="{{ __('Domain') }}" wire:model="questions.{{ $index }}.domain_id">
                                <option value="">{{ __('Choose Domain...') }}</option>
                                @foreach ($surveyFor->where('p_id_sub',config('appConstant.domains_of_assessment')) as $section)
                                    <option value="{{ $section->id }}">{{ $section->status_name }}</option>
                                @endforeach
                            </flux:select>
                        </div>

                        <div class="md:col-span-3">
                           <flux:select label="{{ __('Answer Type') }}" wire:model.live="questions.{{ $index }}.answer_input_type">
                                <option value="1">{{ __('Short Text') }}</option>
                                <option value="2">{{ __('Multiple Choice') }}</option>
                                <option value="3">{{ __('Number') }}</option>
                                <option value="5">{{ __('Date') }}</option>
                                <option value="4">{{ __('Long Text') }}</option>
                                <option value="6">{{ __('File Upload') }}</option>
                            </flux:select> 
                        </div>

                        <div class="md:col-span-1">
                            <flux:input type="number" label="{{ __('Order') }}" wire:model="questions.{{ $index }}.question_order" min="1" />
                        </div>
                          
                        {{-- Row 2: Logic and Validation --}}
                        <div class="md:col-span-12 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-12 gap-6 items-end border-t border-zinc-100 dark:border-zinc-800 pt-4">
                            <div class="lg:col-span-2">
                                <flux:checkbox label="{{ __('Required') }}" wire:model="questions.{{ $index }}.required_answer" />
                            </div>
                            <div class="lg:col-span-2">
                                <flux:checkbox label="{{ __('Has Details') }}" wire:model="questions.{{ $index }}.require_detail" />
                            </div>
                            <div class="lg:col-span-3">
                                <flux:input type="text" label="{{ __('Detail Label') }}" wire:model="questions.{{ $index }}.detail" placeholder="{{ __('e.g. Specify...') }}" />
                            </div>
                            <div class="lg:col-span-4">
                                <flux:input type="text" label="{{ __('Internal Note') }}" wire:model="questions.{{ $index }}.note" placeholder="{{ __('Administrative notes...') }}"/>
                            </div>
                            <div class="lg:col-span-1 flex justify-end">
                                <span title="{{ __('Delete this question') }}">
                                    <flux:button wire:click="removeQuestion({{ $index }})" variant="ghost" size="sm" icon="trash" 
                                        wire:confirm="{{ __('Are you sure you want to delete this question?') }}" 
                                        class="text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20" />
                                </span>
                            </div>
                        </div>

                        {{-- Row 3: Scoring --}}
                        <div class="md:col-span-12 grid grid-cols-2 md:grid-cols-4 gap-6 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                            <div>
                                <flux:input type="number" label="{{ __('Min Score') }}" wire:model="questions.{{ $index }}.min_score" />
                                @error('questions.'.$index.'.min_score') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <flux:input type="number" label="{{ __('Max Score') }}" wire:model="questions.{{ $index }}.max_score" />
                                @error('questions.'.$index.'.max_score') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
    
                        {{-- Row 4: Multiple Choice Options --}}
                        @if((isset($question['answer_input_type']) && $question['answer_input_type'] == 2))
                        <div class="md:col-span-12 mt-4 bg-white dark:bg-zinc-800/40 p-5 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                                <div>
                                    <flux:heading size="sm">{{ __('Answer Options') }}</flux:heading>
                                    <flux:subheading size="xs">{{ __('Define the multiple choice options here.') }}</flux:subheading>
                                </div>
                                <span title="{{ __('Add a new choice option') }}">
                                    <flux:button wire:click="addAnswerOption({{ $index }})" variant="ghost" size="xs" icon="plus">
                                        {{ __('Add Option') }}
                                    </flux:button>
                                </span>
                            </div>
                            
                            <div class="space-y-4">
                                @if(isset($question['answer_options']) && is_array($question['answer_options']))
                                    @foreach($question['answer_options'] as $optIndex => $option)
                                        <div class="flex items-center gap-4 p-3 bg-zinc-50 dark:bg-zinc-900/50 rounded-lg border border-zinc-100 dark:border-zinc-800 group/opt transition-all">
                                            <div class="flex-1 grid grid-cols-2 gap-4">
                                                <flux:input  type="text" placeholder="{{ __('Label (Display)') }}" wire:model="questions.{{ $index }}.answer_options.{{ $optIndex }}.label" />
                                                <flux:input type="text" placeholder="{{ __('Value (Data)') }}" wire:model="questions.{{ $index }}.answer_options.{{ $optIndex }}.value" />
                                            </div>
                                            <span title="{{ __('Remove this option') }}">
                                                <flux:button wire:click="removeAnswerOption({{ $index }}, {{ $optIndex }})" variant="ghost" size="xs" icon="x-mark" class="text-zinc-400 hover:text-red-500" />
                                            </span>
                                        </div>
                                    @endforeach
                                @endif
                                
                                @if(!isset($question['answer_options']) || empty($question['answer_options']))
                                    <div class="text-center py-8 bg-zinc-50/50 dark:bg-zinc-900/20 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-700">
                                        <flux:icon name="list-bullet" class="size-8 text-zinc-300 mx-auto mb-2" />
                                        <p class="text-xs text-zinc-500">{{ __('No options added yet. Click "Add Option" to create choices.') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-zinc-50/50 dark:bg-zinc-900/20 rounded-3xl border border-dashed border-zinc-200 dark:border-zinc-700">
                    <flux:icon name="question-mark-circle" class="size-12 text-zinc-300 mx-auto mb-4" />
                    <flux:heading size="lg" class="text-zinc-500">{{ __('No Questions Found') }}</flux:heading>
                    <flux:subheading>{{ __('Start by adding your first question using the button at the top.') }}</flux:subheading>
                </div>
            @endforelse
        </div>
        
        @if(count($questions) > 0)
        <div class="mt-10 pt-6 border-t border-zinc-100 dark:border-zinc-700 flex justify-end">
            <span title="{{ __('Apply and save all changes made to these questions') }}" class="w-full sm:w-auto">
                <flux:button wire:click="save" variant="primary" icon="check" class="w-full sm:w-auto px-8">
                    {{ __('Save All Questions') }}
                </flux:button>
            </span>
        </div>
        @endif
    </div>
    @endif
    @endif
</div>
