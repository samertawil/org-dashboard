<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Daily Log Report') }} - {{ $date }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #4f46e5;
            margin: 0;
            font-size: 20px;
        }
        .section-title {
            background-color: #f3f4f6;
            padding: 8px 12px;
            border-right: 4px solid #4f46e5;
            margin: 20px 0 10px 0;
            font-weight: bold;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: right;
        }
        th {
            background-color: #f9fafb;
            font-weight: bold;
        }
        .cost-box {
            background-color: #eef2ff;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
        .cost-box span {
            display: block;
            font-size: 18px;
            font-weight: bold;
            color: #4f46e5;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #9ca3af;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            background: #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('Daily Activity Entry Log') }}</h1>
        <p>{{ $date }}</p>
    </div>

    @if($exchangeRate)
    <div style="font-size: 11px; margin-bottom: 15px; color: #4b5563;">
        {{ __('Exchange Rate') }}: 1 USD = {{ $exchangeRate->currency_value }} NIS
    </div>
    @endif

    <div class="section-title">{{ __('Operations Activities') }}</div>
    @forelse($activities as $activity)
        <div style="margin-bottom: 20px; border-bottom: 1px solid #f3f4f6; padding-bottom: 10px;">
            <div style="display: flex; justify-content: space-between;">
                <strong>{{ $activity->name }}</strong>
                <span style="color: #4f46e5; font-weight: bold;">{{ number_format($activity->cost, 2) }} $</span>
            </div>
            <div style="font-size: 11px; color: #6b7280;">
                {{ $activity->regions->region_name ?? '' }} / {{ $activity->cities->city_name ?? '' }} | 
                {{ __('By') }}: {{ $activity->creator->name ?? 'Unknown' }}
            </div>
            
            <table style="margin-top: 5px;">
                <tr>
                    <th width="50%">{{ __('Distributed Parcels') }}</th>
                    <th width="50%">{{ __('Beneficiaries') }}</th>
                </tr>
                <tr>
                    <td valign="top">
                        @forelse($activity->parcels as $parcel)
                            <div>{{ $parcel->parcelType->status_name ?? 'Parcel' }}: {{ number_format($parcel->distributed_parcels_count) }} {{ $parcel->unit->status_name ?? '' }}</div>
                        @empty
                            <div style="color: #9ca3af;">-</div>
                        @endforelse
                    </td>
                    <td valign="top">
                        @forelse($activity->beneficiaries as $ben)
                            <div>{{ $ben->beneficiaryType->status_name ?? 'Beneficiary' }}: {{ number_format($ben->beneficiaries_count) }}</div>
                        @empty
                            <div style="color: #9ca3af;">-</div>
                        @endforelse
                    </td>
                </tr>
            </table>
        </div>
    @empty
        <p style="text-align: center; color: #9ca3af;">{{ __('No activities recorded today.') }}</p>
    @endforelse

    <div class="section-title">{{ __('Education & Tracking') }}</div>
    <table style="width: 100%;">
        <tr>
            <th width="50%">{{ __('Evaluations & Surveys') }}</th>
            <th width="50%">{{ __('Attendance Tracking') }}</th>
        </tr>
        <tr>
            <td valign="top">
                @forelse($evaluations as $eval)
                    <div style="margin-bottom: 5px;">
                        <strong>{{ $eval->surveyfor->status_name ?? __('Evaluation') }}</strong>: {{ $eval->students_count }} {{ __('Students') }}
                    </div>
                @empty
                    <div style="color: #9ca3af;">{{ __('No evaluations recorded.') }}</div>
                @endforelse
            </td>
            <td valign="top">
                @forelse($attendanceStats as $stat)
                    <div style="margin-bottom: 5px;">
                        <strong>{{ $stat->studentGroup->name ?? __('Group') }}</strong>: {{ $stat->total_entries }} {{ __('Entries') }}
                    </div>
                @empty
                    <div style="color: #9ca3af;">{{ __('No attendance entries recorded.') }}</div>
                @endforelse
            </td>
        </tr>
    </table>

    <div class="footer">
        {{ __('Generated automatically by') }} {{ config('app.name') }} - {{ now()->format('Y-m-d H:i') }}
    </div>
</body>
</html>
