<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تقرير مالي - {{ $orphan->name }}</title>
    <style>
        /* التنسيق العام */
        body { font-family: 'DejaVu Sans', sans-serif; text-align: center; padding: 30px; color: #333; background-color: #fff; }
        .header { margin-bottom: 40px; border-bottom: 3px solid #444; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #000; font-size: 28px; }
        .header h2 { color: #555; margin: 10px 0; }
        
        /* تنسيق الجدول */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #000; padding: 15px; text-align: center; font-size: 16px; }
        th { background-color: #f8f9fa; font-weight: bold; }
        tr:nth-child(even) { background-color: #fafafa; }

        /* صناديق الإحصائيات */
        .stats-container { margin-top: 40px; display: flex; justify-content: center; gap: 20px; }
        .stat-box { padding: 20px; border-radius: 8px; min-width: 250px; font-size: 18px; }
        .target-box { border: 2px solid #6c757d; background-color: #f8f9fa; color: #495057; }
        .total-box { border: 2px solid #28a745; background-color: #d4edda; color: #155724; font-weight: bold; }

        /* زر الطباعة */
        .no-print { margin-bottom: 30px; }
        .btn-print { padding: 12px 25px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-print:hover { background: #218838; }

        @media print { 
            .no-print { display: none !important; } 
            body { padding: 0; }
            table { box-shadow: none; }
        }

    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">طباعة التقرير (PDF)</button>
        <p style="color: #666; font-size: 14px;">سيتم إخفاء هذا الزر تلقائياً عند الطباعة</p>
    </div>

    @php
        $sysLogo = $systemLogo ?? \App\Models\Setting::getValue('site_logo', '');
        $sysName = $systemName ?? \App\Models\Setting::getValue('site_name', 'منصة كفيل لرعاية وكفالة الأيتام');
        $org = $orgName ?? \App\Models\Setting::getValue('org_name', 'منصة كفيل');
        $mgr = $orgManager ?? \App\Models\Setting::getValue('org_manager', '');
        $mgrEmail = $orgEmail ?? \App\Models\Setting::getValue('org_email', '');
        $mgrPhone = $orgPhone ?? \App\Models\Setting::getValue('org_phone', '');
        $mgrWebsite = $orgWebsite ?? \App\Models\Setting::getValue('org_website', '');
        $mgrAddress = $orgAddress ?? \App\Models\Setting::getValue('org_address', '');
        $settings = (object)[
            'manager' => $mgr,
            'phone' => $mgrPhone,
            'email' => $mgrEmail,
            'website' => $mgrWebsite,
            'address' => $mgrAddress,
        ];
    @endphp
    <div class="header">
        @if (!empty($sysLogo))
            <img src="{{ asset('storage/' . $sysLogo) }}" alt="{{ $sysName }}"
                 style="max-height: 50px; margin-bottom: 5px;">
        @endif
        <h1>{{ $sysName }}</h1>
        <h3>{{ $org }}</h3>
        <hr style="border: 1px solid #444; margin: 10px 0;">
        <h2>كشف مبالغ الكفالة التفصيلي</h2>
        <h3>اسم اليتيم: {{ $orphan->name }}</h3>
        <h4>العام المالي المختار: {{ $year }}</h4>
    </div>

    <table>
        <thead>
            <tr>
                <th>رقم السند</th>
                <th>تاريخ الدفع</th>
                <th>المبلغ (ريال يمني)</th>
                <th>حالة الدفع</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->date }}</td>
                    <td>{{ number_format($payment->amount) }} ريال</td>
                    <td>
                        <span style="color: {{ $payment->payment_status == 'paid' ? '#28a745' : '#dc3545' }}">
                            {{ $payment->payment_status == 'paid' || $payment->payment_status == 'تم الاستلام' ? 'تم الاستلام' : 'معلق' }}
                        </span>
                    </td>
                </tr>
                @php $total += $payment->amount; @endphp
            @empty
                <tr>
                    <td colspan="4" style="color: #dc3545; padding: 30px; font-weight: bold;">
                        لا توجد أي مدفوعات مسجلة لهذا اليتيم في النظام
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="stats-container">
        <div class="stat-box target-box">
            المستهدف السنوي: 
            @php $monthly = $orphan->sponsorship->monthly_amount ?? 0; @endphp
            {{ number_format($monthly * 12) }} ريال
        </div>

        <div class="stat-box total-box">
            إجمالي التحصيل الفعلي: {{ number_format($total) }} ريال
        </div>
    </div>

    <div style="margin-top: 60px; display: flex; justify-content: space-around; font-weight: bold;">
        <div style="text-align: center;">
            <p>توقيع المسؤول المالي</p>
            <p>............................</p>
        </div>
        <div style="text-align: center;">
            <p>ختم المؤسسة</p>
            <br>
            <p>( ............................ )</p>
        </div>
    </div>

    <div style="width: 100%; margin-top: 40px; padding-top: 15px; border-top: 2px solid #333; direction: rtl; text-align: center;">
        <table style="width: 100%; border-collapse: collapse; direction: rtl;">
            <tr>
                <td style="text-align: center !important; font-size: 13px; font-weight: bold; color: #222; font-family: sans-serif; line-height: 1.6; direction: rtl !important;">
                    @if(!empty($settings->manager)) <span style="display: inline-block;">مدير المؤسسة: {{ $settings->manager }}</span> @endif
                    @if(!empty($settings->phone)) <span style="display: inline-block; margin-right: 15px;">| هاتف الرسمي: {{ $settings->phone }}</span> @endif
                    @if(!empty($settings->email)) <span style="display: inline-block; margin-right: 15px;">| البريد الرسمي: {{ $settings->email }}</span> @endif
                    @if(!empty($settings->website)) <span style="display: inline-block; margin-right: 15px;">| الموقع الإلكتروني: {{ $settings->website }}</span> @endif
                    @if(!empty($settings->address)) <span style="display: inline-block; margin-right: 15px;">| العنوان: {{ $settings->address }}</span> @endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
