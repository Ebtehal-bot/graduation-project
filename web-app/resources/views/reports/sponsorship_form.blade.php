<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>استمارة كفالة يتيم</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: #fff;
            color: #222;
            font-size: 9pt;
            line-height: 1.5;
            padding: 0;
        }
        @page {
            size: A4 portrait;
            margin: 1cm 1.2cm;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }

        .no-print { text-align: center; margin-bottom: 12px; padding: 10px; }
        .btn-print {
            padding: 8px 24px; background: #333; color: #fff; border: none;
            border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold;
        }

        .form-container {
            max-width: 190mm;
            margin: 0 auto;
            border: 2px solid #222;
            padding: 14px 16px 12px;
            background: #fff;
        }

        .header-wrap {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 6px;
            min-height: 95px;
        }
        .header-right { flex: 1; text-align: right; padding-left: 8px; }
        .header-right .gov-line { font-size: 11pt; font-weight: bold; color: #000; line-height: 1.6; }
        .header-center { text-align: center; flex-shrink: 0; }
        .header-center img { max-height: 50px; }
        .header-left {
            flex-shrink: 0;
            width: 3cm;
            height: 3.8cm;
            border: 2px solid #333;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            background: #fafafa; margin-right: 8px;
        }
        .header-left .photo-icon { font-size: 20px; color: #999; margin-bottom: 2px; }
        .header-left .photo-label { font-size: 7.5pt; color: #888; }
        .header-left .photo-dim { font-size: 7pt; color: #aaa; margin-top: 2px; }
        .header-left img { width: 100%; height: 100%; object-fit: cover; }

        .form-title-row { text-align: center; margin-bottom: 6px; }
        .form-title-row .title-main { font-size: 13pt; font-weight: bold; color: #000; }
        .form-title-row .title-sub { font-size: 8pt; color: #555; }

        .date-row { text-align: left; margin-bottom: 6px; font-size: 9pt; }
        .date-row .date-label { font-weight: bold; }
        .date-row .date-val { border-bottom: 1px solid #000; padding: 0 18px; }

        .section { border: 1px solid #666; margin-bottom: 6px; }
        .section-title {
            background: #e8e8e8; padding: 2px 8px; font-weight: bold;
            font-size: 8.5pt; color: #000; border-bottom: 1px solid #666;
        }
        .section-body { padding: 3px 5px; }

        .form-table { width: 100%; border-collapse: collapse; }
        .form-table td {
            border: 1px solid #999; padding: 2px 4px;
            vertical-align: middle; font-size: 8pt;
        }
        .form-table .fl {
            font-weight: bold; color: #333; background: #f6f6f6;
            white-space: nowrap; width: 1%;
        }
        .form-table .fv { color: #000; }

        .check-group { display: inline-flex; align-items: center; gap: 2px; margin-left: 8px; }
        .check-group input[type="checkbox"] { width: 11px; height: 11px; accent-color: #333; }
        .check-group label { font-size: 8pt; white-space: nowrap; }

        .family-table { width: 100%; border-collapse: collapse; }
        .family-table th {
            border: 1px solid #666; padding: 3px 4px; background: #e8e8e8;
            font-size: 7.5pt; font-weight: bold; color: #000; text-align: center;
        }
        .family-table td {
            border: 1px solid #999; padding: 2px 4px;
            font-size: 7.5pt; text-align: center; height: 18px;
        }

        .sup-table { width: 100%; border-collapse: collapse; }
        .sup-table td {
            border: 1px solid #999; padding: 2px 5px;
            vertical-align: middle; font-size: 8pt;
        }
        .sup-table .fl { font-weight: bold; color: #333; background: #f6f6f6; width: 1%; white-space: nowrap; }

        .doc-table { width: 100%; border-collapse: collapse; }
        .doc-table td, .doc-table th {
            border: 1px solid #999; padding: 2px 4px;
            text-align: center; font-size: 7.5pt; vertical-align: middle;
        }
        .doc-table th { background: #e8e8e8; font-weight: bold; }
        .doc-table .doc-item { font-weight: bold; color: #333; }

        .sig-table { width: 100%; border-collapse: collapse; margin-top: 3px; }
        .sig-table td {
            border: 1px solid #999; padding: 5px 8px;
            vertical-align: top; text-align: center;
        }
        .sig-table .sig-label { font-weight: bold; font-size: 8.5pt; display: block; margin-bottom: 2px; }
        .sig-table .sig-field { margin-top: 16px; padding-top: 3px; border-top: 1px solid #666; font-size: 7.5pt; }
        .sig-table .field-label { font-size: 7pt; color: #555; }

        .footer {
            width: 100%; margin-top: 8px; padding-top: 5px;
            border-top: 2px solid #333; text-align: center;
            font-size: 7pt; color: #444; line-height: 1.5;
        }
        .footer .sep { margin: 0 3px; color: #999; }

        .page-break { page-break-before: always; }

        .attachments-section { margin-top: 0; }
        .attachments-section .section-title { font-size: 10pt; }
        .attachments-section .attachments-grid {
            display: flex; flex-wrap: wrap; gap: 12px;
            justify-content: center; padding: 8px 0;
        }
        .attachments-section .attachments-grid .attach-item {
            text-align: center; page-break-inside: avoid;
            break-inside: avoid; margin-bottom: 10px;
        }
        .attachments-section .attachments-grid .attach-item img {
            max-width: 100%; height: auto;
            border: 1px solid #ccc; border-radius: 4px;
        }
        .attachments-section .attachments-grid .attach-item .attach-label {
            font-size: 8pt; font-weight: bold; color: #333;
            margin-bottom: 4px; display: block;
        }
        .attachments-section .attachments-grid .attach-item iframe {
            width: 100%; height: 500px; border: 1px solid #ccc; border-radius: 4px;
        }
        .attach-page-break { page-break-after: always; }

        .attachment-placeholder {
            border: 2px dashed #bbb; padding: 30px; text-align: center;
            color: #999; font-size: 9pt; border-radius: 6px; margin: 8px 0;
        }

        .na { color: #999; }
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
        $settings = (object)[
            'manager' => $mgr, 'phone' => $mgrPhone, 'email' => $mgrEmail,
        ];
        $docTypeLabels = [
            'birth_certificate' => 'شهادة ميلاد',
            'death_certificate' => 'شهادة وفاة الوالد',
            'id_card' => 'هوية المعيل',
            'other' => 'مستند',
        ];
        $imgExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    @endphp

    <div class="form-container">

        {{-- HEADER --}}
        <div class="header-wrap">
            <div class="header-right">
                <div class="gov-line">الجمهورية اليمنية</div>
                <div class="gov-line">وزارة الشئون الاجتماعية والعمل</div>
                <div class="gov-line">{{ $org }}</div>
            </div>
            <div class="header-center">
                @if (!empty($sysLogo))
                    <img src="{{ asset('storage/' . $sysLogo) }}" alt="{{ $sysName }}">
                @endif
            </div>
            <div class="header-left">
                @if(isset($orphan) && $orphan->photo)
                    <img src="{{ asset('storage/' . $orphan->photo) }}" alt="صورة اليتيم">
                @else
                    <div class="photo-icon">&#128444;</div>
                    <div class="photo-label">صورة اليتيم</div>
                    <div class="photo-dim">4 &times; 6 سم</div>
                @endif
            </div>
        </div>

        <div class="form-title-row">
            <div class="title-main">استمارة كفالة يتيم</div>
            <div class="title-sub">Orphan Sponsorship Form</div>
        </div>

        <div class="date-row">
            <span class="date-label">التاريخ:</span>
            <span class="date-val"> / / 20   م</span>
        </div>

        {{-- 1. ORPHAN'S DETAILS --}}
        <div class="section">
            <div class="section-title">أولاً: بيانات اليتيم</div>
            <div class="section-body">
                <table class="form-table">
                    <tr>
                        <td class="fl">الاسم الأول:</td>
                        <td class="fv">{{ $orphan->name_first ?? '____________________' }}</td>
                        <td class="fl">اسم الأب:</td>
                        <td class="fv">{{ $orphan->name_father ?? '____________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">اسم الجد:</td>
                        <td class="fv">{{ $orphan->name_grandfather ?? '____________________' }}</td>
                        <td class="fl">اسم العائلة:</td>
                        <td class="fv">{{ $orphan->name_family ?? '____________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">تاريخ الميلاد:</td>
                        <td class="fv">{{ $orphan->birth_date ?? '________________' }}</td>
                        <td class="fl">الجنسية:</td>
                        <td class="fv">{{ $orphan->nationality ?? 'يمنية' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">الجنس:</td>
                        <td class="fv" colspan="3">
                            <span class="check-group">
                                <input type="checkbox"{{ isset($orphan) && $orphan->gender == 'male' ? ' checked' : '' }}>
                                <label>ذكر</label>
                            </span>
                            <span class="check-group">
                                <input type="checkbox"{{ isset($orphan) && $orphan->gender == 'female' ? ' checked' : '' }}>
                                <label>أنثى</label>
                            </span>
                        </td>
                    </tr>
                    @if(isset($orphan) && $orphan->file_number)
                    <tr>
                        <td class="fl">رقم الملف:</td>
                        <td class="fv" colspan="3">{{ $orphan->file_number }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        {{-- 2. EDUCATION --}}
        <div class="section">
            <div class="section-title">ثانياً: المستوى التعليمي لليتيم</div>
            <div class="section-body">
                <table class="form-table">
                    <tr>
                        <td class="fl">الصف الدراسي:</td>
                        <td class="fv">{{ $orphan->academic_level ?? '____________________' }}</td>
                        <td class="fl">المرحلة:</td>
                        <td class="fv">{{ $orphan->education_status ?? '____________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">المدرسة:</td>
                        <td class="fv" colspan="3">{{ $orphan->school_name ?? '________________________________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">حلقة التحفيظ:</td>
                        <td class="fv" colspan="3">{{ $orphan->quran_memorization ?? '________________________________________' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- 3. FATHER --}}
        <div class="section">
            <div class="section-title">ثالثاً: والد اليتيم</div>
            <div class="section-body">
                <table class="form-table">
                    <tr>
                        <td class="fl">تاريخ الميلاد:</td>
                        <td class="fv">{{ isset($orphan) && $orphan->father_birth_date ? $orphan->father_birth_date : '________________' }}</td>
                        <td class="fl">تاريخ الوفاة:</td>
                        <td class="fv">{{ $orphan->father_death_date ?? '________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">سبب الوفاة:</td>
                        <td class="fv" colspan="3">{{ $orphan->father_death_cause ?? '________________________________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">عدد الأبناء:</td>
                        <td class="fv">{{ isset($orphan) && $orphan->father_children_count ? $orphan->father_children_count : '______' }}</td>
                        <td class="fl">الدخل الشهري قبل الوفاة:</td>
                        <td class="fv">{{ isset($orphan) && $orphan->father_income_before ? $orphan->father_income_before : '______________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">المعاش بعد الوفاة:</td>
                        <td class="fv" colspan="3">{{ isset($orphan) && $orphan->father_pension_after ? $orphan->father_pension_after : '________________________________________' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- 4. MOTHER --}}
        <div class="section">
            <div class="section-title">رابعاً: والدة اليتيم</div>
            <div class="section-body">
                <table class="form-table">
                    <tr>
                        <td class="fl">تاريخ الميلاد:</td>
                        <td class="fv">{{ isset($orphan) && $orphan->mother_birth_date ? $orphan->mother_birth_date : '________________' }}</td>
                        <td class="fl">تاريخ الوفاة:</td>
                        <td class="fv">{{ $orphan->mother_death_date ?? '________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">الحالة الاجتماعية:</td>
                        <td class="fv">{{ isset($orphan) && $orphan->mother_social_status ? $orphan->mother_social_status : '____________________' }}</td>
                        <td class="fl">المستوى التعليمي:</td>
                        <td class="fv">{{ isset($orphan) && $orphan->mother_education ? $orphan->mother_education : '____________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">المهنة:</td>
                        <td class="fv">{{ $orphan->mother_job ?? '____________________' }}</td>
                        <td class="fl">الدخل الشهري:</td>
                        <td class="fv">{{ isset($orphan) && $orphan->mother_income ? $orphan->mother_income : '______________' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- 5. GUARDIAN --}}
        <div class="section">
            <div class="section-title">خامساً: معيل اليتيم الحالي</div>
            <div class="section-body">
                <table class="form-table">
                    <tr>
                        <td class="fl">صلة القرابة باليتيم:</td>
                        <td class="fv">{{ $orphan->guardian_relation ?? '____________________' }}</td>
                        <td class="fl">المستوى التعليمي:</td>
                        <td class="fv">{{ isset($orphan) && $orphan->guardian_education ? $orphan->guardian_education : '____________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">عدد أفراد الأسرة:</td>
                        <td class="fv">{{ isset($orphan) && $orphan->guardian_dependents ? $orphan->guardian_dependents : '______' }}</td>
                        <td class="fl">نوع العمل:</td>
                        <td class="fv">{{ isset($orphan) && $orphan->guardian_job_type ? $orphan->guardian_job_type : '____________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">الدخل الشهري:</td>
                        <td class="fv">{{ isset($orphan) && $orphan->guardian_income ? $orphan->guardian_income : '______________' }}</td>
                        <td class="fl">إعانة اليتيم:</td>
                        <td class="fv">{{ isset($orphan) && $orphan->guardian_allowance ? $orphan->guardian_allowance : '______________' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- 6. HOUSING --}}
        <div class="section">
            <div class="section-title">سادساً: إيضاحات السكن</div>
            <div class="section-body">
                <table class="form-table">
                    <tr>
                        <td class="fl">نوع السكن:</td>
                        <td class="fv" colspan="3">
                            <span class="check-group">
                                <input type="checkbox"{{ isset($orphan) && $orphan->housing_type == 'owned' ? ' checked' : '' }}>
                                <label>ملك</label>
                            </span>
                            <span class="check-group">
                                <input type="checkbox"{{ isset($orphan) && $orphan->housing_type == 'relatives' ? ' checked' : '' }}>
                                <label>ملك للأقارب</label>
                            </span>
                            <span class="check-group">
                                <input type="checkbox"{{ isset($orphan) && $orphan->housing_type == 'free' ? ' checked' : '' }}>
                                <label>مجاني</label>
                            </span>
                            <span class="check-group">
                                <input type="checkbox"{{ isset($orphan) && $orphan->housing_type == 'rented' ? ' checked' : '' }}>
                                <label>مستأجر / قيمة الإيجار:</label>
                            </span>
                            <span>{{ isset($orphan) && $orphan->housing_rent ? $orphan->housing_rent : '______' }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- 7. ADDRESS --}}
        <div class="section">
            <div class="section-title">سابعاً: عنوان اليتيم</div>
            <div class="section-body">
                <table class="form-table">
                    <tr>
                        <td class="fl">المحافظة:</td>
                        <td class="fv">{{ $orphan->address_gov ?? '____________________' }}</td>
                        <td class="fl">المديرية:</td>
                        <td class="fv">{{ $orphan->address_dist ?? '____________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">القرية / المحلة:</td>
                        <td class="fv" colspan="3">{{ $orphan->address_village ?? '________________________________________' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- 8. FAMILY MEMBERS TABLE --}}
        <div class="section">
            <div class="section-title">ثامناً: أفراد أسرة اليتيم</div>
            <div class="section-body" style="padding: 0;">
                <table class="family-table">
                    <thead>
                        <tr>
                            <th style="width: 20px;">م</th>
                            <th>الاسم</th>
                            <th>تاريخ الميلاد</th>
                            <th>صلة القرابة لليتيم</th>
                            <th>الحالة الاجتماعية</th>
                            <th>الحالة الصحية</th>
                            <th>المهنة</th>
                            <th>الدخل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $familyRows = isset($orphan) && $orphan->family_members ? json_decode($orphan->family_members, true) : []; @endphp
                        @for($i = 1; $i <= 8; $i++)
                        <tr>
                            <td>{{ $i }}</td>
                            <td>{{ $familyRows[$i-1]['name'] ?? '' }}</td>
                            <td>{{ $familyRows[$i-1]['birth_date'] ?? '' }}</td>
                            <td>{{ $familyRows[$i-1]['relation'] ?? '' }}</td>
                            <td>{{ $familyRows[$i-1]['social_status'] ?? '' }}</td>
                            <td>{{ $familyRows[$i-1]['health_status'] ?? '' }}</td>
                            <td>{{ $familyRows[$i-1]['job'] ?? '' }}</td>
                            <td>{{ $familyRows[$i-1]['income'] ?? '' }}</td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 9. DISPLACEMENT --}}
        <div class="section">
            <div class="section-title">تاسعاً: معلومات النزوح</div>
            <div class="section-body">
                <table class="sup-table">
                    <tr>
                        <td class="fl">محافظة المنشأ:</td>
                        <td>{{ isset($orphan) && $orphan->displacement_origin ? $orphan->displacement_origin : '____________________' }}</td>
                        <td class="fl">تاريخ النزوح:</td>
                        <td>{{ isset($orphan) && $orphan->displacement_date ? $orphan->displacement_date : '________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">أسباب النزوح:</td>
                        <td colspan="3">{{ isset($orphan) && $orphan->displacement_reasons ? $orphan->displacement_reasons : '________________________________________________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">احتياجات الأسرة:</td>
                        <td colspan="3">{{ isset($orphan) && $orphan->displacement_needs ? $orphan->displacement_needs : '________________________________________________________' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- 10. FAMILY HEALTH --}}
        <div class="section">
            <div class="section-title">عاشراً: الوضع الصحي للأسرة</div>
            <div class="section-body">
                <table class="sup-table">
                    <tr>
                        <td class="fl">الحالة النفسية:</td>
                        <td>{{ isset($orphan) && $orphan->health_psychological ? $orphan->health_psychological : '____________________' }}</td>
                        <td class="fl">الأسباب:</td>
                        <td>{{ isset($orphan) && $orphan->health_psychological_reasons ? $orphan->health_psychological_reasons : '____________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">الوضع الصحي للأسرة:</td>
                        <td colspan="3">{{ isset($orphan) && $orphan->health_family_status ? $orphan->health_family_status : '________________________________________' }}</td>
                    </tr>
                    <tr>
                        <td class="fl">نوع المرض:</td>
                        <td colspan="3">{{ isset($orphan) && $orphan->health_disease_type ? $orphan->health_disease_type : '________________________________________' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- 11. TRAINING NEEDS --}}
        <div class="section">
            <div class="section-title">الحادي عشر: احتياجات الأسرة من التدريبات</div>
            <div class="section-body">
                <table class="sup-table">
                    <tr>
                        <td class="fl">عدد المهتمين بالتدريب:</td>
                        <td>{{ isset($orphan) && $orphan->training_interested_count ? $orphan->training_interested_count : '______' }}</td>
                        <td class="fl">نوع التدريب المطلوب:</td>
                        <td>{{ isset($orphan) && $orphan->training_type ? $orphan->training_type : '____________________' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- 12. REQUIRED DOCUMENTS CHECKLIST --}}
        <div class="section">
            <div class="section-title">ثاني عشر: الوثائق المطلوبة</div>
            <div class="section-body" style="padding: 0;">
                <table class="doc-table">
                    <thead>
                        <tr>
                            <th>البيان</th>
                            <th>صورة 4&times;6</th>
                            <th>صورة طولية لليتيم</th>
                            <th>آخر شهادة دراسية</th>
                            <th>شهادة الوفاة</th>
                            <th>بلاغ عملياتي</th>
                            <th>حصة الورثة</th>
                            <th>بطاقة الأم</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="doc-item">توفرها</td>
                            <td><input type="checkbox"{{ isset($orphan) && $orphan->doc_photo_4x6 ? ' checked' : '' }}></td>
                            <td><input type="checkbox"{{ isset($orphan) && $orphan->doc_full_photo ? ' checked' : '' }}></td>
                            <td><input type="checkbox"{{ isset($orphan) && $orphan->doc_last_certificate ? ' checked' : '' }}></td>
                            <td><input type="checkbox"{{ isset($orphan) && $orphan->doc_death_certificate ? ' checked' : '' }}></td>
                            <td><input type="checkbox"{{ isset($orphan) && $orphan->doc_operational_report ? ' checked' : '' }}></td>
                            <td><input type="checkbox"{{ isset($orphan) && $orphan->doc_inheritance_share ? ' checked' : '' }}></td>
                            <td><input type="checkbox"{{ isset($orphan) && $orphan->doc_mother_card ? ' checked' : '' }}></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 13. SIGNATURES --}}
        <div class="section">
            <div class="section-title">التوقيعات</div>
            <div class="section-body">
                <table class="sig-table">
                    <tr>
                        <td style="width: 50%;">
                            <span class="sig-label">مندوب المديرية</span>
                            <div style="margin-top: 14px;">
                                <div class="sig-field">
                                    <span class="field-label">رقم الجوال: _________________________________</span>
                                </div>
                                <div class="sig-field">
                                    <span class="field-label">التوقيع: _________________________________</span>
                                </div>
                            </div>
                        </td>
                        <td style="width: 50%;">
                            <span class="sig-label">مندوب المحافظة</span>
                            <div style="margin-top: 14px;">
                                <div class="sig-field">
                                    <span class="field-label">رقم الجوال: _________________________________</span>
                                </div>
                                <div class="sig-field">
                                    <span class="field-label">التوقيع: _________________________________</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="footer">
            @if(!empty($settings->manager)) <span>مدير المؤسسة: {{ $settings->manager }}</span> @endif
            @if(!empty($settings->phone)) <span class="sep">|</span> <span>هاتف: {{ $settings->phone }}</span> @endif
            @if(!empty($settings->email)) <span class="sep">|</span> <span>بريد: {{ $settings->email }}</span> @endif
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         ATTACHMENTS & DOCUMENTS PRINTING (after form signatures)
         Each document gets its own page via page-break-before.
       ═══════════════════════════════════════════════════════════════ --}}

    @if(isset($orphan) && $orphan->relationLoaded('attachments') && $orphan->attachments->isNotEmpty())

        @php $attachIndex = 0; @endphp
        @foreach($orphan->attachments as $attachment)
            @php
                $filePath = $attachment->file_path;
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $isImage = in_array($ext, $imgExts);
                $isPdf = $ext === 'pdf';
                $label = $docTypeLabels[$attachment->document_type] ?? $attachment->document_type ?? 'مستند';
                $fileUrl = $filePath ? \Illuminate\Support\Facades\Storage::disk('public')->url($filePath) : null;
            @endphp

            @if($fileUrl)
                <div class="page-break attachments-section">
                    <div style="padding: 0 16px;">
                        <div style="border: 1px solid #666; margin-bottom: 6px;">
                            <div class="section-title" style="text-align: center; font-size: 10pt;">
                                {{ $label }}
                                <span style="font-weight: normal; font-size: 8pt; color: #666; margin-right: 10px;">
                                    ({{ $attachIndex + 1 }} / {{ $orphan->attachments->count() }})
                                </span>
                            </div>
                            <div style="padding: 10px; text-align: center;">
                                @if($isImage)
                                    <img src="{{ $fileUrl }}"
                                         alt="{{ $label }}"
                                         style="max-width: 100%; max-height: 90vh; border: 1px solid #ddd; border-radius: 4px;">
                                @elseif($isPdf)
                                    <iframe src="{{ $fileUrl }}"
                                            style="width: 100%; height: 90vh; border: 1px solid #ddd; border-radius: 4px;"
                                            title="{{ $label }}"></iframe>
                                @else
                                    <div style="padding: 40px; border: 2px dashed #bbb; border-radius: 6px; background: #fafafa;">
                                        <div style="font-size: 14pt; color: #999; margin-bottom: 8px;">&#128196;</div>
                                        <div style="font-size: 10pt; font-weight: bold; color: #555; margin-bottom: 6px;">{{ $label }}</div>
                                        <div style="font-size: 8pt; color: #888; margin-bottom: 12px;">({{ strtoupper($ext) }})</div>
                                        <a href="{{ $fileUrl }}" target="_blank"
                                           style="display: inline-block; padding: 6px 20px; background: #333; color: #fff; text-decoration: none; border-radius: 4px; font-size: 9pt;">
                                            فتح المستند
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @php $attachIndex++; @endphp
            @endif
        @endforeach

    @elseif(isset($orphan) && $orphan->relationLoaded('attachments') && $orphan->attachments->isEmpty())
        <div class="page-break attachments-section">
            <div style="padding: 0 16px;">
                <div class="attachment-placeholder">
                    لا توجد مرفقات مرفوعة لهذا اليتيم
                </div>
            </div>
        </div>
    @elseif(!isset($orphan))
        <div class="page-break attachments-section">
            <div style="padding: 0 16px;">
                <div class="attachment-placeholder">
                    [ سيتم عرض المرفقات بعد حفظ بيانات اليتيم ]
                </div>
            </div>
        </div>
    @endif

</body>
</html>
