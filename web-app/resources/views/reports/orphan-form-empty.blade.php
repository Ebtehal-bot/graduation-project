<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>استمارة تسجيل يتيم</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: #fff;
            color: #222;
            font-size: 11pt;
            line-height: 1.7;
            padding: 0;
        }

        @page {
            size: A4 portrait;
            margin: 1.5cm 1.8cm;
        }

        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }

        /* ─── PRINT BUTTON ─── */
        .no-print { text-align: center; margin-bottom: 18px; padding: 15px; }
        .btn-print {
            padding: 10px 30px; background: #333; color: #fff; border: none;
            border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;
        }

        /* ─── FORM CONTAINER ─── */
        .form-container {
            max-width: 190mm;
            margin: 0 auto;
            border: 2px solid #222;
            padding: 28px 30px 22px;
            background: #fff;
        }

        /* ─── HEADER ─── */
        .header { text-align: center; margin-bottom: 20px; }
        .header .logo-wrap { margin-bottom: 8px; }
        .header .logo-wrap img { max-height: 60px; }
        .org-name { font-size: 16pt; font-weight: bold; color: #000; margin-bottom: 2px; }
        .system-name { font-size: 12pt; color: #555; font-weight: normal; margin-bottom: 8px; }

        .form-label { font-size: 11pt; color: #555; margin-bottom: 2px; }
        .form-title { font-size: 20pt; font-weight: bold; color: #000; margin-bottom: 14px; }

        .form-meta { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .form-meta td {
            text-align: center; font-size: 11pt; padding: 4px 8px;
            border-bottom: 1px solid #ccc;
        }

        /* ─── SECTION CONTAINER ─── */
        .section {
            border: 1px solid #bbb;
            padding: 14px 16px 12px;
            margin-bottom: 22px;
        }
        .section.section-photo-wrap {
            position: relative;
            min-height: 150px;
        }

        .section-title {
            font-size: 13pt; font-weight: bold; color: #000;
            margin-bottom: 12px;
            padding-bottom: 5px;
            border-bottom: 2px solid #222;
        }

        /* ─── PHOTO BOX ─── */
        .photo-box {
            float: left;
            width: 4cm;
            height: 5cm;
            border: 2px solid #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-right: 18px;
            margin-bottom: 8px;
            margin-top: 4px;
            background: #fafafa;
        }
        .photo-box .photo-icon { font-size: 28px; color: #999; margin-bottom: 4px; }
        .photo-box .photo-label { font-size: 10pt; color: #888; }
        .photo-box .photo-dim { font-size: 8pt; color: #aaa; margin-top: 2px; }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }

        .clearfix::after { content: ""; display: table; clear: both; }

        /* ─── FIELDS TABLE (two-column) ─── */
        .fields-table { width: 100%; border-collapse: collapse; }
        .fields-table td {
            padding: 6px 4px;
            vertical-align: middle;
            font-size: 11pt;
        }
        .fields-table .fl {
            width: 28%;
            font-weight: bold;
            color: #444;
            white-space: nowrap;
        }
        .fields-table .fv {
            width: 22%;
            border-bottom: 1px solid #666;
            padding-bottom: 2px;
            color: #000;
            letter-spacing: 2px;
        }
        .fields-table .fv-wide {
            width: 72%;
        }
        .fields-table .fv-full {
            width: 72%;
            display: block;
        }

        /* ─── NOTES ─── */
        .notes-area {
            min-height: 100px;
            border: 1px dashed #999;
            padding: 12px;
            margin-top: 4px;
            background: #fcfcfc;
        }

        /* ─── SIGNATURE ─── */
        .signature-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .signature-grid td {
            text-align: center;
            padding: 6px 12px;
            vertical-align: top;
        }
        .signature-grid .sig-label {
            font-size: 11pt; font-weight: bold; color: #222;
            display: block; margin-bottom: 4px;
        }
        .signature-grid .sig-line {
            border-top: 1px solid #222;
            padding-top: 4px;
            margin-top: 32px;
            min-width: 100px;
        }

        /* ─── FOOTER ─── */
        .footer {
            width: 100%;
            margin-top: 20px;
            padding-top: 12px;
            border-top: 2px solid #333;
            text-align: center;
            font-size: 9pt;
            color: #444;
            line-height: 1.8;
        }
        .footer span { display: inline-block; }
        .footer .sep { margin: 0 6px; color: #999; }

        /* ─── UTILITY ─── */
        .mt-2 { margin-top: 8px; }
        .mb-2 { margin-bottom: 8px; }
        .gap-row { height: 8px; }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">طباعة الاستمارة</button>
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

    <div class="form-container">

        {{-- HEADER --}}
        <div class="header">
            @if (!empty($sysLogo))
                <div class="logo-wrap">
                    <img src="{{ asset('storage/' . $sysLogo) }}" alt="{{ $sysName }}">
                </div>
            @endif
            <div class="org-name">{{ $sysName }}</div>
            <div class="system-name">{{ $org }}</div>
            <div class="form-label">عنوان الاستمارة:</div>
            <div class="form-title">استمارة تسجيل يتيم</div>
            <table class="form-meta">
                <tr>
                    <td style="width: 50%;">رقم الاستمارة: ______________________</td>
                    <td style="width: 50%;">تاريخ الاستمارة: ______________________</td>
                </tr>
            </table>
        </div>

        {{-- PHOTO + SECTION 1 --}}
        <div class="section clearfix">
            <div class="photo-box">
                <div class="photo-icon">&#128444;</div>
                <div class="photo-label">مكان الصورة</div>
                <div class="photo-dim">4 × 5 سم</div>
            </div>
            <div class="section-title">أولاً: البيانات الشخصية</div>
            <table class="fields-table">
                <tr>
                    <td class="fl">اسم اليتيم:</td>
                    <td class="fv">____________________</td>
                    <td class="fl">الجنس / الجنسية:</td>
                    <td class="fv">____________________</td>
                </tr>
                <tr>
                    <td class="fl">العمر ومحل الميلاد:</td>
                    <td class="fv">____________________</td>
                    <td class="fl">العنوان الحالي:</td>
                    <td class="fv">____________________</td>
                </tr>
            </table>
        </div>

        {{-- SECTION 2 --}}
        <div class="section">
            <div class="section-title">ثانياً: التعليم والصحة</div>
            <table class="fields-table">
                <tr>
                    <td class="fl">الحالة التعليمية:</td>
                    <td class="fv">____________________</td>
                    <td class="fl">الصف الدراسي:</td>
                    <td class="fv">____________________</td>
                </tr>
                <tr>
                    <td class="fl">المدرسة:</td>
                    <td class="fv">____________________</td>
                    <td class="fl">هاتف المدرسة:</td>
                    <td class="fv">____________________</td>
                </tr>
                <tr>
                    <td class="fl">الحالة الصحية:</td>
                    <td class="fv" colspan="3">__________________________________________________</td>
                </tr>
            </table>
        </div>

        {{-- SECTION 3 --}}
        <div class="section">
            <div class="section-title">ثالثاً: بيانات الأسرة</div>
            <table class="fields-table">
                <tr>
                    <td class="fl">حالة الوالد:</td>
                    <td class="fv">____________________</td>
                    <td class="fl">سبب الوفاة:</td>
                    <td class="fv">____________________</td>
                </tr>
                <tr>
                    <td class="fl">تاريخ الوفاة:</td>
                    <td class="fv">____________________</td>
                    <td class="fl">اسم الأم:</td>
                    <td class="fv">____________________</td>
                </tr>
                <tr>
                    <td class="fl">حالة الأم:</td>
                    <td class="fv" colspan="3">__________________________________________________</td>
                </tr>
            </table>
        </div>

        {{-- SECTION 4 --}}
        <div class="section">
            <div class="section-title">رابعاً: بيانات المعيل</div>
            <table class="fields-table">
                <tr>
                    <td class="fl">اسم المعيل:</td>
                    <td class="fv">____________________</td>
                    <td class="fl">صلة القرابة:</td>
                    <td class="fv">____________________</td>
                </tr>
                <tr>
                    <td class="fl">رقم البطاقة:</td>
                    <td class="fv">____________________</td>
                    <td class="fl">رقم الهاتف:</td>
                    <td class="fv">____________________</td>
                </tr>
            </table>
        </div>

        {{-- SECTION 5: NOTES --}}
        <div class="section">
            <div class="section-title">خامساً: الملاحظات</div>
            <div class="notes-area"></div>
        </div>

        {{-- SIGNATURE --}}
        <div class="section">
            <div class="section-title">التوقيعات والاعتماد</div>
            <table class="signature-grid">
                <tr>
                    <td style="width: 33%;">
                        <span class="sig-label">توقيع الموظف:</span>
                        <div class="sig-line"></div>
                    </td>
                    <td style="width: 34%;">
                        <span class="sig-label">اعتماد الإدارة:</span>
                        <div class="sig-line"></div>
                    </td>
                    <td style="width: 33%;">
                        <span class="sig-label">التاريخ:</span>
                        <div class="sig-line"></div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- FOOTER --}}
        <div class="footer">
            @if(!empty($settings->manager)) <span>مدير المؤسسة: {{ $settings->manager }}</span> @endif
            @if(!empty($settings->phone)) <span class="sep">|</span> <span>هاتف: {{ $settings->phone }}</span> @endif
            @if(!empty($settings->email)) <span class="sep">|</span> <span>بريد: {{ $settings->email }}</span> @endif
            @if(!empty($settings->website)) <span class="sep">|</span> <span>موقع: {{ $settings->website }}</span> @endif
            @if(!empty($settings->address)) <span class="sep">|</span> <span>عنوان: {{ $settings->address }}</span> @endif
        </div>

    </div>

</body>
</html>
