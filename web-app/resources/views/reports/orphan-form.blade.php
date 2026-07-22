<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تقرير يتيم - {{ $orphan->name }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; margin: 0; padding: 20px; color: #333; line-height: 1.6; }
        .container { border: 2px solid #1a237e; padding: 25px; position: relative; min-height: 90vh; border-radius: 4px; }
        .header { text-align: center; border-bottom: 3px solid #1a237e; margin-bottom: 20px; padding-bottom: 15px; }
        .header h2 { color: #1a237e; margin: 5px 0; }
        .header h3 { color: #555; font-weight: normal; margin: 5px 0; }
        .header h4 { color: #333; margin: 8px 0 0; }
        .photo-box { position: absolute; top: 100px; left: 30px; width: 120px; height: 150px; border: 2px solid #e2e8f0; overflow: hidden; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }
        .info-section { margin-top: 20px; width: 72%; }
        .info-row { display: flex; margin-bottom: 12px; border-bottom: 1px dotted #ddd; padding: 6px 0; }
        .label { font-weight: bold; width: 140px; color: #1a237e; font-size: 13px; }
        .thank-you-section { margin-top: 40px; padding: 20px; border: 2px dashed #d97706; background-color: #fffbeb; border-radius: 12px; text-align: center; }
        .thank-you-title { font-weight: bold; color: #d97706; margin-bottom: 10px; font-size: 16px; }
        .message-content { font-style: italic; font-size: 1.1em; line-height: 1.8; text-align: center; color: #555; }
        .footer { margin-top: 60px; display: flex; justify-content: space-between; padding-top: 20px; border-top: 2px solid #1a237e; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .btn-print { background: #1a237e; color: white; padding: 12px 28px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 15px; }
        .btn-print:hover { background: #283593; }
        @media print { .no-print { display: none; } .container { border: 1px solid #000; } body { padding: 0; } }

    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ طباعة الكشف التفصيلي</button>
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
    @endphp
    <div class="container">
        <div class="header">
            @if (!empty($sysLogo))
                <img src="{{ public_path('storage/' . $sysLogo) }}" alt="{{ $sysName }}"
                     style="max-height: 50px; margin-bottom: 5px;">
            @endif
            <h2>{{ $sysName }}</h2>
            <h3>{{ $org }}</h3>
            <h4 style="margin: 5px 0;">كشف تفصيلي لبيانات اليتيم</h4>
        </div>

        <div class="photo-box">
            @if($orphan->photo)
                <img src="{{ asset('storage/' . $orphan->photo) }}" alt="صورة اليتيم">
            @else
                <div style="text-align: center; padding-top: 50px;">لا توجد صورة</div>
            @endif
        </div>

        <div class="info-section">
            <div class="info-row"><span class="label">اسم اليتيم:</span> <span>{{ $orphan->name }}</span></div>
            <div class="info-row"><span class="label">رقم الملف:</span> <span>{{ $orphan->file_number }}</span></div>
            <div class="info-row"><span class="label">تاريخ الميلاد:</span> <span>{{ $orphan->birth_date }}</span></div>
            <div class="info-row"><span class="label">الجنس:</span> <span>{{ $orphan->gender == 'male' ? 'ذكر' : 'أنثى' }}</span></div>
            <div class="info-row"><span class="label">الحالة التعليمية:</span> <span>{{ $orphan->education_status ?? 'غير محدد' }}</span></div>
            <div class="info-row"><span class="label">الصف الدراسي:</span> <span>{{ $orphan->academic_level ?? '---' }}</span></div>
            <div class="info-row"><span class="label">حفظ القرآن:</span> <span>{{ $orphan->quran_memorization ?? '---' }}</span></div>
            <div class="info-row"><span class="label">اسم المعيل:</span> <span>{{ $orphan->guardian_name ?? 'غير مسجل' }}</span></div>
        </div>

        <div class="thank-you-section">
            <div class="thank-you-title">💌 رسالة شكر من اليتيم إلى الكفيل:</div>
            @if($orphan->thank_you_letter)
                <div style="margin-top: 10px;">
                    <img src="{{ asset('storage/' . $orphan->thank_you_letter) }}" 
                         style="max-width: 100%; max-height: 350px; border: 1px solid #ddd; border-radius: 8px;">
                </div>
            @else
                <div class="message-content">
                    " نتوجه بخالص الشكر والتقدير لكفيلنا الكريم على عطائه المستمر. "
                </div>
            @endif
        </div>

        <div class="footer">
            <div>توقيع المسؤول المالي: ....................</div>
            <div>ختم المؤسسة الرسمي: ....................</div>
        </div>
    </div>

    <table style="width: 100%; border-top: 2px solid #333; padding-top: 10px; margin-top: 30px; direction: rtl; border-collapse: collapse;">
        <tr>
            <td style="text-align: center; font-size: 13px; font-weight: bold; color: #333;">
                @if(!empty($mgr)) مدير المؤسسة: {{ $mgr }} @endif
                @if(!empty($mgrPhone)) | هاتف: {{ $mgrPhone }} @endif
                @if(!empty($mgrEmail)) | البريد: {{ $mgrEmail }} @endif
                @if(!empty($mgrWebsite)) | الموقع: {{ $mgrWebsite }} @endif
                @if(!empty($mgrAddress)) | العنوان: {{ $mgrAddress }} @endif
            </td>
        </tr>
    </table>
</body>
</html>
