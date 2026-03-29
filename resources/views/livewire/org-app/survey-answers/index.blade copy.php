<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Survey Answers') }}</flux:heading>
            <flux:subheading>{{ __('Manage survey answers.') }}</flux:subheading>
        </div>
        <div class="flex gap-2">
            @can('survey.create')
                <flux:button href="{{ route('survey-answers.create') }}" wire:navigate variant="primary" icon="plus">
                    {{ __('Add Answer') }}
                </flux:button>
            @endcan
        </div>
    </div>

    <x-auth-session-status class="text-center" :status="session('message')" />

    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 relative">

            <flux:input wire:model="searchAccountId" :placeholder="__('Account ID')" />
            <flux:select wire:model="searchAccountName" :placeholder="__('Student Name')">
                @foreach ($students as $student)
                    <option value="{{ $student->identity_number }}">{{ $student->full_name }}</option>
                @endforeach
            </flux:select>
            <flux:input wire:model="searchSurveyNo" :placeholder="__('Survey No')" />
            <flux:select wire:model="searchCreatedBy" :placeholder="__('Created By')">
                <option value="">{{ __('Any Creator') }}</option>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
            </flux:select>
            <flux:input type="date" wire:model="searchCreatedAt" :placeholder="__('Created At')" />
        </div>
        <div class="px-4 pb-4 flex items-center justify-end gap-2 border-b border-zinc-200 dark:border-zinc-700">
            <flux:button wire:click="searchData" variant="primary" size="sm" icon="magnifying-glass">
                {{ __('Find Data') }}
            </flux:button>
            @if ($searchAccountId || $searchSurveyNo || $searchCreatedBy || $searchCreatedAt || $readyToLoad)
                <flux:button wire:click="clearFilters" variant="ghost" size="sm" icon="x-mark">
                    {{ __('Clear Filters') }}
                </flux:button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                        {{ __('Showing') }}
                        <span
                            class="font-medium text-zinc-900 dark:text-white">{{ $this->answers->firstItem() }}</span>
                        {{ __('to') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $this->answers->lastItem() }}</span>
                        {{ __('of') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $this->answers->total() }}</span>
                        {{ __('results') }}
                    </p>
                </div>
            </div>

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
                    @forelse($this->answers as $answer)
                       
                       
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $answer->question->question_order }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $answer->surveyfor->status_name }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                @php
                                $fullName =  $answer->student->full_name ;
                                 $parts = explode(' ', trim($fullName));
     
                                 $count = count($parts);
     
                                 $firstName = $parts[0] ?? ''; // first part
                                 $lastName = $parts[$count - 1] ?? ''; // last part
                                 $middleName = $count > 2 ? $parts[$count - 2] : ''; // part before last
                             @endphp
                                {{ $answer->account_id ?? '-' }}<br>
                                {{$firstName}}&nbsp;{{ $middleName}}&nbsp;{{$lastName}} </td>
                            
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $answer->question?->question_ar_text ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $answer->creator?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $answer->created_at ? $answer->created_at->format('Y-m-d H:i') : null }}</td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button href="{{ route('survey-answers.edit', $answer) }}" wire:navigate
                                        variant="ghost" size="sm" icon="pencil-square" />
                                    <flux:button wire:click="delete({{ $answer->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this answer?') }}"
                                        variant="ghost" size="sm" icon="trash"
                                        class="text-red-500 hover:text-red-600" />
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
        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $this->answers->links() }}
        </div>
    </div>
</div>
