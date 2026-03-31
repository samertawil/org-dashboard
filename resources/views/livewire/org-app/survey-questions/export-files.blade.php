<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Export Files') }}</flux:heading>
            <flux:subheading>{{ __('Download specific survey and report data in Excel format.') }}</flux:subheading>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Survey Answers Export Card --}}
        <div
            class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-6 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <flux:icon name="document-text" class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <flux:heading size="lg">{{ __('Survey Answers Export') }}</flux:heading>
                </div>

                <flux:text class="text-sm">
                    {{ __('Export all student answers for a specific survey group. Includes student names, group names, and creator details.') }}
                </flux:text>

                <div class="pt-2">
                    <flux:field>
                        <flux:label>{{ __('Select Survey Group') }}</flux:label>
                        <flux:select wire:model="surveyNo">
                            <option value="">{{ __('All Surveys') }}</option>
                            @foreach ($surveys as $survey)
                                <option value="{{ $survey->id }}">{{ $survey->status_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>
            </div>

            <div class="mt-6">
                <flux:button wire:click="exportSurveyAnswers" variant="primary" icon="document-arrow-down"
                    class="w-full">
                    {{ __('Download Excel') }}
                </flux:button>
            </div>
        </div>

        {{-- Survey Answers Pivot Export Card --}}
        <div
            class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-6 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                        <flux:icon name="table-cells" class="size-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <flux:heading size="lg">{{ __('Pivoted Survey Export') }}</flux:heading>
                </div>

                <flux:text class="text-sm">
                    {{ __('Export student records where each question is a separate column. Best for statistical analysis and overview.') }}
                </flux:text>

                <div class="pt-2">
                    <flux:field>
                        <flux:label badge="Required" badgeColor="text-red-600">{{ __('Select Survey Group') }}
                        </flux:label>
                        <flux:select wire:model="surveyNoPivot">
                            <option value="">{{ __('Select Survey...') }}</option>
                            @foreach ($surveys as $survey)
                                <option value="{{ $survey->id }}">{{ $survey->status_name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="surveyNoPivot" />
                    </flux:field>
                </div>


                <div class="pt-2">
                    <flux:field>
                        <flux:label>{{ __('Select Education Point Name') }}</flux:label>
                        <flux:select wire:model="groupIdPivot">
                            <option value="">{{ __('Select Point...') }}</option>
                            @foreach ($groupNames as $groupName)
                                <option value="{{ $groupName->id }}">{{ $groupName->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="groupIdPivot" />
                    </flux:field>
                </div>
            </div>

            <div class="mt-6">
                <flux:button wire:click="exportSurveyAnswersPivot" variant="primary" icon="table-cells" class="w-full">
                    {{ __('Download Pivoted Excel') }}
                </flux:button>
            </div>
        </div>


        {{-- export Survey Late Export Card --}}
        <div
            class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-6 flex flex-col justify-between">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                        <flux:icon name="clock" class="size-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <flux:heading size="lg">{{ __('Late Survey Export') }}</flux:heading>
                </div>

                <flux:text class="text-sm">
                    {{ __('Export student records where are they late to provided.') }}
                </flux:text>

                <div class="pt-2">
                    <flux:field>
                        <flux:label>{{ __('Select Survey Group') }}</flux:label>
                        <flux:select wire:model="surveyLate">
                            <option value="">{{ __('Select Survey...') }}</option>
                            @foreach ($surveys as $survey)
                                <option value="{{ $survey->id }}">{{ $survey->status_name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="surveyLate" />
                    </flux:field>
                </div>


                <div class="pt-2">
                    <flux:field>
                        <flux:label>{{ __('Select Education Point Name') }}</flux:label>
                        <flux:select wire:model="groupIdLate">
                            <option value="">{{ __('Select Point...') }}</option>
                            @foreach ($groupNames as $groupName)
                                <option value="{{ $groupName->id }}">{{ $groupName->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="groupIdLate" />
                    </flux:field>
                </div>
            </div>

            <div class="mt-6">
                <flux:button wire:click="exportSurveyLate" variant="primary" icon="document-arrow-down" class="w-full">
                    {{ __('Download Late List') }}
                </flux:button>
            </div>
        </div>
        {{-- Future Export Cards can go here --}}
        <div
            class="bg-zinc-50 dark:bg-zinc-900/50 rounded-xl border border-dashed border-zinc-300 dark:border-zinc-700 p-6 flex flex-col items-center justify-center text-center opacity-70">
            <flux:icon name="plus" class="size-8 text-zinc-400 mb-2" />
            <flux:text size="sm" class="text-zinc-500 italic">{{ __('More export types coming soon...') }}
            </flux:text>
        </div>
    </div>
</div>
