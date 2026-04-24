@php
    use App\Helpers\Arabic;
@endphp
<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ Arabic::reshape('عرض سعر مالي') }}</title>
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
        @font-face {
            font-family: 'Cairo';
            font-style: normal;
            font-weight: normal;
            src: url("{{ public_path('fonts/cairo_normal.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'Cairo';
            font-style: normal;
            font-weight: bold;
            src: url("{{ public_path('fonts/cairo_bold.ttf') }}") format('truetype');
        }

        body {
            font-family: 'DejaVu Sans', 'Cairo', 'Amiri', sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.5;
            direction: rtl;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-content {
            display: table;
            width: 100%;
        }
        .header-left, .header-right {
            display: table-cell;
            vertical-align: bottom;
        }
        .header-right {
            text-align: right;
        }
        h1 {
            font-size: 18px;
            margin: 0;
            color: #111;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
            color: #444;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right; /* تم ضبطها لليمين */
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 11px;
        }
        .label {
            font-weight: bold;
            color: #555;
            font-size: 11px;
        }
        .value {
            font-size: 12px;
            color: #000;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px;
            text-align: center;
            font-size: 9px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <h1>{{ Arabic::reshape('عرض سعر مالي') }}</h1>
                <div style="font-size: 10px; color: #666; margin-top: 5px;">
                    {{ Arabic::reshape('تاريخ الاستخراج:') }} {{ date('Y-m-d H:i') }}
                </div>
            </div>
            <div class="header-right">
                <strong>{{ Arabic::reshape(config('app.name')) }}</strong><br>
                {{ Arabic::reshape('نظام إدارة المشتريات') }}
            </div>
        </div>
    </div>

    <!-- معلومات الطلب والمورد -->
    <div class="section">
        <div class="section-title">{{ Arabic::reshape('معلومات العرض') }}</div>
        <table>
            <tr>
                <td style="width: 50%; border: none;">
                    <span class="label">{{ Arabic::reshape('اسم المورد:') }}</span>
                    <span class="value">{{ Arabic::reshape($quotation->vendor->name) }}</span>
                </td>
                <td style="width: 50%; border: none;">
                    <span class="label">{{ Arabic::reshape('رقم طلب الشراء:') }}</span>
                    <span class="value">#{{ $quotation->purchaseRequisition->request_number }}</span>
                </td>
            </tr>
            <tr>
                <td style="border: none;">
                    <span class="label">{{ Arabic::reshape('تاريخ التقديم:') }}</span>
                    <span class="value">{{ $quotation->submitted_at->format('Y-m-d H:i') }}</span>
                </td>
                <td style="border: none;">
                    <span class="label">{{ Arabic::reshape('العملة:') }}</span>
                    <span class="value">{{ Arabic::reshape($quotation->currency->status_name ?? '-') }}</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- جدول الأصناف -->
    <div class="section">
        <div class="section-title">{{ Arabic::reshape('تفاصيل الأسعار المقدمة') }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 50%;">{{ Arabic::reshape('الصنف') }}</th>
                    <th>{{ Arabic::reshape('الكمية') }}</th>
                    <th>{{ Arabic::reshape('سعر الوحدة') }}</th>
                    <th>{{ Arabic::reshape('الإجمالي') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quotation->prices as $price)
                <tr>
                    <td>
                        {{ Arabic::reshape($price->requisitionItem->item_name) }}
                        @if($price->vendor_item_notes)
                            <br><small style="color: #666;">{{ Arabic::reshape('ملاحظة: ' . $price->vendor_item_notes) }}</small>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $price->requisitionItem->quantity }}</td>
                    <td style="text-align: center;">{{ number_format($price->offered_price, 2) }}</td>
                    <td style="text-align: center;">{{ number_format($price->offered_price * $price->requisitionItem->quantity, 2) }}</td>
                </tr>
                @endforeach
                <tr style="background-color: #f9f9f9; font-weight: bold;">
                    <td colspan="3" style="text-align: left; padding-left: 20px;">{{ Arabic::reshape('المجموع الإجمالي للعرض:') }}</td>
                    <td style="text-align: center; color: #d9534f;">{{ number_format($quotation->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if($quotation->notes)
        <div class="section">
            <div class="section-title">{{ Arabic::reshape('ملاحظات المورد العامة') }}</div>
            <div style="padding: 10px; background: #f9f9f9; border: 1px solid #eee;">
                {{ Arabic::reshape($quotation->notes) }}
            </div>
        </div>
    @endif

    <div class="footer">
        {{ Arabic::reshape(config('app.name')) }} - {{ Arabic::reshape('تقرير عرض سعر آلي') }}
    </div>
</body>
</html>
