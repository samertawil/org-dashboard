<div class="max-w-4xl mx-auto flex flex-col gap-6">
    <div class="flex flex-col gap-1">
        <flux:heading level="1" size="xl">{{ __('Create Comparison Scale') }}</flux:heading>
        <flux:subheading>{{ __('Define a new smart evaluation rule for survey comparison.') }}</flux:subheading>
    </div>

    <flux:card>
        <form wire:submit="save" class="flex flex-col gap-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="from_percentage" type="number" step="0.01" :label="__('From Difference %')" :placeholder="__('e.g., -100')" required />
                <flux:input wire:model="to_percentage" type="number" step="0.01" :label="__('To Difference %')" :placeholder="__('e.g., -1')" required />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="evaluation" :label="__('Evaluation Label')" :placeholder="__('e.g., Regression')" required />
                <flux:input wire:model="color" type="color" :label="__('Label Color')" class="h-12" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <flux:select wire:model="domain_id" :label="__('Specific Domain (Optional)')">
                    <option value="">{{ __('All Domains (Total)') }}</option>
                    @foreach ($domains as $domain)
                        <option value="{{ $domain->id }}">{{ $domain->status_name }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="batch_no" :label="__('Batch Number (Optional)')">
                    <option value="">{{ __('All Batches') }}</option>
                    @foreach ($batches as $batch)
                        <option value="{{ $batch->batch_no }}">{{ __('Batch') }} {{ $batch->batch_no }}</option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="survey_for_section" :label="__('Survey Section')">
                    @foreach ($surveySections as $section)
                        <option value="{{ $section->id }}">{{ $section->status_name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <flux:textarea wire:model="description" :label="__('Description (Internal Note)')" :placeholder="__('Describe when this rule applies...')" />

            <div class="flex justify-end gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:button href="{{ route('org-app.survey-questions.comparison-scale.index') }}" wire:navigate variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Save Comparison Scale') }}
                </flux:button>
            </div>
        </form>
    </flux:card>
</div>
