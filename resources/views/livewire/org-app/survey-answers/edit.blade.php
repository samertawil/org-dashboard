<div class="flex flex-col gap-6" x-data x-on:scroll-to-top.window="window.scrollTo({ top: 0, behavior: 'smooth' })">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ __('Fill in the survey answer details below.') }}</flux:subheading>
        </div>
        <div class="flex gap-2">
            <flux:button href="{{ route('survey-answers.index') }}" wire:navigate variant="ghost" icon="arrow-left">
                {{ __('Back to List') }}
            </flux:button>
        </div>
    </div>

    <form wire:submit="save" class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden p-6 space-y-6">
        
        @include('livewire.org-app.survey-answers.survey-answers-form')

        <div class="flex items-center justify-end border-t border-zinc-200 dark:border-zinc-700 pt-4 mt-6">
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ $type == 'save' ? __('Create Answer') : __('Update Answer') }}</span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Saving...') }}
                </span>
            </flux:button>
        </div>
    </form>
</div>
