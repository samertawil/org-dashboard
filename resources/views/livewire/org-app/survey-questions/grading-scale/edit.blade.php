<div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-8">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ __('Edit the evaluation range for the survey grading scale.') }}</flux:subheading>
        </div>
        <flux:button href="{{ route('survey.grading.scale.index') }}" wire:navigate variant="ghost" icon="arrow-left">
            {{ __('Back to List') }}
        </flux:button>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden p-6">
        <form wire:submit.prevent="update" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Percentage Range --}}
                <flux:field>
                    <flux:label>{{ __('From Percentage (%)') }}</flux:label>
                    <flux:input type="number" wire:model="from_percentage" min="0" max="100" />
                    <flux:error name="from_percentage" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('To Percentage (%)') }}</flux:label>
                    <flux:input type="number" wire:model="to_percentage" min="0" max="100" />
                    <flux:error name="to_percentage" />
                </flux:field>

                {{-- Evaluation & Description --}}
                <flux:field class="md:col-span-2">
                    <flux:label>{{ __('Evaluation Name') }}</flux:label>
                    <flux:input wire:model="evaluation" :placeholder="__('Example: Excellent, Good, etc.')" />
                    <flux:error name="evaluation" />
                </flux:field>

                <flux:field class="md:col-span-2">
                    <flux:label>{{ __('Description') }}</flux:label>
                    <flux:textarea wire:model="description" :placeholder="__('Additional details about this evaluation level...')" rows="3" />
                    <flux:error name="description" />
                </flux:field>

                {{-- Selects --}}
                <flux:field>
                    <flux:label>{{ __('Select Batch') }}</flux:label>
                    <flux:select wire:model="batch_no">
                        <option value="">{{ __('Choose Batch...') }}</option>
                        @foreach($this->batches as $batch)
                            <option value="{{ $batch->batch_no }}">{{ $batch->batch_no }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="batch_no" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Survey Section') }}</flux:label>
                    <flux:select wire:model="survey_for_section">
                        <option value="">{{ __('Choose Section...') }}</option>
                        @foreach($this->surveySections as $section)
                            <option value="{{ $section->id }}">{{ $section->status_name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="survey_for_section" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Grading Type') }}</flux:label>
                    <flux:select wire:model="type">
                        <option value="">{{ __('Choose Type...') }}</option>
                        @foreach($this->gradingTypes as $gType)
                            <option value="{{ $gType->id }}">{{ $gType->status_name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="type" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Question Type') }}</flux:label>
                    <flux:select wire:model="question_type">
                        <option value="">{{ __('Choose Question Type...') }}</option>
                        @foreach($this->questionTypes as $qType)
                            <option value="{{ $qType->id }}">{{ $qType->status_name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="question_type" />
                </flux:field>
            </div>

            <div class="flex justify-end pt-6 border-t border-zinc-100 dark:border-zinc-700">
                <flux:button type="submit" variant="primary" icon="check" class="bg-blue-600 hover:bg-blue-700 text-white">
                    {{ __('Update Grading Scale') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
