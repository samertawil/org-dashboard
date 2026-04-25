<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #ffffff; color: #1a202c;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #2d3748; font-size: 24px; margin-bottom: 10px;">{{ $data['subject'] }}</h1>
        <div style="width: 50px; height: 4px; background: #4f46e5; margin: 0 auto; border-radius: 2px;"></div>
    </div>

    <div style="margin-bottom: 25px;">
        <p style="font-size: 16px; line-height: 1.6;">
            <strong>اسم المورد:</strong> {{ $data['name'] }}
        </p>
    </div>

    <div style="background-color: #f7fafc; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
        <h3 style="margin-top: 0; color: #4a5568; font-size: 14px; text-transform: uppercase; letter-spacing: 0.05em;">ملاحظات عرض السعر:</h3>
        <p style="font-size: 15px; line-height: 1.5; color: #2d3748; margin-bottom: 0;">
            {{ $data['notes'] ?: 'لا توجد ملاحظات إضافية' }}
        </p>
    </div>

    <div style="background-color: #f7fafc; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
       
        <p style="font-size: 15px; line-height: 1.5; color: #2d3748; margin-bottom: 0;">
            {{ $data['link']  }}
        </p>
    </div>

    <div style="text-align: center; padding-top: 20px; border-top: 1px solid #edf2f7; font-size: 12px; color: #a0aec0;">
        هذا البريد مرسل تلقائياً من نظام إدارة المؤسسة.
    </div>
</div>