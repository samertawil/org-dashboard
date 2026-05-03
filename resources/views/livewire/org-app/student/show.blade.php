<div class="flex flex-col gap-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <flux:heading level="1" size="xl">{{ __('Student Details') }}
            </flux:heading>
            <flux:subheading class="hidden sm:block">{{ __('View comprehensive information and survey answers for this student.') }}
            </flux:subheading>
        </div>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <span title="{{ __('Go back to student list') }}">
                <flux:button href="{{ route('student.index') }}" wire:navigate variant="ghost" icon="arrow-left"
                    class="flex-1 sm:flex-none print:hidden">
                    {{ __('Back') }}
                </flux:button>
            </span>
            <span title="{{ __('View grading scale results') }}">
                <flux:button wire:click="$set('showGradingScale', true)" variant="ghost" icon="chart-bar" class="flex-1 sm:flex-none print:hidden">
                    {{ __('Scale') }}
                </flux:button>
            </span>
            <span title="{{ __('Print student report') }}">
                <flux:button onclick="printWithDynamicName()" variant="ghost" icon="printer" class="flex-1 sm:flex-none print:hidden">
                    {{ __('Print') }}
                </flux:button>
            </span>
            @can('student.create')
                <span title="{{ __('Edit student details') }}">
                    <flux:button href="{{ route('student.edit', $student) }}" wire:navigate variant="primary" icon="pencil"
                        class="flex-1 sm:flex-none print:hidden">
                        {{ __('Edit') }}
                    </flux:button>
                </span>
            @endcan
        </div>
    </div>


    <script>
        function printWithDynamicName() {
            const originalTitle = document.title;
            document.title = "{{ $student->full_name }}";
            window.print();
            setTimeout(() => {
                document.title = originalTitle;
            }, 100);
        }
    </script>

    <style>
        @media print {
            body {
                background-color: white !important;
                color: black !important;
            }

            .print\:hidden {
                display: none !important;
            }

            .bg-zinc-50,
            .bg-zinc-100,
            .bg-white {
                background-color: transparent !important;
            }

            .dark\:bg-zinc-800,
            .dark\:bg-zinc-900 {
                background-color: transparent !important;
            }

            .border,
            .border-zinc-200,
            .border-zinc-700 {
                border-color: #e5e7eb !important;
            }

            .shadow-sm {
                shadow: none !important;
            }

            .grid {
                display: block !important;
            }

            .md\:grid-cols-3 {
                display: flex !important;
                flex-direction: column !important;
            }

            .md\:col-span-1,
            .md\:col-span-2 {
                width: 100% !important;
                margin-bottom: 2rem !important;
            }

            .p-6 {
                padding: 1.5rem !important;
            }

            h1,
            h2,
            h3,
            h4 {
                color: black !important;
            }

            .text-zinc-500,
            .text-zinc-400 {
                color: #6b7280 !important;
            }

            @page {
                margin: 2cm;
            }
        }
    </style>

    <style>
        @media print {
            body {
                background-color: white !important;
                color: black !important;
                font-size: 0.85rem !important;
            }

            .print\:hidden {
                display: none !important;
            }

            .bg-zinc-50,
            .bg-zinc-100,
            .bg-white,
            .dark\:bg-zinc-800,
            .dark\:bg-zinc-900 {
                background-color: transparent !important;
            }

            .border,
            .border-zinc-200,
            .border-zinc-700 {
                border-color: #d1d5db !important;
            }

            .shadow-sm {
                box-shadow: none !important;
            }

            .grid {
                display: grid !important;
                /* Keep grid for layout control */
            }

            .md\:grid-cols-3 {
                display: block !important;
            }

            .md\:col-span-1,
            .md\:col-span-2 {
                width: 100% !important;
                margin-bottom: 0.75rem !important;
            }

            .p-6,
            .p-4 {
                padding: 0.4rem !important;
            }

            .m-6,
            .mb-6 {
                margin-bottom: 0.25rem !important;
            }

            h1 {
                font-size: 1.1rem !important;
            }

            h2 {
                font-size: 1rem !important;
            }

            h3 {
                font-size: 0.95rem !important;
            }

            .h-16.w-16 {
                display: none !important;
            }

            @page {
                margin: 0.5cm;
            }

            .survey-table th,
            .survey-table td {
                padding: 2px 6px !important;
                border: 1px solid #d1d5db !important;
            }

            .survey-table {
                border-collapse: collapse !important;
                width: 100% !important;
            }

            /* Ensure info pairs are on the same line if possible */
            .info-pair {
                display: flex !important;
                gap: 0.5rem !important;
                align-items: baseline !important;
            }
        }
    </style>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Student Information Card --}}
        <div class="md:col-span-1 flex flex-col gap-6">
            <div
                class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden p-6 print:p-2">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 mb-6 print:mb-2 text-center sm:text-start">
                    <div
                        class="h-16 w-16 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 text-2xl font-bold shrink-0 print:hidden">
                        {{ strtoupper(substr($student->full_name, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-zinc-900 dark:text-white print:text-lg">
                            {{ $student->full_name }}</h2>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $student->identity_number }}</p>
                    </div>
                </div>

                <div class="space-y-3 print:space-y-1">
                    <div
                        class="space-y-3 grid grid-cols-1 gap-y-4 gap-x-2 text-sm print:grid-cols-3 print:gap-y-1 print:gap-x-4">


                        <div class="info-pair">
                            <span class="text-zinc-500 min-w-fit">{{ __('Group') }}:</span>
                            <span
                                class="font-medium text-zinc-900 dark:text-zinc-100">{{ $student->studentGroup?->name ?? '-' }}</span>
                        </div>

                        <div class="info-pair">
                            <span class="text-zinc-500 min-w-fit">{{ __('Birth Date') }}:</span>
                            <span
                                class="font-medium text-zinc-900 dark:text-zinc-100">{{ $student->birth_date ?? '-' }}</span>
                        </div>

                        <div class="info-pair">
                            <span class="text-zinc-500 min-w-fit">{{ __('Age When Join') }}:</span>
                            <span
                                class="font-medium text-zinc-900 dark:text-zinc-100">{{ $this->studentData->student_age_when_join ?? '-' }}

                                y</span>
                        </div>

                        <div class="info-pair">
                            <span class="text-zinc-500 min-w-fit">{{ __('Gender') }}:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">
                                @php
                                    $genderEnum = \App\Enums\GlobalSystemConstant::tryFrom($student->gender);
                                @endphp
                                {{ $genderEnum ? $genderEnum->label() : '-' }}
                            </span>
                        </div>

                        <div class="info-pair">
                            <span class="text-zinc-500 min-w-fit">{{ __('Status') }}:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">
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
                            </span>
                        </div>

                        <div class="info-pair">
                            <span class="text-zinc-500 min-w-fit">{{ __('Group DB') }}:</span>
                            <span
                                class="font-medium text-zinc-900 dark:text-zinc-100">{{ $student->group->status_name ?? '-' }}</span>
                        </div>

                        <div class="info-pair">
                            <span class="text-zinc-500 min-w-fit">{{ __('Enrollment') }}:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">
                                @if ($student->enrollment_type === 'full_week')
                                    {{ __('Full Week') }}
                                @elseif($student->enrollment_type === 'sat_mon_wed')
                                    {{ __('Sat/Mon/Wed') }}
                                @elseif($student->enrollment_type === 'sun_tue_thu')
                                    {{ __('Sun/Mon/Thu') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>


                    </div>


                    @if ($student->notes)
                        <div class="pt-4 mt-4 border-t border-zinc-200 dark:border-zinc-700 print:pt-1 print:mt-1">
                            <div class="text-sm text-zinc-500 mb-1 print:text-xs font-semibold">{{ __('Notes') }}:
                            </div>
                            <p class="text-sm text-zinc-900 dark:text-zinc-100 print:text-xs">{{ $student->notes }}</p>
                        </div>
                    @endif

                        @foreach ($lateSurveyStudentData as $data)
                        
                        <div class="info-pair flex">
                            <span class="text-zinc-500 min-w-fit">{{ __('Late Survey') }}:</span>
                            <div
                                class="font-medium text-zinc-900 dark:text-zinc-100">{{ $data->section_name ?? $data->survey_for_section }}</div>
                        </div>
                        
                        @endforeach

                </div>
            </div>
        </div>

        {{-- Survey Information Section --}}
        <div class="md:col-span-2">
            <div
                class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 shadow-sm overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 print:px-2 print:py-1">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-white print:text-base">
                        {{ __('اجابات النماذج والتقيمات') }}</h3>
                </div>

                <div class="p-6 space-y-8">
                    {{-- Comparison Section --}}
                    @if(!empty($comparisonResults))
                    <div class="space-y-4">
                        <flux:heading level="3" class="flex items-center gap-2">
                            <flux:icon name="chart-bar" class="size-5 text-indigo-500" />
                            {{ __('نتائج قياس الأثر والتقدم (Pre vs Post)') }}
                        </flux:heading>

                        @foreach($comparisonResults as $pair)
                        <div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                                <thead class="bg-zinc-50 dark:bg-zinc-900/50">
                                    <tr>
                                        <th colspan="5" class="px-4 py-2 text-center text-xs font-bold text-zinc-600 bg-indigo-50/50 dark:bg-indigo-900/20">
                                            {{ $pair->pre_name }} vs {{ $pair->post_name }}
                                        </th>
                                    </tr>
                                    <tr>
                                        <th scope="col" class="px-3 py-2 text-right text-xs font-semibold text-zinc-500 uppercase">{{ __('Domain') }}</th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-semibold text-zinc-500 uppercase">{{ __('Pre') }}</th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-semibold text-zinc-500 uppercase">{{ __('Post') }}</th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-semibold text-zinc-500 uppercase">{{ __('Progress') }}</th>
                                        <th scope="col" class="px-3 py-2 text-center text-xs font-semibold text-zinc-500 uppercase">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
                                    @foreach($pair->domains as $domain)
                                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50">
                                        <td class="px-3 py-2 text-xs font-medium text-zinc-900 dark:text-zinc-100">{{ $domain['name'] }}</td>
                                        <td class="px-3 py-2 text-xs text-center text-zinc-600 tracking-tighter">{{ $domain['pre'] }}</td>
                                        <td class="px-3 py-2 text-xs text-center text-zinc-600 tracking-tighter">{{ $domain['post'] }}</td>
                                        <td class="px-3 py-2 text-xs text-center font-bold">
                                            @if($domain['diff'] !== null)
                                            <span class="{{ $domain['diff'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ $domain['diff'] > 0 ? '+' : '' }}{{ $domain['diff'] }}%
                                            </span>
                                            @else
                                            <span class="text-zinc-400">---</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border" style="background-color: {{ $domain['color'] }}20; color: {{ $domain['color'] }}; border-color: {{ $domain['color'] }}40">
                                                {{ $domain['evaluation'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                @if($pair->total)
                                <tfoot class="bg-zinc-50/50 dark:bg-zinc-900/30">
                                    <tr class="font-bold">
                                        <td class="px-3 py-2 text-xs text-indigo-700 dark:text-indigo-400">{{ __('Total Progress') }}</td>
                                        <td class="px-3 py-2 text-xs text-center">{{ $pair->total['pre'] }}</td>
                                        <td class="px-3 py-2 text-xs text-center">{{ $pair->total['post'] }}</td>
                                        <td class="px-3 py-2 text-xs text-center">
                                            @if($pair->total['diff'] !== null)
                                            <span class="{{ $pair->total['diff'] >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                                                {{ $pair->total['diff'] > 0 ? '+' : '' }}{{ $pair->total['diff'] }}%
                                            </span>
                                            @else
                                            <span class="text-zinc-400">---</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2 text-center">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black" style="color: {{ $pair->total['color'] }}">
                                                {{ $pair->total['evaluation'] }}
                                            </span>
                                        </td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                        @endforeach
                    </div>
                    <flux:separator variant="subtle" />
                    @endif

                    @if ($student->surveyStudentanswers->isEmpty())
                        <div class="p-6 text-center text-zinc-500 dark:text-zinc-400">
                            {{ __('No survey answers recorded for this student.') }}
                        </div>
                    @else
                        @forelse ($student->surveyStudentanswers->unique('survey_no')->values() as $survey)
                            {{-- Mobile Cards View --}}
                            <div class="md:hidden space-y-4 mb-8 print:hidden">
                                <div class="bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700">
                                    <h4 class="text-base font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                                        <flux:icon name="clipboard-document-list" size="sm" />
                                        {{ $survey->surveyfor->status_name }}
                                    </h4>
                                </div>

                                <div class="space-y-3">
                                    @foreach ($student->surveyStudentanswers->where('survey_no', $survey->survey_no) as $answer)
                                        @php
                                            $displayAnswerAr = $answer->answer_ar_text;
                                            if (!empty($answer->answer_ar_text) && !empty($answer->question?->answer_options)) {
                                                $decodedAr = json_decode($answer->answer_ar_text, true);
                                                $valuesAr = json_last_error() === JSON_ERROR_NONE && is_array($decodedAr) ? $decodedAr : [$answer->answer_ar_text];
                                                $labelsAr = [];
                                                foreach ($valuesAr as $val) {
                                                    $found = $val;
                                                    $options = is_string($answer->question->answer_options) ? json_decode($answer->question->answer_options, true) : $answer->question->answer_options;
                                                    if (is_array($options)) {
                                                        foreach ($options as $option) {
                                                            if (is_array($option) && isset($option['value']) && isset($option['label'])) {
                                                                if ((string) $option['value'] === (string) $val) { $found = $option['label']; break; }
                                                            } elseif (is_string($option)) {
                                                                if ((string) $option === (string) $val) { $found = $option; break; }
                                                            }
                                                        }
                                                    }
                                                    $labelsAr[] = $found;
                                                }
                                                $displayAnswerAr = implode('، ', $labelsAr);
                                            } else {
                                                $decodedAr = json_decode($answer->answer_ar_text, true);
                                                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedAr)) {
                                                    $displayAnswerAr = implode('، ', $decodedAr);
                                                }
                                            }
                                        @endphp

                                        <div class="bg-white dark:bg-zinc-800 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm space-y-3">
                                            <div class="flex items-start gap-3">
                                                <span class="shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-700 text-xs font-bold text-zinc-500">
                                                    {{ $answer->question?->question_order }}
                                                </span>
                                                <div class="text-sm font-bold text-zinc-900 dark:text-white leading-relaxed">
                                                    {{ $answer->question?->question_ar_text }}
                                                </div>
                                            </div>
                                            
                                            <div class="bg-emerald-50/50 dark:bg-emerald-900/10 p-3 rounded-lg border border-emerald-100/50 dark:border-emerald-800/30">
                                                <div class="text-[10px] uppercase tracking-wider text-emerald-600 dark:text-emerald-400 font-bold mb-1">{{ __('الإجابة') }}</div>
                                                <div class="text-sm text-zinc-800 dark:text-zinc-200 leading-relaxed">
                                                    {{ $displayAnswerAr ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="p-4 bg-zinc-50 dark:bg-zinc-900/30 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-700 text-[10px] text-zinc-500 space-y-1">
                                    @php
                                        $creators = $student->surveyStudentanswers->where('survey_no', $survey->survey_no)->map(fn($ans) => $ans->creator?->full_name ?? $ans->created_by)->filter()->unique()->implode('، ');
                                        $created = $student->surveyStudentanswers->where('survey_no', $survey->survey_no)->map(fn($ans) => $ans->created_at ? \Carbon\Carbon::parse($ans->created_at)->format('Y-m-d') : null)->filter()->unique()->implode('، ');
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <flux:icon name="user" size="xs" />
                                        <span>{{ __('بواسطة') }}: {{ $creators ?: '-' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <flux:icon name="calendar" size="xs" />
                                        <span dir="ltr">{{ $created }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Desktop Table View --}}
                            <div class="hidden md:block overflow-x-auto mb-8 print:block">
                                <table class="w-full survey-table border-collapse">
                                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 print:bg-white">
                                        <tr>
                                            <th class="px-4 py-2 border text-sm font-semibold text-center">
                                             # </th>
                                            <th class="px-4 py-2 border text-sm font-semibold text-center">
                                                {{ __('أسئلة نموذج') }} - {{ $survey->surveyfor->status_name }} </th>
                                            <th class="px-4 py-2 border text-sm font-semibold text-center">
                                                {{ __('الإجابة') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($student->surveyStudentanswers->where('survey_no', $survey->survey_no) as $answer)
                                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                                                <td class="px-4 py-2 border text-sm">
                                                    <div class="font-medium text-zinc-900 dark:text-zinc-100"
                                                        style="width: 10px;">
                                                        {{ $answer->question?->question_order ?? __('Unknown Question') }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-2 border text-sm">
                                                    <div class="font-medium text-zinc-900 dark:text-zinc-100"
                                                        style="width: 350px;">
                                                        {{ $answer->question?->question_ar_text ?? __('Unknown Question') }}
                                                    </div>
                                                    @if ($answer->question?->question_en_text)
                                                        <div class="text-xs text-zinc-500 print:hidden">
                                                            {{ $answer->question->question_en_text }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 border text-sm text-zinc-700 dark:text-zinc-300"
                                                    style="width: 200px;">
                                                    @php
                                                        $displayAnswerAr = $answer->answer_ar_text;
                                                        if (!empty($answer->answer_ar_text) && !empty($answer->question?->answer_options)) {
                                                            $decodedAr = json_decode($answer->answer_ar_text, true);
                                                            $valuesAr = json_last_error() === JSON_ERROR_NONE && is_array($decodedAr) ? $decodedAr : [$answer->answer_ar_text];
                                                            $labelsAr = [];
                                                            foreach ($valuesAr as $val) {
                                                                $found = $val;
                                                                $options = is_string($answer->question->answer_options) ? json_decode($answer->question->answer_options, true) : $answer->question->answer_options;
                                                                if (is_array($options)) {
                                                                    foreach ($options as $option) {
                                                                        if (is_array($option) && isset($option['value']) && isset($option['label'])) {
                                                                            if ((string) $option['value'] === (string) $val) { $found = $option['label']; break; }
                                                                        } elseif (is_string($option)) {
                                                                            if ((string) $option === (string) $val) { $found = $option; break; }
                                                                        }
                                                                    }
                                                                }
                                                                $labelsAr[] = $found;
                                                            }
                                                            $displayAnswerAr = implode('، ', $labelsAr);
                                                        } else {
                                                            $decodedAr = json_decode($answer->answer_ar_text, true);
                                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedAr)) {
                                                                $displayAnswerAr = implode('، ', $decodedAr);
                                                            }
                                                        }
                                                    @endphp
                                                    {{ $displayAnswerAr ?? '-' }}
                                                    @if ($answer->answer_en_text)
                                                        <div class="text-xs text-zinc-500 mt-1 border-t border-zinc-100 pt-1 print:hidden">
                                                            {{ $answer->answer_en_text }}
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-zinc-50 dark:bg-zinc-900/50 print:bg-white">
                                        <tr>
                                            <td colspan="3" class="px-4 py-2 border text-sm text-zinc-500 dark:text-zinc-400 text-center font-medium">
                                                @php
                                                    $creators = $student->surveyStudentanswers->where('survey_no', $survey->survey_no)->map(fn($ans) => $ans->creator?->full_name ?? $ans->created_by)->filter()->unique()->implode('، ');
                                                    $created = $student->surveyStudentanswers->where('survey_no', $survey->survey_no)->map(fn($ans) => $ans->created_at ? \Carbon\Carbon::parse($ans->created_at)->format('Y-m-d') : null)->filter()->unique()->implode('، ');
                                                @endphp
                                                <div class="flex items-center justify-center gap-6">
                                                    <div class="flex items-center gap-1">
                                                        <flux:icon name="user" class="size-4" />
                                                        <span>{{ __('بواسطة') }}: {{ $creators ?: '-' }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <flux:icon name="calendar" class="size-4" />
                                                        <span dir="ltr">{{ $created }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @empty
                            <div class="text-center text-zinc-500 py-4">{{ __('No surveys found') }}</div>
                        @endforelse
                    @endif

                </div>
            </div>
        </div>
        <flux:modal wire:model="showGradingScale" class="md:w-[800px]">
        @if ($showGradingScale)
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <flux:heading level="2" size="lg">
                        {{ __('Grading Scale Results') }}
                    </flux:heading>
                </div>
@forelse ($this->studentGradingScale->unique('survey_no')->values() as $survey  )
<div>
    <p class="text-center" style="font-weight: 500;">{!!$survey->status_name ?? '-'!!}</p>
</div>
<div class="overflow-x-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
    <table class="w-full table-auto divide-y divide-zinc-200 dark:divide-zinc-700">
        <thead class="bg-zinc-50 dark:bg-zinc-800/50">
            <tr>
                <th class="px-4 py-3 text-start text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Domain') }}</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Total Marks') }}</th>
               
                <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Grade %') }}</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Evaluation') }}</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Description') }}</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-zinc-800 divide-y divide-zinc-200 dark:divide-zinc-700">
            @forelse ($this->studentGradingScale->where('survey_no', $survey->survey_no)  as $grade)
                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                    @php
                        $domain = \App\Models\Status::find($grade->domain_id);
                    @endphp
                    <td @class([
                        'px-4 py-3 text-sm font-medium',
                     
                        'text-red-600 dark:text-red-400' => !$domain,
                    ])>
                        <p>{{ $domain?->status_name ?? __('التقييم الكلي للطفل') }}</p>
                    </td>
                    <td class="px-4 py-3 text-sm text-center text-zinc-600 dark:text-zinc-300">
                        {{ intval($grade->total_marks) }}/{{ $grade->max_total_score }}
                    </td>
                    
                    <td class="px-4 py-3 text-sm text-center font-bold text-indigo-600 dark:text-indigo-400">
                        {{ $grade->grade }}%
                    </td>
                    <td class="px-4 py-3 text-sm text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400">
                            {{ $grade->evaluation }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5  text-xs font-medium ">
                            {{ $grade->description }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-zinc-500 dark:text-zinc-400">
                        {{ __('No grading scale data available for this student.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@empty
    
@endforelse
               

                <div class="flex justify-end">
                    <flux:button wire:click="$set('showGradingScale', false)" variant="ghost">
                        {{ __('Close') }}
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
</div>

</div>
