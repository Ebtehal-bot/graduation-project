<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف أيتام الفرع</title>
    <style>
        @php
            $sysLogo = $systemLogo ?? \App\Models\Setting::getValue('site_logo', '');
            $sysName = $systemName ?? \App\Models\Setting::getValue('site_name', 'منصة كفيل لرعاية وكفالة الأيتام');
            $org = $orgName ?? \App\Models\Setting::getValue('org_name', 'منصة كفيل');
            $mgr = $orgManager ?? \App\Models\Setting::getValue('org_manager', '');
            $mgrPhone = $orgPhone ?? \App\Models\Setting::getValue('org_phone', '');
            $mgrEmail = $orgEmail ?? \App\Models\Setting::getValue('org_email', '');
        @endphp
        body { font-family: 'DejaVu Sans', sans-serif; text-align: right; padding: 25px; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #1a237e; padding-bottom: 18px; }
        .header h1 { color: #1a237e; font-size: 22px; margin: 6px 0; }
        .header h2 { color: #555; font-size: 16px; font-weight: normal; margin: 4px 0; }
        .header h3 { color: #1a237e; font-size: 18px; margin: 8px 0 4px; }
        .header p { color: #888; font-size: 13px; margin: 4px 0; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; border-radius: 8px; overflow: hidden; }
        th { background-color: #1a237e; color: #fff; padding: 12px 10px; text-align: center; font-size: 14px; font-weight: bold; border: 1px solid #1a237e; }
        td { border: 1px solid #ddd; padding: 10px; text-align: center; font-size: 13px; }
        tr:nth-child(even) { background-color: #f8faff; }

        .no-print { text-align: center; margin-bottom: 20px; }
        .btn-print { background: #1a237e; color: white; padding: 12px 25px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 15px; }
        .footer { width: 100%; margin-top: 35px; padding-top: 15px; border-top: 2px solid #1a237e; text-align: center; font-size: 12px; color: #555; }
        @media print { .no-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ طباعة كشف الفرع</button>
    </div>

    <div class="header">
        @if (!empty($sysLogo))
            <img src="{{ asset('storage/' . $sysLogo) }}" alt="{{ $sysName }}"
                 style="max-height: 50px; margin-bottom: 5px;">
        @endif
        <h1>{{ $sysName }}</h1>
        <h2>{{ $org }}</h2>
        <h3>كشف أيتام: {{ $record->name }}</h3>
        <p>محافظة {{ $record->governorate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">م</th>
                <th>اسم اليتيم</th>
                <th>الكفيل</th>
                <th>مبلغ الكفالة</th>
                <th style="width: 140px;">التوقيع / البصمة</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @foreach($orphans as $index => $orphan)
                @php
                    $amount = $orphan->sponsorship->monthly_amount ?? 0;
                    $totalAmount += $amount;
                @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="font-weight: bold;">{{ $orphan->name }}</td>
                <td>{{ $orphan->sponsorship->sponsor->name ?? 'غير مكفول' }}</td>
                <td style="color: #1a237e; font-weight: bold;">{{ number_format($amount) }}</td>
                <td style="height: 36px;"></td>
            </tr>
            @endforeach
            <tr style="background-color: #f0f4ff; font-weight: bold; border-top: 2px solid #1a237e;">
                <td colspan="3" style="text-align: left;">الإجمالي العام:</td>
                <td style="color: #1a237e;">{{ number_format($totalAmount) }} ريال</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        @if(!empty($mgr)) مدير المؤسسة: {{ $mgr }} @endif
        @if(!empty($mgrPhone)) | هاتف: {{ $mgrPhone }} @endif
        @if(!empty($mgrEmail)) | البريد: {{ $mgrEmail }} @endif
    </div>
</body>
</html>