<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف صرف مستحقات الأيتام</title>
    @php
        $sysLogo = $systemLogo ?? \App\Models\Setting::getValue('site_logo', '');
        $sysName = $systemName ?? \App\Models\Setting::getValue('site_name', 'منصة كفيل لرعاية وكفالة الأيتام');
        $org = $orgName ?? \App\Models\Setting::getValue('org_name', 'منصة كفيل');
        $mgr = $orgManager ?? \App\Models\Setting::getValue('org_manager', '');
        $mgrPhone = $orgPhone ?? \App\Models\Setting::getValue('org_phone', '');
        $mgrEmail = $orgEmail ?? \App\Models\Setting::getValue('org_email', '');
    @endphp
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            padding: 25px;
            direction: rtl;
            background-color: #fff;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #1a237e;
            padding-bottom: 18px;
        }
        .header h1 { color: #1a237e; font-size: 22px; margin: 6px 0; }
        .header h2 { color: #555; font-size: 16px; font-weight: normal; margin: 4px 0; }
        .header h3 { color: #1a237e; font-size: 20px; margin: 10px 0 4px; }
        .header p { color: #888; font-size: 13px; margin: 4px 0; }
        .header .meta-row { display: flex; justify-content: space-between; font-size: 12px; color: #64748b; margin-top: 8px; }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 13px;
            border-radius: 8px;
            overflow: hidden;
        }
        .report-table th {
            background-color: #1a237e;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            padding: 12px 10px;
            border: 1px solid #1a237e;
        }
        .report-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .report-table tr:nth-child(even) {
            background-color: #f8faff;
        }
        .highlight-amount {
            font-weight: bold;
            color: #1a237e;
            font-size: 14px;
        }
        .grand-total {
            background-color: #f0f4ff;
            font-weight: bold;
            border-top: 2px solid #1a237e;
        }
        .grand-total td {
            color: #1a237e;
            font-size: 15px;
        }

        .signature-zone { font-size: 11px; color: #64748b; width: 140px; }
        .signature-box { border: 1px dashed #94a3b8; height: 45px; margin-top: 5px; border-radius: 4px; }

        .footer-signatures {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            padding: 0 30px;
        }
        .officer-box {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
        }

        .no-print-btn {
            background: #1a237e;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .no-print { text-align: center; margin-bottom: 20px; }

        .footer { width: 100%; margin-top: 35px; padding-top: 15px; border-top: 2px solid #1a237e; text-align: center; font-size: 12px; color: #555; }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="no-print-btn" onclick="window.print()">🖨️ طباعة الكشف المالي</button>
    </div>

    <div class="header">
        @if (!empty($sysLogo))
            <img src="{{ asset('storage/' . $sysLogo) }}" alt="{{ $sysName }}"
                 style="max-height: 50px; margin-bottom: 5px;">
        @endif
        <h2>{{ $org }}</h2>
        <h1>{{ $sysName }}</h1>
        <h3>كشف صرف مستحقات الأيتام الدوري</h3>
        <p>إدارة الشؤون المالية والكفالات</p>
        <div class="meta-row">
            <span>تاريخ التصدير: {{ date('Y-m-d') }}</span>
            <span>الوقت: {{ date('H:i') }}</span>
        </div>
    </div>

    <div class="meta-info">
        <table style="width: 100%; border: none;">
            <tr>
                <td><strong>عدد أشهر الصرف المستهدفة:</strong> <span style="color:#0284c7; font-weight:bold;">{{ $monthsCount }} أشهر</span></td>
                <td><strong>ملاحظات الصرف:</strong> {{ $notes ?: 'صرف دوري معتمد' }}</td>
            </tr>
        </table>
    </div>

    <!-- جدول البيانات والحسابات التلقائية -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">اسم اليتيم</th>
                <th style="width: 15%;">الفرع/النطاق</th>
                <th style="width: 12%;">الكفالة الشهرية</th>
                <th style="width: 10%;">الأشهر</th>
                <th style="width: 15%;">الإجمالي المستحق</th>
                <th style="width: 18%;">توقيع أو بصمة الوصي</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandTotal = 0; 
            @endphp
            @foreach($orphans as $index => $orphan)
                @php
                    // تعديل ذكي: جلب مبلغ كفالة اليتيم من جدول الكفالات، أو وضع مبلغ افتراضي (مثلاً 30000) إذا كان فارغاً
                    $monthlyAmount = $orphan->sponsorship->amount ?? $orphan->monthly_amount ?? 30000;
                    $totalOwed = $monthlyAmount * $monthsCount;
                    $grandTotal += $totalOwed;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: right; font-weight: 600;">{{ $orphan->name }}</td>
                    <td>{{ $orphan->branch->name ?? 'المركز الرئيسي' }}</td>
                    <td>{{ number_format($monthlyAmount) }}</td>
                    <td style="font-weight: bold; color: #0284c7;">{{ $monthsCount }}</td>
                    <td class="highlight-amount">{{ number_format($totalOwed) }}</td>
                    <td>
                        <div class="signature-zone">
                            <div class="signature-box"></div>
                        </div>
                    </td>
                </tr>
            @endforeach
            
            <!-- سطر الإجمالي العام للكشف بالكامل لراحة المحاسب -->
            <tr style="background-color: #f1f5f9; font-weight: bold; border-top: 2px solid #0f172a;">
                <td colspan="5" style="text-align: left; font-size: 15px;">الإجمالي العام لجميع الأيتام في الكشف:</td>
                <td class="highlight-amount" style="font-size: 16px; color: #b91c1c; background-color: #fef2f2;">
                    {{ number_format($grandTotal) }}
                </td>
                <td style="background-color: #f1f5f9;"></td>
            </tr>
        </tbody>
    </table>

    <!-- اعتمادات الإدارة واللجنة الصارفة -->
    <div class="footer-signatures">
        <div class="officer-box">
            <div>المحاسب المختص</div>
            <div style="margin-top: 30px;">__________________</div>
        </div>
        <div class="officer-box">
            <div>أمين الصندوق</div>
            <div style="margin-top: 30px;">__________________</div>
        </div>
        <div class="officer-box">
            <div>مدير إدارة الأيتام ومراجعة الصرف</div>
            <div style="margin-top: 30px;">__________________</div>
        </div>
    </div>

    <div class="footer">
        @if(!empty($mgr)) مدير المؤسسة: {{ $mgr }} @endif
        @if(!empty($mgrPhone)) | هاتف: {{ $mgrPhone }} @endif
        @if(!empty($mgrEmail)) | البريد: {{ $mgrEmail }} @endif
    </div>

</body>
</html>