<div class="flex flex-col gap-6" x-data="{
    expanded: {},
    toggleKeys: {{ json_encode($toggleKeys) }},
    toggle(id) {
        this.expanded[id] = !this.expanded[id];
    },
    expandAll() {
        this.toggleKeys.forEach(k => {
            this.expanded[k] = true;
        });
    },
    collapseAll() {
        this.expanded = {};
    }
}" x-on:scroll-to-top.window="window.scrollTo({ top: 0, behavior: 'smooth' })">

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Integrated Survey Structure') }}</flux:heading>
            <flux:subheading>{{ __('A hierarchical tree view of surveys, questions, grading scales, and comparison scales.') }}</flux:subheading>
        </div>
        <div class="flex gap-2">
            <flux:button wire:click="downloadPdf" wire:loading.attr="disabled" variant="primary" icon="document-arrow-down" size="sm">
                {{ __('Export PDF') }}
            </flux:button>
            <flux:button x-on:click="expandAll()" variant="subtle" icon="arrows-pointing-out" size="sm">
                {{ __('Expand All') }}
            </flux:button>
            <flux:button x-on:click="collapseAll()" variant="subtle" icon="arrows-pointing-in" size="sm">
                {{ __('Collapse All') }}
            </flux:button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm p-5 space-y-4">
        <div class="flex items-center gap-2 border-b border-zinc-100 dark:border-zinc-700 pb-2">
            <flux:icon name="funnel" class="size-5 text-zinc-500" />
            <flux:heading size="lg">{{ __('Filter & Search Data') }}</flux:heading>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <flux:input type="text" 
                            label="{{ __('Search keyword') }}" 
                            placeholder="{{ __('Survey name, question text...') }}" 
                            wire:model.live.debounce.300ms="search" 
                            icon="magnifying-glass" />
            </div>

            <!-- Section Filter -->
            <div>
                <flux:select label="{{ __('Section / Department') }}" wire:model.live="selectedSection">
                    <option value="">{{ __('All Sections') }}</option>
                    @foreach($sections as $sec)
                        <option value="{{ $sec->id }}">{{ $sec->status_name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Target Filter -->
            <div>
                <flux:select label="{{ __('Target Group') }}" wire:model.live="selectedTarget">
                    <option value="">{{ __('All Targets') }}</option>
                    @foreach($targets as $tar)
                        <option value="{{ $tar->id }}">{{ $tar->status_name }}</option>
                    @endforeach
                </flux:select>
            </div>

            <!-- Batch Filter -->
            <div>
                <flux:select label="{{ __('Batch / Group') }}" wire:model.live="selectedBatch">
                    <option value="">{{ __('All Batches') }}</option>
                    @foreach($batches as $bNo)
                        <option value="{{ $bNo }}">{{ __('Batch') }} {{ $bNo }}</option>
                    @endforeach
                </flux:select>
            </div>
        </div>

        @if($search || $selectedSection || $selectedTarget || $selectedBatch)
            <div class="flex justify-end pt-2">
                <flux:button wire:click="resetFilters" variant="subtle" icon="x-mark" size="sm" class="text-red-500 hover:text-red-600">
                    {{ __('Clear Filters') }}
                </flux:button>
            </div>
        @endif
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="flex justify-center py-6">
        <flux:icon name="arrow-path" class="size-8 animate-spin text-blue-500" />
    </div>

    <!-- Tree Structure -->
    <div wire:loading.class="opacity-50" class="space-y-4">
        @forelse($surveyTree as $surveyItem)
            @php
                $survey = $surveyItem['record'];
                $surveyId = 'survey_' . $survey->id;
            @endphp
            <!-- Survey Node -->
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-xs overflow-hidden transition-all duration-300 hover:shadow-md">
                <!-- Survey Header -->
                <div class="p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 cursor-pointer select-none bg-zinc-50/50 dark:bg-zinc-800/40 hover:bg-zinc-100/50 dark:hover:bg-zinc-700/40 transition-colors"
                     x-on:click="toggle('{{ $surveyId }}')">
                    
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-blue-50 dark:bg-blue-950/50 rounded-lg text-blue-600 dark:text-blue-400 shrink-0">
                            <flux:icon name="clipboard-document-list" class="size-6" />
                        </div>
                        <div class="space-y-1">
                            <flux:heading level="3" class="text-zinc-950 dark:text-white font-semibold text-base">
                                {{ $survey->survey_name }}
                            </flux:heading>
                            <div class="flex flex-wrap gap-2 text-xs">
                                @if($survey->sectionRel)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 font-medium">
                                        {{ __('Section') }}: {{ $survey->sectionRel->status_name }}
                                    </span>
                                @endif
                                @if($survey->targetRel)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 font-medium">
                                        {{ __('Target') }}: {{ $survey->targetRel->status_name }}
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                                    {{ __('Age') }}: {{ $survey->from_age }} - {{ $survey->to_age }}
                                </span>
                                @if($survey->semester)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300">
                                        {{ __('Semester') }}: {{ $survey->semester }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 self-start md:self-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $survey->is_active ? 'bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-500' }}">
                            {{ $survey->is_active ? __('Active') : __('Inactive') }}
                        </span>
                        <flux:icon name="chevron-down" class="size-5 text-zinc-400 transition-transform duration-300" x-bind:class="expanded['{{ $surveyId }}'] ? 'rotate-180' : ''" />
                    </div>
                </div>

                <!-- Survey Content (Batches / Groups) -->
                <div x-show="expanded['{{ $surveyId }}']" x-collapse>
                    <div class="p-5 border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900/30">
                        @if(empty($surveyItem['batches']))
                            <div class="text-zinc-500 italic text-sm text-center py-6">
                                {{ __('No questions or batches match the search criteria in this survey.') }}
                            </div>
                        @else
                            <!-- Vertical connector line on the left side for LTR -->
                            <div class="relative ml-4 pl-6 border-l-2 border-dashed border-zinc-200 dark:border-zinc-700 space-y-6">
                                @foreach($surveyItem['batches'] as $batchItem)
                                    @php
                                        $batchNo = $batchItem['batch_no'];
                                        $batchId = 'batch_' . $survey->id . '_' . $batchNo;
                                    @endphp
                                    
                                    <!-- Batch Node -->
                                    <div class="relative">
                                        <!-- Connect point indicator on the left -->
                                        <div class="absolute left-[-31px] top-[14px] size-3.5 rounded-full border-2 border-white dark:border-zinc-950 bg-blue-500 shadow-xs"></div>
                                        
                                        <!-- Batch Header -->
                                        <div class="flex items-center gap-2.5 p-2 px-3 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200/80 dark:border-zinc-700/80 rounded-lg cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700 w-fit select-none transition-colors"
                                             x-on:click="toggle('{{ $batchId }}')">
                                            <flux:icon name="users" class="size-4.5 text-blue-500" />
                                            <span class="font-semibold text-sm text-zinc-800 dark:text-zinc-200">
                                                {{ $batchItem['batch_name'] }}
                                            </span>
                                            <flux:icon name="chevron-down" class="size-4 text-zinc-400 transition-transform duration-200" x-bind:class="expanded['{{ $batchId }}'] ? 'rotate-180' : ''" />
                                        </div>

                                        <!-- Batch Content (Scales and Questions) -->
                                        <div x-show="expanded['{{ $batchId }}']" x-collapse class="mt-4 ml-4 pl-6 border-l-2 border-dotted border-zinc-200 dark:border-zinc-700 space-y-6">
                                            
                                            <!-- Sub-section: Grading Scales (If present) -->
                                            @if(!empty($batchItem['grading_scales']))
                                                @php
                                                    $scalesToggleId = 'scales_' . $survey->id . '_' . $batchNo;
                                                @endphp
                                                <div class="relative bg-zinc-50/50 dark:bg-zinc-800/10 border border-zinc-200/40 dark:border-zinc-700/30 rounded-xl p-5 md:p-6">
                                                    <!-- Connect point indicator -->
                                                    <div class="absolute left-[-31px] top-[24px] size-2.5 rounded-full border border-white dark:border-zinc-950 bg-amber-500"></div>
                                                    
                                                    <div class="flex items-center justify-between cursor-pointer select-none hover:opacity-85 transition-opacity" x-on:click="toggle('{{ $scalesToggleId }}')">
                                                        <div class="flex items-center gap-2">
                                                            <flux:icon name="academic-cap" class="size-5 text-amber-500" />
                                                            <span class="font-semibold text-sm text-zinc-800 dark:text-zinc-200">
                                                                {{ __('Evaluation Levels & Grading Scales') }} ({{ count($batchItem['grading_scales']) }})
                                                            </span>
                                                        </div>
                                                        <flux:icon name="chevron-down" class="size-4 text-zinc-400 transition-transform duration-200" x-bind:class="expanded['{{ $scalesToggleId }}'] ? 'rotate-180' : ''" />
                                                    </div>

                                                    <div x-show="expanded['{{ $scalesToggleId }}']" x-collapse class="mt-5 pt-5 border-t border-zinc-200/50 dark:border-zinc-700/50 space-y-6">
                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                                                            @foreach($batchItem['grading_scales'] as $scaleItem)
                                                                @php
                                                                    $scale = $scaleItem['record'];
                                                                    $evalClasses = match($scale->evaluation) {
                                                                        'جيد جداً', 'جيد جدا' => 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-300 border-emerald-100 dark:border-emerald-900',
                                                                        'جيد' => 'bg-sky-50 dark:bg-sky-950/20 text-sky-700 dark:text-sky-300 border-sky-100 dark:border-sky-900',
                                                                        'هشاشة', 'هشاشة نفسية واضحة', 'ضعيف' => 'bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-300 border-rose-100 dark:border-rose-900',
                                                                        default => 'bg-zinc-50 dark:bg-zinc-950/20 text-zinc-700 dark:text-zinc-300 border-zinc-100 dark:border-zinc-900'
                                                                    };
                                                                @endphp
                                                                
                                                                <div class="p-5 bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 rounded-lg space-y-4 shadow-2xs hover:shadow-xs transition-shadow">
                                                                    <div class="flex items-center justify-between gap-2">
                                                                        <span class="text-xs font-mono font-extrabold text-zinc-500 dark:text-zinc-400 bg-zinc-50 dark:bg-zinc-800/80 px-2 py-0.5 rounded border border-zinc-200/40 dark:border-zinc-700/40">
                                                                            {{ $scale->from_percentage }}% - {{ $scale->to_percentage }}%
                                                                        </span>
                                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $evalClasses }} border">
                                                                            {{ $scale->evaluation }}
                                                                        </span>
                                                                    </div>

                                                                    @if($scale->description)
                                                                        <div class="text-xs text-zinc-600 dark:text-zinc-300 italic bg-zinc-50 dark:bg-zinc-800/40 p-3 rounded-lg border border-zinc-200/20 dark:border-zinc-700/20">
                                                                            <span class="text-zinc-400 font-normal block not-italic text-[9px] uppercase tracking-wider mb-0.5">{{ __('Scale Description') }}:</span>
                                                                            {{ $scale->description }}
                                                                        </div>
                                                                    @endif

                                                                    <div class="flex flex-wrap gap-x-3 gap-y-1.5 text-[11px] text-zinc-500 pt-0.5">
                                                                        @if($scale->typeRel)
                                                                            <div>
                                                                                <span class="text-zinc-400">{{ __('Scale Type') }}:</span>
                                                                                <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $scale->typeRel->status_name }}</span>
                                                                            </div>
                                                                        @endif
                                                                        @if($scale->batch_no)
                                                                            <div>
                                                                                <span class="text-zinc-400">{{ __('Batch') }}:</span>
                                                                                <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $scale->batch_no }}</span>
                                                                            </div>
                                                                        @endif
                                                                    </div>

                                                                    <!-- Descriptions (Domain Specific) -->
                                                                    @if(!empty($scaleItem['descriptions']))
                                                                        <div class="pt-3.5 border-t border-zinc-100 dark:border-zinc-800/60 space-y-4">
                                                                            @foreach($scaleItem['descriptions'] as $descItem)
                                                                                @php
                                                                                    $desc = $descItem['record'];
                                                                                    
                                                                                    $procClasses = match($desc->need_processing) {
                                                                                        'تدخل مكثف', 'تدخل' => 'bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-300 border-rose-100 dark:border-rose-900',
                                                                                        'دعم محدود' => 'bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-300 border-amber-100 dark:border-amber-900',
                                                                                        'استمرار وتعزيز', 'تعزيز' => 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-300 border-emerald-100 dark:border-emerald-900',
                                                                                        default => 'bg-indigo-50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-300 border-indigo-100 dark:border-indigo-900'
                                                                                    };
                                                                                @endphp
                                                                                
                                                                                <div class="bg-zinc-50 dark:bg-zinc-800/30 p-3.5 rounded-lg border border-zinc-100 dark:border-zinc-800/50 space-y-2.5">
                                                                                    @if($desc->domainRel)
                                                                                        <div class="text-[9px] uppercase tracking-wider font-extrabold text-purple-600 dark:text-purple-400 mb-1 flex items-center gap-1">
                                                                                            {{ __('Domain') }}: {{ $desc->domainRel->status_name }}
                                                                                        </div>
                                                                                    @endif
                                                                                    <div class="text-xs text-zinc-700 dark:text-zinc-300 font-medium leading-relaxed">
                                                                                        {{ $desc->description }}
                                                                                    </div>
                                                                                    @if($desc->need_processing)
                                                                                        <div class="flex items-center gap-1.5 text-[10px]">
                                                                                            <span class="text-zinc-400 font-medium">{{ __('Required Action') }}:</span>
                                                                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded font-semibold border {{ $procClasses }}">
                                                                                                {{ $desc->need_processing }}
                                                                                            </span>
                                                                                        </div>
                                                                                    @endif

                                                                                    <!-- Comparison Scales nested -->
                                                                                    @if(!empty($descItem['comparisons']))
                                                                                        <div class="mt-3.5 pt-3 border-t border-zinc-200/50 dark:border-zinc-700/50 space-y-3">
                                                                                            <div class="text-[9px] uppercase tracking-wider font-extrabold text-zinc-400 flex items-center gap-1 select-none">
                                                                                                <flux:icon name="arrows-right-left" class="size-3 text-zinc-400" />
                                                                                                {{ __('Comparison Scale for Results & Reports') }}
                                                                                            </div>
                                                                                            
                                                                                            @foreach($descItem['comparisons'] as $comp)
                                                                                                @php
                                                                                                    $compColor = $comp['color'] ?? '#71717a';
                                                                                                @endphp
                                                                                                <div class="flex items-center gap-2 p-2 px-3 bg-white dark:bg-zinc-900 border border-zinc-200/50 dark:border-zinc-800/60 rounded-md text-[11px] hover:bg-zinc-50 dark:hover:bg-zinc-800/80 transition-colors">
                                                                                                    <span class="size-2 rounded-full shrink-0 shadow-xs" style="background-color: {{ $compColor }}"></span>
                                                                                                    <span class="font-semibold text-zinc-800 dark:text-zinc-200">
                                                                                                        {{ $comp['evaluation'] }}
                                                                                                    </span>
                                                                                                    @if(!empty($comp['description']))
                                                                                                        <span class="text-[10px] text-zinc-400 font-normal">
                                                                                                            ({{ $comp['description'] }})
                                                                                                        </span>
                                                                                                    @endif
                                                                                                </div>
                                                                                            @endforeach
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Sub-section: Questions list -->
                                            <div class="space-y-4">
                                                <div class="text-xs font-bold text-zinc-400 flex items-center gap-1.5 select-none pl-1">
                                                    <flux:icon name="question-mark-circle" class="size-4.5 text-teal-500" />
                                                    {{ __('Questions List') }} ({{ count($batchItem['questions']) }})
                                                </div>
                                                
                                                @foreach($batchItem['questions'] as $qItem)
                                                    @php
                                                        $question = $qItem['record'];
                                                    @endphp
                                                    <!-- Question Node -->
                                                    <div class="relative bg-zinc-50/30 dark:bg-zinc-800/10 border border-zinc-200/60 dark:border-zinc-700/40 rounded-xl p-4 transition-all hover:bg-zinc-50/80 dark:hover:bg-zinc-800/20">
                                                        <!-- Connect point indicator on the left -->
                                                        <div class="absolute left-[-31px] top-[24px] size-2.5 rounded-full border border-white dark:border-zinc-950 bg-teal-500"></div>

                                                        <div class="space-y-2">
                                                            <!-- Question Title & Order -->
                                                            <div class="flex items-start gap-2.5">
                                                                <div class="space-y-0.5">
                                                                    <div class="font-medium text-sm text-zinc-900 dark:text-zinc-100">
                                                                        <span class="text-zinc-400 dark:text-zinc-500 mr-1.5 font-mono">#{{ $question->question_order }}</span>
                                                                        {{ $question->question_ar_text }}
                                                                    </div>
                                                                    @if($question->question_en_text)
                                                                        <div class="text-xs text-zinc-400 dark:text-zinc-500 italic">
                                                                            {{ $question->question_en_text }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <!-- Question Info Badges -->
                                                            <div class="flex flex-wrap gap-2 pt-1 text-[11px]">
                                                                @if($question->domainRel)
                                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-purple-50 dark:bg-purple-950/30 text-purple-700 dark:text-purple-300 border border-purple-100 dark:border-purple-900">
                                                                        {{ __('Assessment Domain') }}: {{ $question->domainRel->status_name }}
                                                                    </span>
                                                                @endif
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">
                                                                    {{ __('Type') }}: {{ $question->answer_input_type == 2 ? __('Multiple Choice') : __('Short Text') }}
                                                                </span>
                                                                @if($question->min_score !== null || $question->max_score !== null)
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-300 border border-amber-100 dark:border-amber-900">
                                                                        {{ __('Score') }}: {{ $question->min_score ?? 0 }} - {{ $question->max_score ?? 0 }}
                                                                    </span>
                                                                @endif
                                                            </div>

                                                            <!-- MCQ Options if input type is choice -->
                                                            @if($question->answer_input_type == 2 && is_array($question->answer_options))
                                                                <div class="flex flex-wrap items-center gap-1.5 pt-2">
                                                                    <span class="text-[11px] text-zinc-400">{{ __('Options') }}:</span>
                                                                    @foreach($question->answer_options as $option)
                                                                        <span class="inline-flex items-center px-2 py-0.5 rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-medium">
                                                                            {{ $option['label'] ?? '' }}
                                                                            <span class="ml-1 text-[10px] text-zinc-400 font-mono">({{ $option['value'] ?? '' }})</span>
                                                                        </span>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl p-8 text-center text-zinc-500 dark:text-zinc-400 italic">
                {{ __('No surveys found matching the search filters.') }}
            </div>
        @endforelse
    </div>

</div>
