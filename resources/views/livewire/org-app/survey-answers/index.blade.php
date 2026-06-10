<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Survey Answers') }}</flux:heading>
            <flux:subheading>{{ __('Manage survey answers.') }}</flux:subheading>
        </div>
        <div class="flex w-full sm:w-auto">
            @can('survey.create')
                <span title="{{ __('Add a new survey answer manually') }}" class="w-full sm:w-auto">
                    <flux:button href="{{ route('survey-answers.create') }}" wire:navigate variant="primary" icon="plus"
                        class="w-full">
                        {{ __('Add Answer') }}
                    </flux:button>
                </span>
            @endcan
        </div>
    </div>

    <x-auth-session-status class="text-center" :status="session('message')" />

    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:select wire:model.live="filterBatch">
                    <option value="">{{ __('Select Batch...') }}</option>
                    @foreach ($this->availableBatches as $batch)
                        <option value="{{ $batch }}">{{ $batch }}</option>
                    @endforeach
                </flux:select>
                <flux:select wire:model.live="filterGroup">
                    <option value="">{{ __('Education Point') }}</option>
                    @foreach ($this->availableGroups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input wire:model="searchAccountId" :placeholder="__('Identity Number')" icon="identification" />
                <flux:select wire:model="searchAccountName">
                    <option value="">{{ __('Select Student') }}</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->identity_number }}">{{ $student->full_name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                {{-- <flux:input wire:model="searchSurveyNo" :placeholder="__('Survey No')" icon="document-text" /> --}}

                <div>
                    <flux:select wire:model="searchSurveyNo">
                        <option value="">{{ __('Select Survey') }}</option>
                        @foreach ($this->surveys as $survey)
                            <option value="{{ $survey->survey_for_section }}">{{ $survey->survey_name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                @if ($isManager)
                    <flux:field>

                        <flux:select wire:model="searchCreatedBy" class="w-full">
                            <option value="">{{ __('All Employees') }}</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                @else
                    <flux:field>

                        <flux:input type="text" value="{{ auth()->user()->employee?->full_name }}" disabled
                            class="w-full bg-zinc-50 dark:bg-zinc-900/50" />
                    </flux:field>
                @endif

                <flux:input type="date" wire:model="searchCreatedAt" :placeholder="__('Created At')" />

                <div class="flex items-center gap-2">
                    <span title="{{ __('Apply search filters') }}" class="flex-1">
                        <flux:button wire:click="searchData" variant="primary" size="sm" icon="magnifying-glass"
                            class="w-full">
                            {{ __('Find Data') }}
                        </flux:button>
                    </span>
                    @if (
                        $searchAccountId ||
                            $searchSurveyNo ||
                            $searchCreatedBy ||
                            $searchCreatedAt ||
                            $filterBatch ||
                            $filterGroup ||
                            $readyToLoad)
                        <span title="{{ __('Reset search filters') }}">
                            <flux:button wire:click="clearFilters" variant="ghost" size="sm" icon="x-mark">
                                {{ __('Clear') }}
                            </flux:button>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                        {{ __('Showing') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $answers->firstItem() }}</span>
                        {{ __('to') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $answers->lastItem() }}</span>
                        {{ __('of') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $answers->total() }}</span>
                        {{ __('results') }}
                    </p>
                </div>
            </div>

            {{-- Mobile Cards View --}}
            <div class="md:hidden divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($answers as $answer)
                    <div class="p-4 space-y-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-zinc-500 uppercase">{{ __('Student') }}</span>
                                <span
                                    class="text-sm font-medium text-zinc-900 dark:text-white leading-tight mt-1">{{ $answer->student->full_name ?? '-' }}
                                    ({{ $answer->account_id }})
                                </span>
                            </div>
                            <span
                                class="text-[10px] text-zinc-400 whitespace-nowrap">{{ $answer->created_at ? \Carbon\Carbon::parse($answer->created_at)->format('Y-m-d') : '' }}</span>
                        </div>

                        <div class="flex flex-col gap-1 text-[11px] text-zinc-500">
                            <div><span class="font-medium">{{ __('Survey') }}:</span>
                                {{ $answer->surveyfor->status_name ?? '-' }}</div>
                            <div><span class="font-medium">{{ __('By') }}:</span>
                                {{ $answer->creator?->full_name ?? '-' }}</div>
                        </div>

                        <div
                            class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-700">
                            <span title="{{ __('View Answers') }}">
                                <flux:button
                                    wire:click="openAnswersModal('{{ $answer->account_id }}', '{{ $answer->survey_no }}')"
                                    variant="ghost" size="xs" icon="eye" class="text-blue-500" />
                            </span>
                            <span title="{{ __('Delete Entire Survey') }}">
                                <flux:button
                                    wire:click="deleteSurveyGroup('{{ $answer->account_id }}', '{{ $answer->survey_no }}')"
                                    wire:confirm="{{ __('Are you sure you want to delete all answers for this survey?') }}"
                                    variant="ghost" size="xs" icon="trash" class="text-red-500" />
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-zinc-500 italic">
                        {{ __('No survey answers found.') }}
                    </div>
                @endforelse
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                    <thead class="bg-zinc-50 dark:bg-zinc-900">
                        <tr>
                            <th wire:click="sortBy('account_id')"
                                class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                <div class="flex items-center gap-1">
                                    {{ __('Student') }}
                                    @if ($sortField === 'account_id')
                                        <flux:icon
                                            name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                            class="size-3" />
                                    @else
                                        <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('survey_no')"
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                <div class="flex items-center gap-1">
                                    {{ __('Survey No') }}
                                    @if ($sortField === 'survey_no')
                                        <flux:icon
                                            name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                            class="size-3" />
                                    @else
                                        <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                    @endif
                                </div>
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Created By') }}
                            </th>
                            <th wire:click="sortBy('created_at')"
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                <div class="flex items-center gap-1">
                                    {{ __('Date') }}
                                    @if ($sortField === 'created_at')
                                        <flux:icon
                                            name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                            class="size-3" />
                                    @else
                                        <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                    @endif
                                </div>
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($answers as $answer)
                            @php
                                $fullName = $answer->student->full_name ?? '';
                                $parts = explode(' ', trim($fullName));
                                $count = count($parts);
                                $firstName = $parts[0] ?? '';
                                $lastName = $count > 1 ? $parts[$count - 1] : '';
                                $middleName = $count > 2 ? $parts[$count - 2] : '';
                            @endphp
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                    <div class="flex flex-col">
                                        <span
                                            class="font-medium text-zinc-900 dark:text-white">{{ $answer->account_id ?? '-' }}</span>
                                        <span class="text-xs text-zinc-500">{{ $firstName }}
                                            {!! $middleName ? '&nbsp;' . $middleName : '' !!} {!! $lastName ? '&nbsp;' . $lastName : '' !!}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300 font-medium">
                                    {{ $answer->surveyfor->status_name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $answer->creator?->full_name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $answer->created_at ? \Carbon\Carbon::parse($answer->created_at)->format('Y-m-d H:i') : null }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <span title="{{ __('View Answers') }}">
                                            <flux:button
                                                wire:click="openAnswersModal('{{ $answer->account_id }}', '{{ $answer->survey_no }}')"
                                                variant="ghost" size="sm" icon="eye"
                                                class="text-blue-500 hover:text-blue-600" />
                                        </span>
                                        <span title="{{ __('Delete Entire Survey') }}">
                                            <flux:button
                                                wire:click="deleteSurveyGroup('{{ $answer->account_id }}', '{{ $answer->survey_no }}')"
                                                wire:confirm="{{ __('Are you sure you want to delete all answers for this survey?') }}"
                                                variant="ghost" size="sm" icon="trash"
                                                class="text-red-500 hover:text-red-600" />
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-zinc-500">
                                    {{ __('No survey answers found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $answers->links() }}
        </div>
    </div>

    {{-- Modal to view and edit answers --}}
    <flux:modal wire:model="showAnswersModal" class="md:w-[900px] max-h-[90vh] overflow-y-auto">
        <div class="space-y-6">
            <div class="flex justify-between items-start border-b border-zinc-100 dark:border-zinc-700 pb-4">
                <div>
                    <flux:heading size="lg">{{ __('Survey Details') }}</flux:heading>
                    @if ($selectedAccountId && count($this->selectedSurveyAnswers) > 0)
                        @php
                            $firstAnswer = $this->selectedSurveyAnswers->first();
                        @endphp
                        <flux:subheading class="mt-1">
                            {{ __('Student') }}: <span
                                class="font-semibold text-zinc-900 dark:text-white">{{ $firstAnswer->student->full_name ?? '-' }}
                                ({{ $selectedAccountId }})</span>
                            <span class="mx-2">|</span>
                            {{ __('Survey') }}: <span
                                class="font-semibold text-zinc-900 dark:text-white">{{ $firstAnswer->surveyfor->status_name ?? '-' }}</span>
                        </flux:subheading>
                    @endif
                </div>
            </div>

            @if (session()->has('modal_message'))
                <div
                    class="bg-green-50/50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 p-3 rounded-lg text-sm text-center">
                    {{ session('modal_message') }}
                </div>
            @endif

            <div class="space-y-6 divide-y divide-zinc-100 dark:divide-zinc-700">
                @forelse($this->selectedSurveyAnswers as $index => $item)
                    <div class="pt-4 first:pt-0 space-y-3">
                        <div class="flex justify-between items-start gap-4">
                            <div class="flex-1">
                                <span class="text-xs font-bold text-indigo-500 uppercase">
                                    {{ __('Question') }} #{{ $item->question->question_order ?? $index + 1 }}
                                    @if ($item->question->domainRel)
                                        ({{ $item->question->domainRel->status_name }})
                                    @endif
                                </span>
                                <h4 class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 mt-1">
                                    {{ $item->question->question_ar_text ?? '-' }}
                                </h4>
                            </div>

                            @if ($editingAnswerId !== $item->id)
                                <div class="flex items-center gap-1 shrink-0">
                                    <span title="{{ __('Edit Answer') }}">
                                        <flux:button wire:click="startEditAnswer({{ $item->id }})"
                                            variant="ghost" size="xs" icon="pencil-square"
                                            class="text-blue-500" />
                                    </span>
                                    <span title="{{ __('Delete Answer') }}">
                                        <flux:button wire:click="deleteAnswer({{ $item->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this answer?') }}"
                                            variant="ghost" size="xs" icon="trash" class="text-red-500" />
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div
                            class="bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-lg border border-zinc-100 dark:border-zinc-700">
                            @if ($editingAnswerId === $item->id)
                                <form wire:submit.prevent="saveAnswer" class="space-y-4">
                                    <flux:field>
                                        <flux:label>{{ __('Your Answer') }}</flux:label>
                                        <div class="mt-2">
                                            @if ($item->question->answer_input_type == 2 && is_array($item->question->answer_options))
                                                {{-- Multiple Choice --}}
                                                <flux:radio.group wire:model="editingAnswerText"
                                                    class="flex flex-col gap-3">
                                                    @foreach ($item->question->answer_options as $option)
                                                        @if (is_array($option) && isset($option['value']) && isset($option['label']))
                                                            <flux:radio value="{{ $option['value'] }}"
                                                                label="{{ $option['label'] }}" />
                                                        @elseif(is_string($option))
                                                            <flux:radio value="{{ $option }}"
                                                                label="{{ $option }}" />
                                                        @endif
                                                    @endforeach
                                                </flux:radio.group>
                                            @elseif($item->question->answer_input_type == 3)
                                                {{-- Number --}}
                                                <flux:input type="number" wire:model="editingAnswerText"
                                                    min="{{ $item->question->min_score }}"
                                                    max="{{ $item->question->max_score }}" />
                                            @elseif($item->question->answer_input_type == 4)
                                                {{-- Long Text --}}
                                                <flux:textarea wire:model="editingAnswerText" rows="3" />
                                            @elseif($item->question->answer_input_type == 5)
                                                {{-- Date --}}
                                                <flux:input type="date" wire:model="editingAnswerText" />
                                            @else
                                                {{-- Short Text --}}
                                                <flux:input wire:model="editingAnswerText" />
                                            @endif
                                        </div>
                                        <flux:error name="editingAnswerText" />
                                    </flux:field>

                                    <div class="flex justify-end gap-2">
                                        <flux:button wire:click="cancelEdit" size="sm" variant="ghost">
                                            {{ __('Cancel') }}</flux:button>
                                        <flux:button type="submit" size="sm" variant="primary">
                                            {{ __('Save') }}</flux:button>
                                    </div>
                                </form>
                            @else
                                <div class="text-sm text-zinc-700 dark:text-zinc-200">
                                    <span
                                        class="font-bold text-xs block mb-1 text-zinc-400 uppercase">{{ __('Answer') }}</span>
                                    @if ($item->answer_label)
                                        {{ $item->answer_label }}
                                    @else
                                        {{ $item->answer_ar_text ?? '-' }}
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-zinc-500 italic">
                        {{ __('No answers found for this survey.') }}
                    </div>
                @endforelse
            </div>

            <div class="flex justify-end pt-4 border-t border-zinc-100 dark:border-zinc-700">
                <flux:button wire:click="$set('showAnswersModal', false)" variant="ghost">{{ __('Close') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
