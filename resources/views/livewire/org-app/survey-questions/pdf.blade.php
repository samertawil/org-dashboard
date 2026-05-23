@php
    use App\Helpers\Arabic;
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ Arabic::reshape(__('Integrated Survey Structure')) }}</title>
    <style>
        @font-face {
            font-family: 'Amiri';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/Amiri-Regular.ttf') }}") format('truetype');
        }

        @font-face {
            font-family: 'Amiri';
            font-style: normal;
            font-weight: bold;
            src: url("{{ public_path('fonts/Amiri-Bold.ttf') }}") format('truetype');
        }

        /* Page configuration for DomPDF */
        @page {
            margin: 90px 45px 70px 45px;
        }

        body {
            font-family: 'Amiri', 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            color: #334155;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        /* Fixed Header on every page */
        .header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 50px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 6px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 0;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .header-title {
            font-size: 15px;
            font-weight: bold;
            color: #1e3a8a;
            text-align: right;
        }

        .header-sub {
            font-size: 9px;
            color: #64748b;
            text-align: left;
        }

        /* Fixed Footer on every page */
        .footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 35px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }

        .page-number:after {
            content: " " counter(page) " / " counter(pages);
        }

        /* Survey Card Layout */
        .survey-node {
            border: 1px solid #e2e8f0;
            border-right: 4px solid #3b82f6;
            border-radius: 8px;
            background-color: #ffffff;
            margin-bottom: 25px;
            padding: 15px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        }

        .survey-header {
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .survey-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e3a8a;
        }

        .survey-meta {
            margin-top: 4px;
        }

        .meta-badge {
            color: #1e40af;
            font-size: 8.5px;
            font-weight: bold;
            margin-left: 10px;
            display: inline-block;
        }

        /* Batch Header styling */
        .batch-node {
            margin-bottom: 25px;
        }

        .batch-header {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-right: 3px solid #10b981;
            border-radius: 6px;
            padding: 6px 12px;
            margin-bottom: 12px;
            font-weight: bold;
            font-size: 11.5px;
            color: #0f766e;
        }

        /* General Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background-color: #ffffff;
        }

        tr {
            page-break-inside: avoid;
        }

        th {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 9.5px;
            color: #475569;
            font-weight: bold;
            text-align: right;
        }

        td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            vertical-align: top;
            font-size: 9.5px;
        }

        .scale-range-badge {
            color: #334155;
            font-weight: bold;
            display: inline-block;
        }

        /* Badges for evaluation level */
        .eval-badge {
            display: inline-block;
            font-size: 9px;
            font-weight: bold;
        }

        .eval-success {
            color: #15803d;
        }

        .eval-info {
            color: #1d4ed8;
        }

        .eval-danger {
            color: #b91c1c;
        }

        .eval-default {
            color: #374151;
        }

        .scale-table-desc-text {
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .desc-box {
            margin-top: 5px;
            padding: 4px 6px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }

        .desc-domain {
            color: #7c3aed;
            font-weight: bold;
            font-size: 8px;
            display: inline-block;
        }

        .desc-text {
            color: #334155;
            font-size: 8.5px;
        }

        .desc-action {
            display: inline-block;
            margin-top: 2px;
            font-size: 7.5px;
            font-weight: bold;
            color: #dc2626;
        }

        /* Section titles */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f766e;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 3px;
        }

        /* Badges for questions */
        .q-badge {
            display: inline-block;
            font-size: 8px;
            margin-left: 4px;
            margin-top: 2px;
            font-weight: bold;
        }

        .q-badge-purple {
            color: #6b21a8;
        }

        .q-badge-gray {
            color: #475569;
        }

        .q-badge-amber {
            color: #92400e;
        }
    </style>
</head>

