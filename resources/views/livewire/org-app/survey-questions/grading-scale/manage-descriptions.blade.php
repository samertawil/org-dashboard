<div class="flex flex-col gap-6" x-data>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ $heading }}</flux:heading>
            <flux:subheading>{{ $subheading }}</flux:subheading>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <flux:button href="{{ route('survey.grading.scale.index') }}" variant="ghost" icon="arrow-left">
                {{ __('Back to List') }}
            </flux:button>
            @if($domain_id && count($scales) > 0)
                <flux:button wire:click="save" variant="primary" icon="check">
                    {{ __('Save Descriptions') }}
                </flux:button>
            @endif
        </div>
    </div>

    <x-auth-session-status class="text-center" :status="session('message')" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Selection Sidebar --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-6">
                <flux:heading size="lg" class="mb-4">{{ __('Selection Filters') }}</flux:heading>
                
                <div class="space-y-4">
                    <flux:select label="{{ __('Section') }}" wire:model.live="surveyForSection">
                        <option value="">{{ __('Choose Section...') }}</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}">{{ $section->status_name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select label="{{ __('Batch') }}" wire:model.live="batch_no">
                        <option value="">{{ __('All Batches') }}</option>
                        @foreach ($batches as $batch)
                            <option value="{{ $batch->batch_no }}">{{ $batch->batch_no }}</option>
                        @endforeach
                    </flux:select>

                    <hr class="border-zinc-100 dark:border-zinc-700 my-4" />

                    <flux:select label="{{ __('Target Domain') }}" wire:model.live="domain_id">
                        <option value="">{{ __('Choose Domain...') }}</option>
                        @foreach ($domains as $domain)
                            <option value="{{ $domain->id }}">{{ $domain->status_name }}</option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            @if($domain_id)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 p-6 rounded-2xl">
                <div class="flex gap-3">
                    <flux:icon name="information-circle" class="size-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" />
                    <div>
                        <flux:heading size="sm" class="text-blue-900 dark:text-blue-200">{{ __('Selected Domain') }}</flux:heading>
                        <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                            {{ $domains->find($domain_id)->status_name ?? '' }}
                        </p>
                        <p class="text-xs text-blue-600/70 dark:text-blue-400/70 mt-2">
                            {{ __('You are now defining specific descriptions for this domain across all available grading scales for the selected section/batch.') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Main Content: Descriptions List --}}
        <div class="lg:col-span-2">
            @if(!$domain_id)
                <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-700 p-12 text-center">
                    <flux:icon name="cursor-arrow-rays" class="size-12 text-zinc-300 mx-auto mb-4" />
                    <flux:heading size="lg" class="text-zinc-500">{{ __('Select a Domain to Begin') }}</flux:heading>
                    <flux:subheading>{{ __('Choose a section and domain from the sidebar to start entering descriptions.') }}</flux:subheading>
                </div>
            @elseif(count($scales) === 0)
                <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-dashed border-zinc-200 dark:border-zinc-700 p-12 text-center">
                    <flux:icon name="exclamation-triangle" class="size-12 text-zinc-300 mx-auto mb-4" />
                    <flux:heading size="lg" class="text-zinc-500">{{ __('No Grading Scales Found') }}</flux:heading>
                    <flux:subheading>{{ __('There are no grading scales defined for the selected section and batch.') }}</flux:subheading>
                    <div class="mt-6">
                        <flux:button href="{{ route('survey.grading-scale.create') }}" variant="filled" size="sm" icon="plus">
                            {{ __('Create Grading Scale') }}
                        </flux:button>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($scales as $scale)
                        <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden group transition-all hover:shadow-md">
                            <div class="bg-zinc-50 dark:bg-zinc-900/50 px-6 py-3 border-b border-zinc-100 dark:border-zinc-700 flex justify-between items-center">
                                <div class="flex items-center gap-4">
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 rounded-full text-xs font-bold tracking-tight">
                                        {{ $scale->from_percentage }}% - {{ $scale->to_percentage }}%
                                    </span>
                                    <flux:heading size="sm">{{ $scale->evaluation }}</flux:heading>
                                </div>
                                <flux:subheading size="xs">{{ __('Original:') }} {{ $scale->description }}</flux:subheading>
                            </div>
                            <div class="p-6 grid grid-cols-1 gap-6">
                                <div>
                                    <flux:textarea 
                                        label="{{ __('Domain Specific Description') }}" 
                                        wire:model="descriptions.{{ $scale->id }}.description" 
                                        placeholder="{{ __('Enter the description for this range in this domain...') }}"
                                        rows="3"
                                    />
                                    @error('descriptions.'.$scale->id.'.description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <flux:textarea 
                                        label="{{ __('Needs Processing / Intervention') }}" 
                                        wire:model="descriptions.{{ $scale->id }}.need_processing" 
                                        placeholder="{{ __('Specify any required actions for this result...') }}"
                                        rows="2"
                                    />
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-6 flex justify-end">
                        <flux:button wire:click="save" variant="primary" icon="check" class="px-8">
                            {{ __('Save All Descriptions') }}
                        </flux:button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
