<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @php
            $sysLogo = $systemLogo ?? \App\Models\Setting::getValue('site_logo', '');
            $sysName = $systemName ?? \App\Models\Setting::getValue('site_name', 'منصة كفيل لرعاية وكفالة الأيتام');
            $org = $orgName ?? \App\Models\Setting::getValue('org_name', 'منصة كفيل');
        @endphp
        body { font-family: 'DejaVu Sans', sans-serif; padding: 30px; direction: rtl; text-align: right; color: #333; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #1a237e; padding-bottom: 18px; }
        .header h1 { color: #1a237e; font-size: 22px; margin: 6px 0; }
        .header h2 { color: #555; font-size: 16px; font-weight: normal; margin: 4px 0; }
        .header p { color: #888; font-size: 13px; margin: 6px 0 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; border-radius: 8px; overflow: hidden; }
        th { background-color: #1a237e; color: #fff; padding: 14px 10px; text-align: center; font-size: 14px; font-weight: bold; border: 1px solid #1a237e; }
        td { border: 1px solid #ddd; padding: 12px 10px; text-align: center; font-size: 14px; }
        tr:nth-child(even) { background-color: #f8faff; }
        .no-print-btn { background: #1a237e; color: white; padding: 12px 25px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 15px; margin-bottom: 20px; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .status-high { color: #16a34a; font-weight: bold; }
        .status-normal { color: #d97706; font-weight: bold; }
        .footer { width: 100%; margin-top: 40px; padding-top: 15px; border-top: 2px solid #1a237e; text-align: center; font-size: 12px; color: #555; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print"><button class="no-print-btn" onclick="window.print()">🖨️ طباعة تقرير الإدارة العليا</button></div>
    <div class="header">
        @if (!empty($sysLogo))
            <img src="{{ asset('storage/' . $sysLogo) }}" alt="{{ $sysName }}" style="max-height: 50px; margin-bottom: 5px;">
        @endif
        <h1>{{ $org }}</h1>
        <h2>{{ $sysName }}</h2>
        <h1 style="margin-top: 10px;">{{ $title }}</h1>
        <p>تقرير تحليلي مقارن لمستوى الإنجاز في الفروع — {{ date('Y') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>رقم الفرع</th>
                <th>اسم الفرع / المحافظة</th>
                <th>إجمالي الأيتام المسجلين</th>
                <th>مستوى التغطية</th>
                <th>حالة الإنتاجية</th>
            </tr>
        </thead>
        <tbody>
            @foreach($branches as $branch)
            <tr>
                <td style="font-weight: bold; color: #1a237e;">BR-{{ $branch->id }}</td>
                <td><strong>{{ $branch->name }}</strong><br><small style="color: #888;">{{ $branch->governorate }}</small></td>
                <td style="font-weight: bold; font-size: 16px;">{{ $branch->orphans_count }}</td>
                <td>
                    @if($branch->orphans_count > 10)
                        <span class="status-high">🟢 تغطية ممتازة</span>
                    @elseif($branch->orphans_count > 5)
                        <span class="status-high">🟢 تغطية نشطة</span>
                    @elseif($branch->orphans_count > 2)
                        <span class="status-normal">🟡 تغطية متوسطة</span>
                    @else
                        <span style="color: #dc2626; font-weight: bold;">🔴 تغطية ضعيفة</span>
                    @endif
                </td>
                <td>
                    <span style="display: inline-block; padding: 3px 12px; border-radius: 12px; font-size: 13px; font-weight: bold;
                        @if($branch->orphans_count > 5) background: #dcfce7; color: #166534;
                        @else background: #fef3c7; color: #92400e;
                        @endif">
                        {{ $branch->orphans_count > 5 ? 'نشط' : 'اعتيادي' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        @php
            $settings = (object)[
                'manager' => $orgManager ?? \App\Models\Setting::getValue('org_manager', ''),
                'phone' => $orgPhone ?? \App\Models\Setting::getValue('org_phone', ''),
                'email' => $orgEmail ?? \App\Models\Setting::getValue('org_email', ''),
            ];
        @endphp
        @if(!empty($settings->manager)) مدير المؤسسة: {{ $settings->manager }} @endif
        @if(!empty($settings->phone)) | هاتف: {{ $settings->phone }} @endif
        @if(!empty($settings->email)) | البريد: {{ $settings->email }} @endif
    </div>
</body>
</html>