<div class="flex flex-col gap-6">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Survey Management') }}</flux:heading>
            <flux:subheading>{{ __('Create and manage survey definitions and their target audience.') }}</flux:subheading>
        </div>
        <div class="flex w-full sm:w-auto">
            <span title="{{ __('Define a new survey with target audience and sections') }}" class="w-full sm:w-auto">
                <flux:button wire:click="openModal()" variant="primary" icon="plus" class="w-full">
                    {{ __('Create New Survey') }}
                </flux:button>
            </span>
        </div>
    </div>

    <x-auth-session-status class="text-center" :status="session('message')" />
    
    @if (session()->has('error'))
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 p-4 rounded-lg text-center">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 flex flex-col md:flex-row gap-4 md:items-center">
            <div class="flex-1 w-full">
                <flux:input wire:model.live="search" :placeholder="__('Search by survey name...')"
                    icon="magnifying-glass" class="w-full" />
            </div>
            <div class="flex items-center gap-2 w-full md:w-auto shrink-0 justify-end">
                @if ($search)
                    <span title="{{ __('Clear search') }}">
                        <flux:button wire:click="$set('search', '')" variant="ghost" size="sm" icon="x-mark">
                            {{ __('Clear') }}
                        </flux:button>
                    </span>
                @endif
                <div wire:loading wire:target="search" class="shrink-0">
                    <flux:icon name="arrow-path" class="size-4 animate-spin text-zinc-400" />
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
            <div class="flex items-center justify-between">
                <p class="text-sm text-zinc-600 dark:text-zinc-400 py-2">
                    {{ __('Showing') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $surveys->firstItem() }}</span>
                    {{ __('to') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $surveys->lastItem() }}</span>
                    {{ __('of') }}
                    <span class="font-medium text-zinc-900 dark:text-white">{{ $surveys->total() }}</span>
                    {{ __('results') }}
                </p>
            </div>
        </div>

        {{-- Mobile Cards View --}}
        <div class="md:hidden divide-y divide-zinc-200 dark:divide-zinc-700">
            @forelse($surveys->whereNotIn('survey_for_section',[137,138,139,140,141,142,143,144]) as $survey)
                <div class="p-4 space-y-3 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                    <div class="flex justify-between items-start">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ $survey->survey_name }}</span>
                            <span class="text-xs text-zinc-500">{{ $survey->sectionRel->status_name ?? __('N/A') }}</span>
                        </div>
                        <span title="{{ __('Toggle survey availability') }}">
                            <flux:switch wire:click="toggleStatus({{ $survey->id }})" :checked="$survey->is_active" color="green" size="sm" />
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                            {{ $survey->targetRel->status_name ?? __('N/A') }}
                        </span>
                        <span class="text-xs text-zinc-500">{{ __('Semester') }}: {{ $survey->semester }}</span>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-zinc-100 dark:border-zinc-700">
                        <span title="{{ __('Edit survey details') }}">
                            <flux:button wire:click="openModal({{ $survey->id }})" variant="ghost" size="xs" icon="pencil-square" />
                        </span>
                        <span title="{{ __('Manage questions and responses') }}">
                            <flux:button href="{{ route('survey.manage', ['survey_table_id' => $survey->id]) }}" variant="ghost" size="xs" icon="question-mark-circle" class="text-blue-500" />
                        </span>
                        <span title="{{ __('Get public response link') }}">
                            <flux:button href="{{ route('survey.public', ['id' => $survey->id]) }}" target="_blank" variant="ghost" size="xs" icon="link" class="text-green-500" />
                        </span>
                        <span title="{{ __('Delete survey') }}">
                            <flux:button wire:click="delete({{ $survey->id }})" variant="ghost" size="xs" icon="trash" wire:confirm="{{ __('Are you sure?') }}" class="text-red-500" />
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-zinc-500 italic">
                    {{ __('No surveys found. Create your first survey now.') }}
                </div>
            @endforelse
        </div>

        {{-- Desktop Table View --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                <thead class="bg-zinc-50 dark:bg-zinc-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Survey Name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Target Audience') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Section') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Semester') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($surveys->whereNotIn('survey_for_section',[137,138,139,140,141,142,143,144]) as $survey)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-zinc-900 dark:text-white">{{ $survey->survey_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">
                                    {{ $survey->targetRel->status_name ?? __('N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span title="{{ __('Toggle Status') }}">
                                    <flux:switch wire:click="toggleStatus({{ $survey->id }})" :checked="$survey->is_active" color="green" />
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">{{ $survey->sectionRel->status_name ?? __('N/A') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-zinc-600 dark:text-zinc-300">{{ $survey->semester }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <span title="{{ __('Edit') }}">
                                        <flux:button wire:click="openModal({{ $survey->id }})" variant="ghost" size="sm" icon="pencil-square" />
                                    </span>
                                      
                                    <span title="{{ __('Manage Questions') }}">
                                        <flux:button href="{{ route('survey.manage', ['survey_table_id' => $survey->id]) }}" variant="ghost" size="sm" icon="question-mark-circle" class="text-blue-500" />
                                    </span>
                                 
                                    <span title="{{ __('Public Link') }}">
                                        <flux:button href="{{ route('survey.public', ['id' => $survey->id]) }}" target="_blank" variant="ghost" size="sm" icon="link" class="text-green-500" />
                                    </span>

                                    <span title="{{ __('Delete') }}">
                                        <flux:button wire:click="delete({{ $survey->id }})" variant="ghost" size="sm" icon="trash" wire:confirm="{{ __('Are you sure?') }}" class="text-red-500 hover:text-red-600" />
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-zinc-500">
                                {{ __('No surveys found. Create your first survey now.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-zinc-100 dark:border-zinc-700">
            {{ $surveys->links() }}
        </div>
    </div>

    {{-- Modal for Create/Edit --}}
    <flux:modal wire:model="showModal" class="md:w-[800px]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ $survey_id ? __('Edit Survey') : __('Create New Survey') }}</flux:heading>
                <flux:subheading>{{ __('Fill in the survey details below.') }}</flux:subheading>
            </div>

            <form wire:submit="save" class="space-y-4">
                <flux:input label="{{ __('Survey Name') }}" wire:model="survey_name" placeholder="{{ __('e.g. Parent Satisfaction 2024') }}" />
                
                <flux:select label="{{ __('Target Audience') }}" wire:model="survey_target">
                    <option value="">{{ __('Select Target...') }}</option>
                    @foreach($targets as $target)
                        <option value="{{ $target->id }}">{{ $target->status_name }}</option>
                    @endforeach
                </flux:select>

                <flux:select label="{{ __('Belongs to Section') }}" wire:model="survey_for_section">
                    <option value="">{{ __('Select Section...') }}</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->status_name }}</option>
                    @endforeach
                </flux:select>

                <flux:input type="number" label="{{ __('Semester') }}" wire:model="semester" />

                <div 
                    x-data="{ value: @entangle('conditions') }"
                    x-init="
                        let interval = setInterval(() => {
                            if (window.jQuery && window.jQuery().summernote) {
                                clearInterval(interval);
                                $(function() {
                                    $($refs.summernote).summernote({
                                        height: 120,
                                        toolbar: [
                                            ['style', ['bold', 'italic', 'underline', 'clear']],
                                            ['color', ['color']],
                                            ['para', ['ul', 'ol', 'paragraph']],
                                        ],
                                        callbacks: {
                                            onChange: function(contents) {
                                                value = contents;
                                            }
                                        }
                                    });
                                    $watch('value', function(newValue) {
                                        if (newValue !== $($refs.summernote).summernote('code')) {
                                            $($refs.summernote).summernote('code', newValue || '');
                                        }
                                    });
                                });
                            }
                        }, 100);
                    "
                    wire:ignore
                >
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">{{ __('Survey Conditions & Instructions') }}</label>
                    <textarea x-ref="summernote"></textarea>
                </div>

                <div 
                    x-data="{ value: @entangle('notes') }"
                    x-init="
                        let intervalNotes = setInterval(() => {
                            if (window.jQuery && window.jQuery().summernote) {
                                clearInterval(intervalNotes);
                                $(function() {
                                    $($refs.summernoteNotes).summernote({
                                        height: 120,
                                        toolbar: [
                                            ['style', ['bold', 'italic', 'underline', 'clear']],
                                            ['color', ['color']],
                                            ['para', ['ul', 'ol', 'paragraph']],
                                        ],
                                        callbacks: {
                                            onChange: function(contents) {
                                                value = contents;
                                            }
                                        }
                                    });
                                    $watch('value', function(newValue) {
                                        if (newValue !== $($refs.summernoteNotes).summernote('code')) {
                                            $($refs.summernoteNotes).summernote('code', newValue || '');
                                        }
                                    });
                                });
                            }
                        }, 100);
                    "
                    wire:ignore
                >
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">{{ __('Important Notes') }}</label>
                    <textarea x-ref="summernoteNotes"></textarea>
                </div>

                <div class="flex items-center gap-3">
                    <flux:switch wire:model="is_active" color="green" />
                    <flux:label>{{ __('Survey Active') }}</flux:label>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <flux:button wire:click="$set('showModal', false)" variant="ghost">{{ __('Cancel') }}</flux:button>
                    <flux:button type="submit" variant="primary">{{ __('Save Survey') }}</flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
        <style>
            .note-editor .note-editing-area .note-editable {
                background: white;
                color: black;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    @endpush
</div>
