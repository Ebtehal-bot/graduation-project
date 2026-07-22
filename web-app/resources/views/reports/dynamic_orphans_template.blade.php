<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @php
            $sysLogo = $logoUrl ?? \App\Models\Setting::getValue('site_logo', '');
            $sysName = $systemName ?? \App\Models\Setting::getValue('site_name', 'منصة كفيل');
            $org = $orgName ?? \App\Models\Setting::getValue('org_name', 'منصة كفيل');
        @endphp
        body { font-family: 'DejaVu Sans', sans-serif; padding: 25px; direction: rtl; text-align: right; color: #333; line-height: 1.5; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 3px solid #1a237e; padding-bottom: 18px; }
        .header h1 { color: #1a237e; font-size: 22px; margin: 6px 0; }
        .header h2 { color: #555; font-size: 16px; font-weight: normal; margin: 4px 0; }
        .header h3 { color: #1a237e; font-size: 18px; margin: 8px 0 4px; }
        .header p { color: #888; font-size: 13px; margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; border-radius: 8px; overflow: hidden; }
        th { background-color: #1a237e; color: #fff; padding: 10px 8px; text-align: center; font-size: 13px; font-weight: bold; border: 1px solid #1a237e; }
        td { border: 1px solid #ddd; padding: 9px 8px; text-align: center; font-size: 13px; }
        tr:nth-child(even) { background-color: #f8faff; }
        .summary-box { margin-top: 20px; border: 2px solid #1a237e; padding: 18px; background: #f0f4ff; border-radius: 8px; display: flex; justify-content: space-around; flex-wrap: wrap; gap: 15px; }
        .summary-box div { font-size: 15px; }
        .signatures { margin-top: 40px; display: flex; justify-content: space-between; text-align: center; }
        .signatures div { flex: 1; padding: 10px; }
        .no-print-btn { background: #1a237e; color: white; padding: 12px 25px; border: none; border-radius: 6px; cursor: pointer; font-size: 15px; font-weight: bold; margin-bottom: 20px; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .report-notes { font-size: 14px; margin: 15px 0; padding: 14px 18px; border: 2px solid #d97706; background-color: #fffbeb; border-radius: 8px; line-height: 1.7; color: #92400e; }
        .report-notes strong { color: #78350f; }
        .footer { width: 100%; margin-top: 35px; padding-top: 15px; border-top: 2px solid #1a237e; text-align: center; font-size: 12px; color: #555; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="no-print-btn" onclick="window.print()">🖨️ طباعة التقرير</button>
    </div>
    <div class="header">
        @if (!empty($sysLogo))
            <img src="{{ asset('storage/' . $sysLogo) }}" alt="{{ $sysName }}" style="max-height: 50px; margin-bottom: 5px;">
        @endif
        <h2>{{ $org }}</h2>
        <h3>{{ $sysName }}</h3>
        <h1>{{ $title }}</h1>
        <p>تاريخ الكشف: {{ date('Y-m-d') }}</p>
    </div>

    @if(!empty($notes))
        <div class="report-notes">
            <strong>بيان وتوجيهات الصرف:</strong> {{ $notes }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">م</th>
                @if(in_array('name', $columns)) <th>اسم اليتيم</th> @endif
                @if(in_array('sponsor', $columns)) <th>الكفيل</th> @endif
                @if(in_array('amount', $columns)) <th>مبلغ الكفالة</th> @endif
                @if(in_array('signature', $columns)) <th style="width: 140px;">التوقيع / البصمة</th> @endif
                @if(in_array('notes', $columns)) <th>ملاحظات</th> @endif
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; $appliedAmount = (!empty($minAmount) && is_numeric($minAmount)) ? (float)$minAmount : null; @endphp
            @foreach($orphans as $index => $orphan)
            @php
                $orphanMonthlyAmount = $appliedAmount !== null ? $appliedAmount : ($orphan->sponsorship->monthly_amount ?? 0);
                $orphanCalculatedAmount = $orphanMonthlyAmount * ($monthsCount ?? 1);
                $totalAmount += $orphanCalculatedAmount;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                @if(in_array('name', $columns)) <td style="font-weight: bold;">{{ $orphan->name }}</td> @endif
                @if(in_array('sponsor', $columns)) <td>{{ $orphan->sponsorship->sponsor->name ?? 'بدون كفيل' }}</td> @endif
                @if(in_array('amount', $columns))
                    <td>
                        <strong style="color: #1a237e;">{{ number_format($orphanCalculatedAmount) }}</strong>
                        @if(($monthsCount ?? 1) > 1)
                            <div style="font-size: 11px; color: #888; margin-top: 2px;">
                                × {{ $monthsCount }} شهر ({{ number_format($orphanMonthlyAmount) }}/شهرياً)
                            </div>
                        @endif
                    </td>
                @endif
                @if(in_array('signature', $columns)) <td style="height: 36px;"></td> @endif
                @if(in_array('notes', $columns)) <td></td> @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <div><strong>إجمالي عدد الأيتام:</strong> {{ $orphans->count() }} يتيم</div>
        <div>
            <strong style="color: #1a237e;">إجمالي المبالغ المقررة:</strong> {{ number_format($totalAmount) }} ريال
            @if(($monthsCount ?? 1) > 1)
                <span style="color: #888; font-size: 12px;">(لمدة {{ $monthsCount }} أشهر)</span>
            @endif
        </div>
    </div>

    <div class="signatures">
        <div>
            <p style="border-top: 1px solid #ddd; padding-top: 8px; font-size: 14px;"><strong>أمين الصندوق والمحاسب</strong></p>
            <p style="margin-top: 25px;">التوقيع: ........................</p>
        </div>
        <div>
            <p style="border-top: 1px solid #ddd; padding-top: 8px; font-size: 14px;"><strong>مدير إدارة الأيتام</strong></p>
            <p style="margin-top: 25px;">التوقيع: ........................</p>
        </div>
        <div>
            <p style="border-top: 1px solid #ddd; padding-top: 8px; font-size: 14px;"><strong>رئيس المؤسسة</strong></p>
            <p style="margin-top: 25px;">التوقيع والختم: ........................</p>
        </div>
    </div>

    @if(!empty($generateDifferences) && $differencesOrphans && $differencesOrphans->count() > 0)
    <div style="page-break-before: always;"></div>

    <div class="header">
        @if (!empty($sysLogo))
            <img src="{{ asset('storage/' . $sysLogo) }}" alt="{{ $sysName }}" style="max-height: 50px; margin-bottom: 5px;">
        @endif
        <h2>{{ $org }}</h2>
        <h3>{{ $sysName }}</h3>
        <h1>كشف الفروقات الإضافي</h1>
        <p>الأيتام الذين تتجاوز كفالاتهم {{ number_format($minAmount) }} ريال</p>
        <p>تاريخ الكشف: {{ date('Y-m-d') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">م</th>
                <th>اسم اليتيم</th>
                <th>الكفيل</th>
                <th>المبلغ المتبقي ( surplus )</th>
            </tr>
        </thead>
        <tbody>
            @php $diffGrandTotal = 0; @endphp
            @foreach($differencesOrphans as $index => $orphan)
                @php
                    $orphanAmount = $orphan->sponsorship->monthly_amount ?? 0;
                    $surplus = ($orphanAmount - (float) $minAmount) * ($monthsCount ?? 1);
                    $diffGrandTotal += $surplus;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="font-weight: bold;">{{ $orphan->name }}</td>
                    <td>{{ $orphan->sponsorship->sponsor->name ?? 'بدون كفيل' }}</td>
                    <td style="color: #16a34a; font-weight: bold; font-size: 15px;">
                        {{ number_format($surplus) }} ريال
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-box">
        <div><strong>عدد الأيتام ذوي الفروقات:</strong> {{ $differencesOrphans->count() }} يتيم</div>
        <div>
            <strong style="color: #16a34a;">إجمالي المبالغ المتبقية:</strong> {{ number_format($diffGrandTotal) }} ريال
        </div>
    </div>

    <div class="signatures">
        <div>
            <p style="border-top: 1px solid #ddd; padding-top: 8px; font-size: 14px;"><strong>المدقق المالي</strong></p>
            <p style="margin-top: 25px;">التوقيع: ........................</p>
        </div>
        <div>
            <p style="border-top: 1px solid #ddd; padding-top: 8px; font-size: 14px;"><strong>مدير إدارة الأيتام</strong></p>
            <p style="margin-top: 25px;">التوقيع: ........................</p>
        </div>
        <div>
            <p style="border-top: 1px solid #ddd; padding-top: 8px; font-size: 14px;"><strong>المدير العام</strong></p>
            <p style="margin-top: 25px;">التوقيع والختم: ........................</p>
        </div>
    </div>

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
    @else
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
    @endif
</body>
</html>