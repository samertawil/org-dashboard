<div class="flex flex-col gap-6">
    <div class="flex items-start justify-between">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Students') }}</flux:heading>
            <flux:subheading>{{ __('Manage your students and their details.') }}</flux:subheading>
        </div>

        <div class="flex gap-2">
            @can('student.create')
                <flux:button href="{{ route('student.imported-files') }}" wire:navigate variant="ghost" icon="archive-box">
                    {{ __('Archived Imports') }}
                </flux:button>
            @endcan
            @can('student.create')
                <flux:modal.trigger name="import-modal">
                    <flux:button variant="ghost" icon="document-arrow-up">{{ __('Import Excel') }}</flux:button>
                </flux:modal.trigger>
                <flux:button href="{{ route('student.create') }}" wire:navigate variant="primary" icon="plus">
                    {{ __('Add Student') }}
                </flux:button>
            @endcan
        </div>
    </div>

    {{-- Success Message --}}
    <x-auth-session-status class="text-center" :status="session('message')" />

    {{-- Error Message --}}
    @if (session('error'))
        <div class="rounded-md bg-red-50 dark:bg-red-900/50 p-4">
            <div class="flex">
                <div class="shrink-0">
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

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 relative">

            <flux:input wire:model="searchIdentityNumber" :placeholder="__('Search by identity number...')" class="p-1"/>
               

            {{-- Searchable Student Name Field --}}
            <div class="px-4 pb-3" x-data="{
                open: false,
                query: '',
                selectedId: @entangle('searchStudentName'),
                selectedLabel: '',
                students: @js($this->studentsNames->map(fn($s) => ['id' => $s->id, 'label' => $s->full_name ?? ''])->values()),
                get filtered() {
                    if (!this.query.trim()) return this.students.slice(0, 50);
                    const q = this.query.toLowerCase();
                    return this.students.filter(s => s.label.toLowerCase().includes(q)).slice(0, 50);
                },
                select(student) {
                    this.selectedId = student.id;
                    this.selectedLabel = student.label;
                    this.query = student.label;
                    this.open = false;
                },
                clear() {
                    this.selectedId = '';
                    this.selectedLabel = '';
                    this.query = '';
                    this.open = false;
                },
                init() {
                    if (this.selectedId) {
                        const found = this.students.find(s => s.id == this.selectedId);
                        if (found) {
                            this.query = found.label;
                            this.selectedLabel = found.label;
                        }
                    }
                    this.$watch('selectedId', val => {
                        if (!val) {
                            this.query = '';
                            this.selectedLabel = '';
                        }
                    });
                }
            }" @click.outside="open = false">
                {{-- <flux:label>{{ __('Search Student by Name') }}</flux:label> --}}

                <div class="relative mt-1">
                    <div class="relative flex items-center">
                        <input type="text" x-model="query" @focus="open = true"
                            @input="open = true; if (!query) clear()" @keydown.escape="open = false"
                            @keydown.enter.prevent="if (filtered.length === 1) select(filtered[0])"
                            placeholder="{{ __('Type student name to filter...') }}" autocomplete="off"
                            class="w-full px-3 pr-9 py-2 text-sm rounded-lg border border-zinc-300 dark:border-zinc-600
                               bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100
                               placeholder-zinc-400 dark:placeholder-zinc-500
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                               transition-colors duration-150" />
                        {{-- Clear button --}}
                        <button type="button" x-show="query" @click="clear()"
                            class="absolute right-3 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                            tabindex="-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Selected badge --}}
                    {{-- <div x-show="selectedId && selectedLabel" x-cloak class="mt-1 flex items-center gap-1">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        <span x-text="selectedLabel"></span>
                    </span>
                </div> --}}

                    {{-- Dropdown list --}}
                    <div x-show="open && filtered.length > 0" x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto
                           rounded-lg border border-zinc-200 dark:border-zinc-600
                           bg-white dark:bg-zinc-800 shadow-lg">
                        <template x-for="student in filtered" :key="student.id">
                            <button type="button" @click="select(student)"
                                class="w-full text-left px-4 py-2.5 text-sm
                                   text-zinc-700 dark:text-zinc-200
                                   hover:bg-blue-50 dark:hover:bg-blue-500/10
                                   hover:text-blue-700 dark:hover:text-blue-300
                                   transition-colors duration-100 flex items-center gap-2"
                                :class="{
                                    'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 font-medium': selectedId ==
                                        student.id
                                }">
                                <svg x-show="selectedId == student.id" class="w-3.5 h-3.5 shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span x-text="student.label"></span>
                            </button>
                        </template>
                    </div>

                    {{-- No results --}}
                    <div x-show="open && query && filtered.length === 0" x-cloak
                        class="absolute z-50 mt-1 w-full rounded-lg border border-zinc-200 dark:border-zinc-600
                           bg-white dark:bg-zinc-800 shadow-lg px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400 text-center">
                        {{ __('No students found matching your search.') }}
                    </div>
                </div>
                <flux:error name="searchStudentName" />
            </div>

            {{-- Searchable Student Group Field --}}
            <div class="px-4 pb-3" x-data="{
                open: false,
                query: '',
                selectedId: @entangle('searchStudentGroupName'),
                selectedLabel: '',
                items: @js($this->educationPoints->map(fn($g) => ['id' => $g->id, 'label' => $g->name ?? ''])->values()),
                get filtered() {
                    if (!this.query.trim()) return this.items.slice(0, 50);
                    const q = this.query.toLowerCase();
                    return this.items.filter(i => i.label.toLowerCase().includes(q)).slice(0, 50);
                },
                select(item) {
                    this.selectedId = item.id;
                    this.selectedLabel = item.label;
                    this.query = item.label;
                    this.open = false;
                },
                clear() {
                    this.selectedId = '';
                    this.selectedLabel = '';
                    this.query = '';
                    this.open = false;
                },
                init() {
                    if (this.selectedId) {
                        const found = this.items.find(i => i.id == this.selectedId);
                        if (found) {
                            this.query = found.label;
                            this.selectedLabel = found.label;
                        }
                    }
                    this.$watch('selectedId', val => {
                        if (!val) {
                            this.query = '';
                            this.selectedLabel = '';
                        }
                    });
                }
            }" @click.outside="open = false">
                <div class="relative mt-1">
                    <div class="relative flex items-center">
                        <input type="text" x-model="query" @focus="open = true"
                            @input="open = true; if (!query) clear()" @keydown.escape="open = false"
                            @keydown.enter.prevent="if (filtered.length === 1) select(filtered[0])"
                            placeholder="{{ __('Type group name to filter...') }}" autocomplete="off"
                            class="w-full px-3 pr-9 py-2 text-sm rounded-lg border border-zinc-300 dark:border-zinc-600
                               bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100
                               placeholder-zinc-400 dark:placeholder-zinc-500
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                               transition-colors duration-150" />
                        {{-- Clear button --}}
                        <button type="button" x-show="query" @click="clear()"
                            class="absolute right-3 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                            tabindex="-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Dropdown list --}}
                    <div x-show="open && filtered.length > 0" x-cloak
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-50 mt-1 w-full max-h-60 overflow-y-auto
                           rounded-lg border border-zinc-200 dark:border-zinc-600
                           bg-white dark:bg-zinc-800 shadow-lg">
                        <template x-for="item in filtered" :key="item.id">
                            <button type="button" @click="select(item)"
                                class="w-full text-left px-4 py-2.5 text-sm
                                   text-zinc-700 dark:text-zinc-200
                                   hover:bg-blue-50 dark:hover:bg-blue-500/10
                                   hover:text-blue-700 dark:hover:text-blue-300
                                   transition-colors duration-100 flex items-center gap-2"
                                :class="{
                                    'bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-300 font-medium': selectedId ==
                                        item.id
                                }">
                                <svg x-show="selectedId == item.id" class="w-3.5 h-3.5 shrink-0" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span x-text="item.label"></span>
                            </button>
                        </template>
                    </div>

                    {{-- No results --}}
                    <div x-show="open && query && filtered.length === 0" x-cloak
                        class="absolute z-50 mt-1 w-full rounded-lg border border-zinc-200 dark:border-zinc-600
                           bg-white dark:bg-zinc-800 shadow-lg px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400 text-center">
                        {{ __('No groups found matching your search.') }}
                    </div>
                </div>
                <flux:error name="searchStudentGroupName" />
            </div>

             {{-- Enrollment Type --}}
        <flux:select wire:model="searchEnrollment">
            <option value="">All Enrollment Days</option>
            <option value="sat_mon_wed">{{ __('Saturday / Monday / Wednesday') }}</option>
            <option value="sun_tue_thu">{{ __('Sunday / Tuesday / Thursday') }}</option>
            <option value="full_week">{{ __('Full Week') }}</option>
        </flux:select>

        {{-- activation Type --}}
        <flux:field class="col-start-1 md:col-start-1 lg:col-start-1">
            <flux:select wire:model="searchActivation">
                <option value="">All Activaion Status</option>
                @foreach ($activations as $a)
                    <option value="{{ $a['value'] }}">{{ $a['label'] }}</option>
                @endforeach
            </flux:select>
        </flux:field>
        </div>

       

        <div class="px-4 pb-4 flex items-center justify-end gap-2 border-b border-zinc-200 dark:border-zinc-700">
            <flux:button wire:click="searchData" variant="primary" size="sm" icon="magnifying-glass">
                {{ __('Find Data') }}
            </flux:button>
            @if ($searchIdentityNumber || $searchStudentName || $searchStudentGroupName || $searchEnrollment || $readyToLoad)
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
                        {{-- <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                            {{ __('Gender') }}
                        </th> --}}
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
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
                            {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                @php
                                    $genderEnum = \App\Enums\GlobalSystemConstant::tryFrom($student->gender);
                                @endphp
                                {{ $genderEnum ? $genderEnum->label() : '-' }}
                            </td> --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">
                                @if ($student->enrollment_type === 'full_week')
                                    {{ __('Full Week') }}
                                @elseif($student->enrollment_type === 'sat_mon_wed')
                                    {{ __('Sat/Mon/Wed') }}
                                @elseif($student->enrollment_type === 'sun_tue_thu')
                                    {{ __('Sun/Mon/Thu') }}
                                @else
                                    -
                                @endif
                                - {{ $student->status->status_name ?? '' }}
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
                                    <flux:button href="{{ route('student.show', $student->id) }}" wire:navigate
                                        variant="ghost" size="sm" icon="eye" />
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
                                    <flux:button wire:click="takeSurveyAnswer({{ $student->identity_number }})"
                                        variant="ghost" size="sm" icon="clipboard-document-check" />
                                    <flux:button wire:click="delete({{ $student->identity_number }})"
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
    <flux:modal name="feedback-modal" class="md:w-lg">
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
