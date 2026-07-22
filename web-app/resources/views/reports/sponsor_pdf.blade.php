<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }
        body { font-family: 'DejaVu Sans', sans-serif; padding: 40px; color: #444; line-height: 1.6; }
        
        /* الهيدر الأنيق */
        .header { text-align: center; background: #f8f9fa; padding: 20px; border-radius: 10px; border-bottom: 4px solid #28a745; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #2c3e50; font-size: 26px; }
        .header h3 { margin: 5px 0 0; color: #7f8c8d; font-weight: normal; }

        /* صندوق المعلومات */
        .info-box { background: #fff; border: 1px solid #e1e1e1; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .info-row { margin-bottom: 10px; font-size: 16px; }
        .info-row strong { color: #2c3e50; min-width: 120px; display: inline-block; }

        /* تصميم الجدول الاحترافي */
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 10px; border: 1px solid #e1e1e1; border-radius: 8px; overflow: hidden; }
        th { background: #28a745; color: white; padding: 15px; text-align: center; }
        td { padding: 12px; border-top: 1px solid #e1e1e1; text-align: center; }
        tr:nth-child(even) { background: #f9f9f9; }
        
        h3.table-title { color: #28a745; margin-bottom: 10px; border-right: 4px solid #28a745; padding-right: 10px; }

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
        <hr style="border: 1px solid #28a745; margin: 10px 0;">
        <h2 style="margin: 0; color: #2c3e50;">{{ $title }}</h2>
        <h4 style="margin: 5px 0 0; color: #7f8c8d; font-weight: normal;">{{ $sponsorName }}</h4>
    </div>

    <div class="info-box">
        <div class="info-row"><strong>{{ $phoneLbl }}</strong> {{ $sponsor->phone }}</div>
        <div class="info-row"><strong>{{ $emailLbl }}</strong> {{ $sponsor->email }}</div>
    </div>

    <h3 class="table-title">{{ $orphansTitle }}</h3>
    <table>
        <thead>
            <tr>
                <th>{{ $orphanName }}</th>
                <th>{{ $status }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orphans as $orphan)
            <tr>
                <td>{{ $orphan['name'] }}</td>
                <td>{{ $orphan['status'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

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
