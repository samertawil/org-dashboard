<div class="min-h-screen bg-zinc-50 dark:bg-zinc-950 flex flex-col items-center justify-center p-4">
    <div class="w-full max-w-2xl bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-white">
            <h1 class="text-2xl font-bold">{{ $survey->survey_name }}</h1>
            <p class="text-blue-100 mt-2">{{ __('Please fill in the information below carefully.') }}</p>
        </div>

        <div class="p-8">
            @if($step == 0)
                {{-- Step 0: Survey Closed --}}
                <div class="text-center py-12">
                    <div class="mx-auto w-20 h-20 bg-amber-100 dark:bg-amber-900/40 rounded-full flex items-center justify-center mb-6">
                        <flux:icon name="no-symbol" class="size-10 text-amber-600 dark:text-amber-400" />
                    </div>
                    <h2 class="text-2xl font-bold dark:text-white">{{ __('Survey Currently Closed') }}</h2>
                    <p class="text-zinc-600 dark:text-zinc-400 mt-2">{{ __('This survey is not accepting responses at the moment. Please check back later.') }}</p>
                    <div class="mt-8">
                        <flux:button href="/" variant="primary">{{ __('Return Home') }}</flux:button>
                    </div>
                </div>

            @elseif($step == 1)
                {{-- Step 1: Identification --}}
                <div class="space-y-6">
                    <div class="text-center">
                        <div class="mx-auto w-16 h-16 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center mb-4">
                            <flux:icon name="identification" class="size-8 text-blue-600 dark:text-blue-400" />
                        </div>
                        <h2 class="text-xl font-semibold dark:text-white">{{ __('Identity Verification') }}</h2>
                        <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{{ __('Enter your ID/Card Number to start the survey.') }}</p>
                    </div>

                    <form wire:submit="startSurvey" class="space-y-4">
                        <flux:input label="{{ __('Card Number / ID') }}" wire:model="account_id" placeholder="123456789"  required />
                        <flux:button type="submit" variant="primary" size="base" class="w-full">
                            {{ __('Start Survey') }}
                        </flux:button>
                    </form>
                </div>

            @elseif($step == 2)
                {{-- Step 2: Survey Questions --}}
                <form wire:submit="submit" class="space-y-8">
                    @foreach($survey->questions as $question)
                        <div class="space-y-3">
                            <flux:label class="text-lg font-medium dark:text-white">{{ $question->question_order }}. {{ $question->question_ar_text }}</flux:label>
                            
                            @if($question->answer_input_type == 1) {{-- Short Text --}}
                                <flux:input wire:model="answers.{{ $question->id }}.answer" placeholder="{{ __('Type your answer here...') }}" />
                            @elseif($question->answer_input_type == 2) {{-- Multiple Choice --}}
                                <flux:radio.group wire:model="answers.{{ $question->id }}.answer" class="grid grid-cols-1 gap-2">
                                    @foreach($question->answer_options as $option)
                                        <flux:radio value="{{ $option['value'] }}" label="{{ $option['label'] }}" />
                                    @endforeach
                                </flux:radio.group>
                            @elseif($question->answer_input_type == 3) {{-- Number --}}
                                <flux:input type="number" wire:model="answers.{{ $question->id }}.answer" />
                            @elseif($question->answer_input_type == 5) {{-- Date --}}
                                <flux:input type="date" wire:model="answers.{{ $question->id }}.answer" />
                            @elseif($question->answer_input_type == 4) {{-- Long Text --}}
                                <flux:textarea wire:model="answers.{{ $question->id }}.answer" rows="3" />
                            @endif

                            @if($question->require_detail)
                                <div class="mt-2 pl-4 border-l-2 border-blue-200 dark:border-blue-800">
                                    <flux:input wire:model="answers.{{ $question->id }}.detail" placeholder="{{ $question->detail ?? __('Please provide more details...') }}" />
                                </div>
                            @endif
                        </div>
                        <hr class="border-zinc-100 dark:border-zinc-800" />
                    @endforeach

                    <div class="flex justify-between items-center pt-4">
                        <flux:button wire:click="$set('step', 1)" variant="ghost">{{ __('Go Back') }}</flux:button>
                        <flux:button type="submit" variant="primary" size="base">
                            {{ __('Submit Response') }}
                        </flux:button>
                    </div>
                </form>

            @elseif($step == 3)
                {{-- Step 3: Success --}}
                <div class="text-center py-12">
                    <div class="mx-auto w-20 h-20 bg-green-100 dark:bg-green-900/40 rounded-full flex items-center justify-center mb-6">
                        <flux:icon name="check" class="size-10 text-green-600 dark:text-green-400" />
                    </div>
                    <h2 class="text-2xl font-bold dark:text-white">{{ __('Thank you!') }}</h2>
                    <p class="text-zinc-600 dark:text-zinc-400 mt-2">{{ session('message') }}</p>
                    <div class="mt-8">
                        <flux:button href="/" variant="primary">{{ __('Return Home') }}</flux:button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
