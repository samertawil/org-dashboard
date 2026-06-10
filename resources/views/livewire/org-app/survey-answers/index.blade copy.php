<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Survey Answers') }}</flux:heading>
            <flux:subheading>{{ __('Manage survey answers.') }}</flux:subheading>
        </div>
        <div class="flex w-full sm:w-auto">
            @can('survey.create')
                <span title="{{ __('Add a new survey answer manually') }}" class="w-full sm:w-auto">
                    <flux:button href="{{ route('survey-answers.create') }}" wire:navigate variant="primary" icon="plus" class="w-full">
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
                <flux:input wire:model="searchAccountId" :placeholder="__('Account ID')" icon="identification" />
                <flux:select wire:model="searchAccountName" :placeholder="__('Student Name')">
                    <option value="">{{ __('Select Student') }}</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->identity_number }}">{{ $student->full_name }}</option>
                    @endforeach
                </flux:select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                <flux:input wire:model="searchSurveyNo" :placeholder="__('Survey No')" icon="document-text" />
                <flux:select wire:model="searchCreatedBy" :placeholder="__('Created By')">
                    <option value="">{{ __('Any Creator') }}</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </flux:select>
                <flux:input type="date" wire:model="searchCreatedAt" :placeholder="__('Created At')" />
                
                <div class="flex items-center gap-2">
                    <span title="{{ __('Apply search filters') }}" class="flex-1">
                        <flux:button wire:click="searchData" variant="primary" size="sm" icon="magnifying-glass" class="w-full">
                            {{ __('Find Data') }}
                        </flux:button>
                    </span>
                    @if ($searchAccountId || $searchSurveyNo || $searchCreatedBy || $searchCreatedAt || $readyToLoad)
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
                        <span
                            class="font-medium text-zinc-900 dark:text-white">{{ $answers->firstItem() }}</span>
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
                                <span class="text-xs font-bold text-zinc-500 uppercase">{{ __('Question') }} #{{ $answer->question->question_order }}</span>
                                <span class="text-sm font-medium text-zinc-900 dark:text-white leading-tight mt-1">{{ $answer->question?->question_ar_text ?? '-' }}</span>
                            </div>
                            <span class="text-[10px] text-zinc-400 whitespace-nowrap">{{ $answer->created_at ? $answer->created_at->format('Y-m-d') : '' }}</span>
                        </div>
                        
                        <div class="bg-zinc-50 dark:bg-zinc-900/50 p-2 rounded text-sm text-zinc-700 dark:text-zinc-200 border border-zinc-100 dark:border-zinc-700">
                            <span class="font-bold text-xs block mb-1 text-zinc-400 uppercase">{{ __('Answer') }}</span>
                            {{ $answer->answer_ar_text ?? '-' }}
                        </div>

                        <div class="flex flex-col gap-1 text-[11px] text-zinc-500">
                            <div><span class="font-medium">{{ __('Student') }}:</span> {{ $answer->student->full_name ?? '-' }} ({{ $answer->account_id }})</div>
                            <div><span class="font-medium">{{ __('Survey') }}:</span> {{ $answer->surveyfor->status_name ?? '-' }}</div>
                            <div><span class="font-medium">{{ __('By') }}:</span> {{ $answer->creator?->name ?? '-' }}</div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-700">
                            <span title="{{ __('Edit answer') }}">
                                <flux:button href="{{ route('survey-answers.edit', $answer) }}" wire:navigate
                                    variant="ghost" size="xs" icon="pencil-square" />
                            </span>
                            <span title="{{ __('Delete answer') }}">
                                <flux:button wire:click="delete({{ $answer->id }})"
                                    wire:confirm="{{ __('Are you sure you want to delete this answer?') }}"
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
                            <th wire:click="sortBy('id')"
                                class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                <div class="flex items-center gap-1">
                                    {{ __('ID') }}
                                    @if ($sortField === 'id')
                                        <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
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
                                        <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                            class="size-3" />
                                    @else
                                        <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                    @endif
                                </div>
                            </th>
                            <th wire:click="sortBy('account_id')"
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                                <div class="flex items-center gap-1">
                                    {{ __('Account ID') }}
                                    @if ($sortField === 'account_id')
                                        <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                            class="size-3" />
                                    @else
                                        <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                    @endif
                                </div>
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                {{ __('Question') }}
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
                                        <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
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
                        @php
                            $previousAccountId = null;
                            $previousSurveyNo = null;
                        @endphp
                        @forelse($answers as $answer)
                            @php
                                $showGroupInfo =
                                    $answer->account_id !== $previousAccountId || $answer->survey_no !== $previousSurveyNo;
                                $previousAccountId = $answer->account_id;
                                $previousSurveyNo = $answer->survey_no;

                                $fullName = $answer->student->full_name ?? '';
                                $parts = explode(' ', trim($fullName));
                                $count = count($parts);
                                $firstName = $parts[0] ?? '';
                                $lastName = $count > 1 ? $parts[$count - 1] : '';
                                $middleName = $count > 2 ? $parts[$count - 2] : '';
                            @endphp
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                                <td class="px-6 py-4 text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $answer->question->question_order }}
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                    @if ($showGroupInfo)
                                        {{ $answer->surveyfor->status_name ?? '-' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                    @if ($showGroupInfo)
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ $answer->account_id ?? '-' }}</span>
                                            <span class="text-xs text-zinc-500">{{ $firstName }} {!! $middleName ? '&nbsp;' . $middleName : '' !!} {!! $lastName ? '&nbsp;' . $lastName : '' !!}</span>
                                        </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                    <div class="flex flex-col gap-1 max-w-xs">
                                        <span class="text-xs text-zinc-400">{{ __('Q') }}: {{ $answer->question?->question_ar_text ?? '-' }}</span>
                                        <span class="font-medium text-zinc-900 dark:text-white">{{ __('A') }}: {{ $answer->answer_ar_text ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $answer->creator?->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                    {{ $answer->created_at ? $answer->created_at->format('Y-m-d H:i') : null }}</td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <span title="{{ __('Edit') }}">
                                            <flux:button href="{{ route('survey-answers.edit', $answer) }}" wire:navigate
                                                variant="ghost" size="sm" icon="pencil-square" />
                                        </span>
                                        <span title="{{ __('Delete') }}">
                                            <flux:button wire:click="delete({{ $answer->id }})"
                                                wire:confirm="{{ __('Are you sure you want to delete this answer?') }}"
                                                variant="ghost" size="sm" icon="trash"
                                                class="text-red-500 hover:text-red-600" />
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-zinc-500">
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
</div>
