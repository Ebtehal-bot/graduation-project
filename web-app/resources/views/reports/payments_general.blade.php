<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>السجل الشامل للمدفوعات</title>
    <style>
        /* التنسيق العام */
        body { font-family: 'DejaVu Sans', sans-serif; text-align: center; padding: 30px; color: #333; background-color: #fff; }
        .header { margin-bottom: 40px; border-bottom: 3px solid #444; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #000; font-size: 28px; }
        
        /* تنسيق الجدول */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #000; padding: 15px; text-align: center; font-size: 16px; }
        th { background-color: #f8f9fa; font-weight: bold; }
        tr:nth-child(even) { background-color: #fafafa; }

        /* صندوق الإجمالي */
        .total-box { padding: 20px; border: 2px solid #28a745; background-color: #d4edda; color: #155724; font-weight: bold; display: inline-block; margin-top: 30px; font-size: 20px; }
        
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
        $sysName = \App\Models\Setting::getValue('site_name', 'منصة كفيل لرعاية وكفالة الأيتام');
        $org = \App\Models\Setting::getValue('org_name', 'منصة كفيل لرعاية وكفالة الأيتام');
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
        <h1>السجل الشامل للمدفوعات</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th>اسم اليتيم</th>
                <th>رقم السند</th>
                <th>تاريخ الدفع</th>
                <th>المبلغ (ريال يمني)</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->sponsorship->orphan->name ?? '---' }}</td>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->date }}</td>
                    <td>{{ number_format($payment->amount) }} ريال</td>
                </tr>
                @php $grandTotal += $payment->amount; @endphp
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        إجمالي التحصيل الكلي: {{ number_format($grandTotal) }} ريال
    </div>

    <div style="width: 100%; margin-top: 40px; padding-top: 15px; border-top: 2px solid #333; direction: rtl !important;">
        <table style="width: 100%; border-collapse: collapse; direction: rtl !important; table-layout: fixed;">
            <tr>
                @if(!empty($settings->manager))
                    <td style="text-align: center; padding: 5px; font-size: 13px; font-weight: bold; color: #222; font-family: 'DejaVu Sans', sans-serif; direction: rtl;">
                        <span dir="rtl">مدير المؤسسة:</span>
                        <span dir="rtl">{{ $settings->manager }}</span>
                    </td>
                @endif
                @if(!empty($settings->phone))
                    <td style="text-align: center; padding: 5px; font-size: 13px; font-weight: bold; color: #222; font-family: 'DejaVu Sans', sans-serif; direction: rtl;">
                        <span dir="rtl">الهاتف:</span>
                        <span dir="ltr">{{ $settings->phone }}</span>
                    </td>
                @endif
                @if(!empty($settings->email))
                    <td style="text-align: center; padding: 5px; font-size: 13px; font-weight: bold; color: #222; font-family: 'DejaVu Sans', sans-serif; direction: rtl;">
                        <span dir="rtl">البريد:</span>
                        <span dir="ltr">{{ $settings->email }}</span>
                    </td>
                @endif
            </tr>
            <tr>
                @if(!empty($settings->website))
                    <td style="text-align: center; padding: 5px; font-size: 13px; font-weight: bold; color: #222; font-family: 'DejaVu Sans', sans-serif; direction: rtl;">
                        <span dir="rtl">الموقع:</span>
                        <span dir="ltr">{{ $settings->website }}</span>
                    </td>
                @endif
                @if(!empty($settings->address))
                    <td style="text-align: center; padding: 5px; font-size: 13px; font-weight: bold; color: #222; font-family: 'DejaVu Sans', sans-serif; direction: rtl;">
                        <span dir="rtl">العنوان:</span>
                        <span dir="rtl">{{ $settings->address }}</span>
                    </td>
                @endif
            </tr>
        </table>
    </div>
</body>
</html>
