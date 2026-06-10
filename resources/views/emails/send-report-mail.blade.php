<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f5f7;
            color: #333333;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid #e1e4e8;
        }
        .header {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .metadata-grid {
            display: grid;
            grid-template-cols: 1fr 1fr;
            gap: 15px;
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            border: 1px solid #f3f4f6;
        }
        .metadata-item {
            font-size: 13px;
        }
        .metadata-label {
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 2px;
            display: block;
        }
        .metadata-value {
            color: #111827;
            font-weight: 700;
        }
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #4f46e5;
            border-bottom: 2px solid #e0e7ff;
            padding-bottom: 8px;
            margin-bottom: 15px;
            margin-top: 30px;
        }
        .body-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fafafa;
        }
        .body-card-header {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .body-card-content {
            font-size: 13px;
            line-height: 1.6;
            color: #4b5563;
        }
        .body-card-obs {
            font-size: 12px;
            color: #b45309;
            background-color: #fffbeb;
            border-right: 3px solid #f59e0b;
            padding: 8px;
            margin-top: 10px;
            border-radius: 0 4px 4px 0;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $report->report_name }}</h1>
            <p>{{ __('Consolidated Supervisor Report') }}</p>
        </div>
        <div class="content">
            <div class="metadata-grid">
                <div class="metadata-item">
                    <span class="metadata-label">{{ __('Date') }}:</span>
                    <span class="metadata-value">{{ $report->report_date?->format('Y-m-d') }}</span>
                </div>
                <div class="metadata-item">
                    <span class="metadata-label">{{ __('Created By') }}:</span>
                    <span class="metadata-value">{{ $report->employee?->full_name }}</span>
                </div>
                <div class="metadata-item">
                    <span class="metadata-label">{{ __('Date From') }}:</span>
                    <span class="metadata-value">{{ $report->date_from?->format('Y-m-d') }}</span>
                </div>
                <div class="metadata-item">
                    <span class="metadata-label">{{ __('Date To') }}:</span>
                    <span class="metadata-value">{{ $report->date_to?->format('Y-m-d') }}</span>
                </div>
                @if($report->batch_no)
                    <div class="metadata-item">
                        <span class="metadata-label">{{ __('Batch Number') }}:</span>
                        <span class="metadata-value">{{ $report->batch_no }}</span>
                    </div>
                @endif
            </div>

            @if($report->note)
                <div class="section-title">{{ __('Notes') }}</div>
                <p style="font-size: 13px; line-height: 1.5; color: #4b5563;">{{ $report->note }}</p>
            @endif

            <div class="section-title">{{ __('Report Details') }}</div>
            @foreach($report->bodies as $index => $body)
                <div class="body-card">
                    <div class="body-card-header">#{{ $index + 1 }}</div>
                    <div class="body-card-content">{!! nl2br(e($body->content)) !!}</div>
                    @if($body->observation)
                        <div class="body-card-obs">
                            <strong>{{ __('Observation') }}:</strong> {{ $body->observation }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="footer">
            <p>{{ __('This email was automatically generated by the Educational Dashboard.') }}</p>
        </div>
    </div>
</body>
</html>