<body>
    <!-- Repeating Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-title">{{ Arabic::reshape(__('Integrated Survey Structure')) }}</td>
                <td class="header-sub">{{ Arabic::reshape(__('Generated on')) }}: {{ date('Y-m-d H:i') }}</td>
            </tr>
        </table>
    </div>

    <!-- Repeating Footer -->
    <div class="footer">
        {{ Arabic::reshape(config('app.name')) }} - {{ Arabic::reshape(__('Integrated Survey Structure')) }} -
        {{ Arabic::reshape('صفحة') }}<span class="page-number"></span>
    </div>

    @forelse($surveyTree as $surveyItem)
        @php
            $survey = $surveyItem['record'];
        @endphp
        <div class="survey-node">
            <div class="survey-header">
                <div class="survey-title">{{ Arabic::reshape($survey->survey_name) }}</div>
                <div class="survey-meta">
                    @if ($survey->sectionRel)
                        <span class="meta-badge">{{ Arabic::reshape(__('Section')) }}:
                            {{ Arabic::reshape($survey->sectionRel->status_name) }}</span>
                    @endif
                    @if ($survey->targetRel)
                        <span class="meta-badge">{{ Arabic::reshape(__('Target')) }}:
                            {{ Arabic::reshape($survey->targetRel->status_name) }}</span>
                    @endif
                    <span class="meta-badge">{{ Arabic::reshape(__('Age')) }}: {{ $survey->from_age }} -
                        {{ $survey->to_age }}</span>
                    @if ($survey->semester)
                        <span class="meta-badge">{{ Arabic::reshape(__('Semester')) }}: {{ $survey->semester }}</span>
                    @endif
                </div>
            </div>

            @foreach ($surveyItem['batches'] as $batchItem)
                <div class="batch-node">
                    <div class="batch-header">
                        {{ Arabic::reshape($batchItem['batch_name']) }}
                    </div>

                    <!-- Redesigned Evaluation Levels & Grading Scales Table -->
                    @if (!empty($batchItem['grading_scales']))
                        @php
                            $groupedScales = collect($batchItem['grading_scales'])->groupBy(function($item) {
                                return $item['record']->type ?? 0;
                            })->sortBy(function($group, $key) {
                                return $key;
                            });
                        @endphp

                        @foreach ($groupedScales as $typeId => $scales)
                            @php
                                $firstScale = $scales->first()['record'];
                                $typeName = $firstScale->typeRel ? $firstScale->typeRel->status_name : __('Evaluation Levels & Grading Scales');
                                // Sort scales within this group by from_percentage
                                $sortedScales = $scales->sortBy(function($item) {
                                    return (float) $item['record']->from_percentage;
                                });
                            @endphp
                            <div class="section-title">🎓 {{ Arabic::reshape($typeName) }}</div>
                            <table class="scales-table">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">{{ Arabic::reshape(__('Range')) }}</th>
                                        <th style="width: 20%;">{{ Arabic::reshape(__('Evaluation')) }}</th>
                                        <th style="width: 35%;">{{ Arabic::reshape(__('Scale Description')) }}</th>
                                        <th style="width: 30%;">
                                            {{ Arabic::reshape(__('Domain Recommendations & Actions')) }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sortedScales as $scaleItem)
                                        @php
                                            $scale = $scaleItem['record'];
                                            $eval = $scale->evaluation;
                                            $evalClass = 'eval-default';
                                            if (str_contains($eval, 'جيد جدا') || str_contains($eval, 'ممتاز')) {
                                                $evalClass = 'eval-success';
                                            } elseif (str_contains($eval, 'جيد')) {
                                                $evalClass = 'eval-info';
                                            } elseif (str_contains($eval, 'ضعيف') || str_contains($eval, 'هشاش')) {
                                                $evalClass = 'eval-danger';
                                            }
                                        @endphp
                                        <tr>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <span class="scale-range-badge">{{ $scale->from_percentage }}% -
                                                    {{ $scale->to_percentage }}%</span>
                                            </td>
                                            <td style="text-align: center; vertical-align: middle;">
                                                <span
                                                    class="eval-badge {{ $evalClass }}">{{ Arabic::reshape($scale->evaluation) }}</span>
                                            </td>
                                            <td>
                                                @if ($scale->description)
                                                    <div class="scale-table-desc-text">
                                                        {{ Arabic::reshape($scale->description) }}</div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if (!empty($scaleItem['descriptions']))
                                                    @foreach ($scaleItem['descriptions'] as $descItem)
                                                        @php
                                                            $desc = $descItem['record'];
                                                        @endphp
                                                        <div
                                                            style="margin-bottom: 6px; padding-bottom: 6px; border-bottom: 1px dashed #e2e8f0;">
                                                            @if ($desc->domainRel)
                                                                <div class="desc-domain">
                                                                    [{{ Arabic::reshape($desc->domainRel->status_name) }}]
                                                                </div>
                                                            @endif
                                                            <span
                                                                class="desc-text">{{ Arabic::reshape($desc->description) }}</span>
                                                            @if ($desc->need_processing)
                                                                <div class="desc-action">
                                                                    {{ Arabic::reshape($desc->need_processing) }}</div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    @endif

                    <!-- Redesigned Questions Table -->
                    @if (!empty($batchItem['questions']))
                        <div class="section-title">❓ {{ Arabic::reshape(__('Questions List')) }}</div>
                        <table class="questions-table">
                            <thead>
                                <tr>
                                    <th style="width: 8%; text-align: center;">#</th>
                                    <th style="width: 50%;">{{ Arabic::reshape(__('Question')) }}</th>
                                    <th style="width: 22%;">{{ Arabic::reshape(__('Assessment Domain')) }}</th>
                                    <th style="width: 20%;">{{ Arabic::reshape(__('Type & Score')) }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($batchItem['questions'] as $qItem)
                                    @php
                                        $question = $qItem['record'];
                                    @endphp
                                    <tr>
                                        <td
                                            style="text-align: center; vertical-align: middle; font-weight: bold; font-family: monospace;">
                                            #{{ $question->question_order }}</td>
                                        <td>
                                            <div style="font-weight: bold; color: #1e293b;">
                                                {{ Arabic::reshape($question->question_ar_text) }}</div>
                                            @if ($question->question_en_text)
                                                <div
                                                    style="font-size: 8.5px; color: #64748b; font-style: italic; margin-top: 2px;">
                                                    {{ $question->question_en_text }}</div>
                                            @endif
                                            @if ($question->answer_input_type == 2 && is_array($question->answer_options))
                                                <div style="font-size: 8.5px; margin-top: 5px; color: #475569;">
                                                    <strong>{{ Arabic::reshape(__('Options')) }}:</strong>
                                                    @foreach ($question->answer_options as $option)
                                                        <span
                                                            style="font-size: 8px; margin-left: 8px; display: inline-block;">
                                                            {{ Arabic::reshape($option['label'] ?? '') }}
                                                            ({{ $option['value'] ?? '' }})
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($question->domainRel)
                                                <span
                                                    class="q-badge q-badge-purple">{{ Arabic::reshape($question->domainRel->status_name) }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <span
                                                class="q-badge q-badge-gray">{{ $question->answer_input_type == 2 ? Arabic::reshape(__('Multiple Choice')) : Arabic::reshape(__('Short Text')) }}</span>
                                            @if ($question->min_score !== null || $question->max_score !== null)
                                                <div
                                                    style="font-size: 8.5px; color: #92400e; margin-top: 3px; font-weight: bold;">
                                                    {{ Arabic::reshape(__('Score')) }}:
                                                    {{ $question->min_score ?? 0 }} - {{ $question->max_score ?? 0 }}
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endforeach
        </div>
    @empty
        <div style="text-align: center; color: #666; font-style: italic; margin-top: 50px;">
            {{ Arabic::reshape(__('No surveys found matching the search filters.')) }}
        </div>
    @endforelse
</body>

</html>
