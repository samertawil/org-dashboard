<div class="flex flex-col gap-6" x-data x-on:scroll-to-top.window="window.scrollTo({ top: 0, behavior: 'smooth' })">
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ $subheading ?? __('Enter the details for the student below.') }}</flux:subheading>
        </div>
        <div class="flex space-x-2 space-x-reverse">
            @if($surveyForSection)
            <flux:button wire:click="addQuestion" variant="primary" icon="plus">
                {{ __('Add New Question') }}
            </flux:button>
            <flux:button wire:click="save" variant="filled" icon="check" class="bg-green-600 hover:bg-green-700 text-white">
                {{ __('Save Changes') }}
            </flux:button>
            @endif
        </div>
    </div>

    {{-- Success Message --}}
    {{-- @if (session()->has('message'))
        <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 rounded-lg p-4 text-center">
            {{ session('message') }}
        </div>
    @endif --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Section Selector --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <div class="border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-4">
            <flux:heading size="lg">{{ __('Survey Section') }}</flux:heading>
        </div>

        <div >
            <flux:select label="{{ __('Select Section') }}" wire:model.live='surveyForSection'>
                <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Choose Section...') }}</option>
                @foreach ($surveyFor as $section)
                    <option value="{{ $section->id }}">{{ $section->status_name ?? __('No Section Name') }}</option>
                @endforeach
            </flux:select>
            <flux:subheading class="mt-3">To add new section go from Status or <a href="{{route('status.create')}}"><span class="text-blue-500">Press Here</span></a> and make child for "Sector For"</flux:subheading>
        </div>
       
    </div>
    <div wire:loading wire:target="surveyForSection" >
        <flux:icon name="arrow-path" class="size-7 animate-spin text-blue-400" />
    </div>

    @if($surveyForSection)
    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
        <div class="border-b border-zinc-100 dark:border-zinc-700 pb-2 mb-4 flex justify-between items-center">
            <flux:heading size="lg">{{ __('Listed Questions') }}</flux:heading>
        </div>

        <div class="space-y-4">
            @forelse($questions as $index => $question)
                <div class="p-4 border rounded-xl {{ empty($question['id']) ? 'bg-green-50 dark:bg-green-900 border-green-300 dark:border-green-700' : 'border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900' }} transition-all hover:shadow-md relative group">
                    
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mt-2 ">
                        
                        {{-- Row 1 --}}
                        <div class="md:col-span-1  ">
                            <flux:input type="text" label="{{ __('Question Text (Arabic)') }}"  
                            style="height:auto;color: blue; " 
                            wire:model="questions.{{ $index }}.question_ar_text" />
                            @error('questions.'.$index.'.question_ar_text') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-5">
                           <flux:select label="{{ __('Answer Type') }}" wire:model.live="questions.{{ $index }}.answer_input_type">
                                <option value="1">{{ __('Short Text (Text)') }}</option>
                                <option value="2">{{ __('Multiple Choice') }}</option>
                                 <option value="3">{{ __('Number') }}</option>
                                <option value="3">{{ __('Date') }}</option>
                                <option value="4">{{ __('Long Text (Textarea)') }}</option>
                              
                            </flux:select> 
                        </div>
                        {{-- <div class="md:col-span-4">
                            <flux:input type="text" label="{{ __('Question Text (English)') }}" wire:model="questions.{{ $index }}.question_en_text" />
                        </div> --}}
                      <flux:field>
                        <flux:label >Order</flux:label>
                        <flux:input  type="number"  wire:model="questions.{{ $index }}.question_order" min="1" />
                      </flux:field>
                          
                       
                        
                        {{-- Row 2 --}}
                        <div class="md:col-span-3 flex items-center pt-2 md:pt-6">
                            <flux:checkbox label="{{ __('Requires Additional Details') }}" wire:model="questions.{{ $index }}.require_detail" />
                        </div>
                        <div class="md:col-span-4">
                            <flux:input type="text" label="{{ __('Detail Description (Visible to User)') }}" wire:model="questions.{{ $index }}.detail" placeholder="{{ __('Example: Please state the reason...') }}" />
                        </div>
                        <div class="md:col-span-4">
                            <flux:input type="text" label="{{ __('Administrative Note') }}" wire:model="questions.{{ $index }}.note" placeholder="{{ __('Internal System Notes...') }}"/>
                        </div>
                        <div class="md:col-span-1 flex items-center pt-6 md:pt-6 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity z-10">
                            <flux:button wire:click="removeQuestion({{ $index }})" variant="danger" size="sm" icon="trash" wire:confirm="{{ __('Are you sure you want to delete?') }}" class="p-2 h-8 w-8 rounded-full!" />
                        </div>
    
                        {{-- Row 3 : Answer Options --}}
                        @if((isset($question['answer_input_type']) && $question['answer_input_type'] == 2))
                        <div class="md:col-span-12 mt-4 bg-zinc-100 dark:bg-zinc-800/50 p-4 rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <div class="flex justify-between items-center mb-4">
                                <flux:heading size="sm">{{ __('Answer Options (Multiple Choice)') }}</flux:heading>
                                <flux:button wire:click="addAnswerOption({{ $index }})" variant="primary" size="sm" icon="plus">
                                    {{ __('Add Option') }}
                                </flux:button>
                            </div>
                            
                            <div class="space-y-3">
                                @if(isset($question['answer_options']) && is_array($question['answer_options']))
                                    @foreach($question['answer_options'] as $optIndex => $option)
                                        <div class="flex items-center gap-3">
                                            <div class="flex-1">
                                                <flux:input  type="text" placeholder="{{ __('Label (e.g., Yes)') }}" wire:model="questions.{{ $index }}.answer_options.{{ $optIndex }}.label" />
                                            </div>
                                            <div class="flex-1">
                                                <flux:input type="text" placeholder="{{ __('Value (e.g., 1)') }}" wire:model="questions.{{ $index }}.answer_options.{{ $optIndex }}.value" />
                                            </div>
                                            <div>
                                                <flux:button wire:click="removeAnswerOption({{ $index }}, {{ $optIndex }})" variant="danger" size="sm" icon="trash" class="p-2 h-8 w-8 rounded-full!" />
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                
                                @if(!isset($question['answer_options']) || empty($question['answer_options']))
                                    <div class="text-center py-4 text-xs text-zinc-500">
                                        {{ __('No options added yet. Click "Add Option" to create choices.') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-zinc-500 italic">
                    {{ __('There are no questions in this section currently. You can add a new question using the button above.') }}
                </div>
            @endforelse
        </div>
        
        @if(count($questions) > 0)
        <div class="mt-8 pt-4 border-t border-zinc-100 dark:border-zinc-700 flex justify-end">
            <flux:button wire:click="save" variant="primary" icon="check" class="w-full sm:w-auto">
                {{ __('Save and Apply Changes') }}
            </flux:button>
        </div>
        @endif
    </div>
    @endif
</div>
