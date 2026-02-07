<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Students') }}</flux:heading>
            <flux:subheading>{{ __('Manage your students and their details.') }}</flux:subheading>
        </div>

        <div class="flex gap-2">
            <flux:button href="{{ route('student.imported-files') }}" wire:navigate variant="ghost" icon="archive-box">
                {{ __('Archived Imports') }}
            </flux:button>
            <flux:modal.trigger name="import-modal">
                <flux:button variant="ghost" icon="document-arrow-up">{{ __('Import Excel') }}</flux:button>
            </flux:modal.trigger>
            <flux:button href="{{ route('student.create') }}" wire:navigate variant="primary" icon="plus">
                {{ __('Add Student') }}
            </flux:button>
        </div>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Error Message --}}
    @if (session('error'))
        <div class="rounded-md bg-red-50 dark:bg-red-900/50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <flux:icon name="x-circle" class="h-5 w-5 text-red-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                        {{ __('Import Errors') }}
                    </h3>
                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                        <p>{!! session('error') !!}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Search and Table Section --}}
    <div
        class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 relative">
            <flux:input wire:model.live="search" :placeholder="__('Search by name or identity number...')"
                icon="magnifying-glass" />
            <div wire:loading wire:target="search" class="absolute right-6 top-1/2 -translate-y-1/2">
                <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
            </div>
        </div>

        @if ($search)
            <div class="mt-4 flex items-center justify-end">
                <flux:button wire:click="$set('search', '');" variant="ghost" size="sm" icon="x-mark">
                    {{ __('Clear Filters') }}
                </flux:button>
            </div>
        @endif

        <div class="overflow-x-auto">
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                        {{ __('Showing') }}
                        <span
                            class="font-medium text-zinc-900 dark:text-white">{{ $this->students->firstItem() }}</span>
                        {{ __('to') }}
                        <span
                            class="font-medium text-zinc-900 dark:text-white">{{ $this->students->lastItem() }}</span>
                        {{ __('of') }}
                        <span class="font-medium text-zinc-900 dark:text-white">{{ $this->students->total() }}</span>
                        {{ __('results') }}
                    </p>
                </div>
            </div>
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th wire:click="sortBy('full_name')"
                            class="px-6 py-3 text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-200 transition-colors">
                            <div class="flex items-center gap-1">
                                {{ __('Name') }}
                                @if ($sortField === 'full_name')
                                    <flux:icon name="{{ $sortDirection === 'asc' ? 'chevron-up' : 'chevron-down' }}"
                                        class="size-3" />
                                @else
                                    <flux:icon name="chevron-up-down" class="size-3 text-zinc-300" />
                                @endif
                            </div>
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Identity Number') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Group') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Gender') }}
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Enrollment') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Status') }}
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($this->students as $student)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $student->full_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $student->identity_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ $student->studentGroup?->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                {{ ucfirst($student->gender) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                @if($student->enrollment_type === 'full_week')
                                    {{ __('Full Week') }}
                                @elseif($student->enrollment_type === 'sat_mon_wed')
                                    {{ __('Sat/Mon/Wed') }}
                                @elseif($student->enrollment_type === 'sun_tue_thu')
                                    {{ __('Sun/Mon/Thu') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @php
                                    $statusEnum = \App\Enums\GlobalSystemConstant::tryFrom($student->activation);
                                @endphp
                                @if ($statusEnum)
                                    <span @class([
                                        'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium',
                                        'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400' =>
                                            $student->activation == 1,
                                        'bg-zinc-100 text-zinc-700 dark:bg-zinc-500/20 dark:text-zinc-400' =>
                                            $student->activation != 1,
                                    ])>
                                        {{ $statusEnum->label() }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button href="{{ route('student.edit', $student) }}" wire:navigate
                                        variant="ghost" size="sm" icon="pencil-square" />
                                    <flux:modal.trigger name="feedback-modal">
                                        <div class="relative">
                                            <flux:button wire:click="manageFeedback({{ $student->id }})"
                                                variant="ghost" size="sm" icon="chat-bubble-left-right"
                                                style="{{ $student->feedbacks_count > 0 ? 'color: #3b82f6 !important;' : '' }}" />
                                            @if ($student->feedbacks_count > 0)
                                                <span
                                                    class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-blue-500 ring-1 ring-white dark:ring-zinc-900"></span>
                                            @endif
                                        </div>
                                    </flux:modal.trigger>
                                    <flux:button wire:click="delete({{ $student->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this student?') }}"
                                        variant="ghost" size="sm" icon="trash"
                                        class="text-red-500 hover:text-red-600" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-zinc-500">
                                {{ __('No students found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $this->students->links() }}
        </div>
    </div>

    {{-- Import Modal --}}
    <flux:modal name="import-modal" class="md:w-96">
        <div class="p-6 space-y-6">
            <div class="flex flex-col gap-1">
                <flux:heading size="lg">{{ __('Import Students') }}</flux:heading>
                <flux:subheading>{{ __('Upload an Excel file to import students.') }}</flux:subheading>
            </div>

            <form wire:submit="import">
                <flux:field>
                    <flux:label>{{ __('Excel File') }}</flux:label>
                    <input type="file" wire:model="excelFile"
                        class="block w-full text-sm text-zinc-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100
                    " />
                    <flux:error name="excelFile" />
                </flux:field>

                <div class="flex justify-end gap-2 mt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">{{ __('Import') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    {{-- Feedback Modal --}}
    <flux:modal name="feedback-modal" class="md:w-[32rem]">
        <div class="p-6 space-y-6">
            <div class="flex flex-col gap-1">
                <flux:heading size="lg">{{ __('Student Feedback') }}</flux:heading>
                @if ($this->selectedStudent)
                    <flux:subheading>{{ __('Manage feedback for') }} <span
                            class="font-bold">{{ $this->selectedStudent->full_name }}</span></flux:subheading>
                @endif
            </div>

            @if (session('feedback_success'))
                <div class="rounded-md bg-green-50 dark:bg-green-900/50 p-4">
                    <p class="text-sm text-green-700 dark:text-green-300">{{ session('feedback_success') }}</p>
                </div>
            @endif

            <div class="space-y-4">
                {{-- Existing Feedback List --}}
                <div class="max-h-60 overflow-y-auto space-y-3 pr-2">
                    @forelse($this->studentFeedbacks as $feedback)
                        <div
                            class="bg-zinc-50 dark:bg-zinc-900 p-3 rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <div class="flex justify-between items-start mb-1">
                                <div class="flex flex-col gap-1 mx-2">
                                    <span
                                        class="text-xs text-zinc-500">{{ $feedback->created_at->format('M d, Y H:i') }}-
                                        {{ $feedback->feedbackTimeStatus->status_name ?? __('N/A') }} </span>

                                    <span
                                        class="text-xs font-medium text-zinc-600 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-2 py-0.5 rounded-full w-fit">
                                        {{ $feedback->feedbackTypeStatus?->status_name ?? __('N/A') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center">
                                        <flux:icon name="star" variant="solid"
                                            class="w-3 h-3 text-yellow-500 mr-1" />
                                        <span class="text-xs font-bold">{{ $feedback->rating }}</span>
                                    </div>
                                    <flux:button wire:click="deleteFeedback({{ $feedback->id }})"
                                        wire:confirm="{{ __('Are you sure you want to delete this feedback?') }}"
                                        variant="ghost" size="xs" icon="trash"
                                        class="text-red-500 hover:text-red-600" />
                                </div>
                            </div>
                            <p class="text-sm text-zinc-700 dark:text-zinc-300 whitespace-pre-wrap">
                                {{ $feedback->comment }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-zinc-500 italic text-center py-4">{{ __('No feedback yet.') }}</p>
                    @endforelse
                </div>

                {{-- Add Feedback Form --}}
                <form wire:submit="saveFeedback" class="space-y-4 border-t border-zinc-200 dark:border-zinc-700 pt-4">
                    <flux:field>
                        <flux:label class="bg-zinc-50 dark:bg-zinc-900 p-3">{{ __('Feedback Type') }}</flux:label>
                        <select wire:model="feedbackType"
                            class="block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Type') }}
                            </option>
                            @foreach ($this->feedbackTypes->where('p_id_sub', 56) as $type)
                                <option value="{{ $type->id }}">{{ $type->status_name }}</option>
                            @endforeach
                        </select>
                        <flux:error name="feedbackType" />
                    </flux:field>

                    <flux:field>
                        <flux:label class="bg-zinc-50 dark:bg-zinc-900 p-3">{{ __('Feedback Time') }}</flux:label>
                        <select wire:model="studentFeedBackTime"
                            class="block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="" class="text-gray-500 placeholder-gray-500">{{ __('Select Time') }}
                            </option>
                            @foreach ($this->feedbackTypes->where('p_id_sub', 60) as $type)
                                <option value="{{ $type->id }}">{{ $type->status_name }}</option>
                            @endforeach
                        </select>
                        <flux:error name="studentFeedBackTime" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Rating') }}</flux:label>
                        <select wire:model="feedbackRating"
                            class="block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Good</option>
                            <option value="3">3 - Average</option>
                            <option value="2">2 - Poor</option>
                            <option value="1">1 - Very Poor</option>
                        </select>
                        <flux:error name="feedbackRating" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('New Comment') }}</flux:label>
                        <flux:textarea wire:model="feedbackComment" placeholder="{{ __('Enter feedback...') }}"
                            rows="3" />
                        <flux:error name="feedbackComment" />
                    </flux:field>

                    <div class="flex justify-end gap-2">
                        <flux:modal.close>
                            <flux:button variant="ghost">{{ __('Done') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">{{ __('Add Feedback') }}</flux:button>
                    </div>
                </form>
            </div>
        </div>
    </flux:modal>
</div>
