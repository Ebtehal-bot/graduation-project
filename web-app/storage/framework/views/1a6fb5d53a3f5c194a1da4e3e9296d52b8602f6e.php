<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>استمارة تسجيل يتيم - <?php echo e($orphan->name); ?></title>
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

        .no-print { text-align: center; margin-bottom: 18px; padding: 15px; }
        .btn-print {
            padding: 10px 30px; background: #333; color: #fff; border: none;
            border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;
        }

        .form-container {
            max-width: 190mm;
            margin: 0 auto;
            border: 2px solid #222;
            padding: 28px 30px 22px;
            background: #fff;
        }

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
        .form-meta .meta-val { color: #000; font-weight: bold; }

        .section {
            border: 1px solid #bbb;
            padding: 14px 16px 12px;
            margin-bottom: 22px;
        }

        .section-title {
            font-size: 13pt; font-weight: bold; color: #000;
            margin-bottom: 12px;
            padding-bottom: 5px;
            border-bottom: 2px solid #222;
        }

        .photo-box {
            float: left;
            width: 4cm;
            height: 5cm;
            border: 2px solid #333;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-right: 18px;
            margin-bottom: 8px;
            margin-top: 4px;
            background: #fafafa;
        }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }
        .photo-box .photo-na { font-size: 10pt; color: #999; text-align: center; padding: 10px; }

        .clearfix::after { content: ""; display: table; clear: both; }

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
            color: #000;
            padding-bottom: 2px;
        }
        .fields-table .fv-wide {
            width: 72%;
        }

        .notes-area {
            min-height: 100px;
            border: 1px solid #ccc;
            padding: 12px;
            margin-top: 4px;
            background: #fcfcfc;
            font-size: 10pt;
            color: #333;
        }
        .notes-area strong { color: #222; }

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
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">طباعة الاستمارة</button>
    </div>

    <?php
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

        $na = 'غير متوفر';
        $eduMap = ['studying' => 'يدرس', 'stopped' => 'منقطع', 'not_enrolled' => 'غير ملتحق'];
    ?>

    <div class="form-container">

        
        <div class="header">
            <?php if(!empty($sysLogo)): ?>
                <div class="logo-wrap">
                    <img src="<?php echo e(asset('storage/' . $sysLogo)); ?>" alt="<?php echo e($sysName); ?>">
                </div>
            <?php endif; ?>
            <div class="org-name"><?php echo e($sysName); ?></div>
            <div class="system-name"><?php echo e($org); ?></div>
            <div class="form-label">عنوان الاستمارة:</div>
            <div class="form-title">استمارة تسجيل يتيم</div>
            <table class="form-meta">
                <tr>
                    <td style="width: 50%;">
                        رقم الاستمارة:
                        <span class="meta-val"><?php echo e($orphan->file_number ?? '______________'); ?></span>
                    </td>
                    <td style="width: 50%;">
                        تاريخ الاستمارة:
                        <span class="meta-val"><?php echo e(now()->format('Y/m/d')); ?></span>
                    </td>
                </tr>
            </table>
        </div>

        
        <div class="section clearfix">
            <div class="photo-box">
                <?php if($orphan->photo): ?>
                    <img src="<?php echo e(asset('storage/' . $orphan->photo)); ?>" alt="صورة اليتيم">
                <?php else: ?>
                    <div class="photo-na">لا توجد صورة</div>
                <?php endif; ?>
            </div>
            <div class="section-title">أولاً: البيانات الشخصية</div>
            <table class="fields-table">
                <tr>
                    <td class="fl">اسم اليتيم:</td>
                    <td class="fv"><?php echo e($orphan->name ?? $na); ?></td>
                    <td class="fl">الجنس / الجنسية:</td>
                    <td class="fv">
                        <?php echo e(isset($orphan->gender) ? ($orphan->gender == 'male' ? 'ذكر' : 'أنثى') : $na); ?>

                        /
                        <?php echo e($orphan->nationality ?? $na); ?>

                    </td>
                </tr>
                <tr>
                    <td class="fl">العمر ومحل الميلاد:</td>
                    <td class="fv">
                        <?php if($orphan->birth_date): ?>
                            <?php echo e(\Carbon\Carbon::parse($orphan->birth_date)->age); ?> سنة
                        <?php else: ?>
                            <?php echo e($na); ?>

                        <?php endif; ?>
                        <?php if($orphan->birth_place): ?>
                            - (<?php echo e($orphan->birth_place); ?>)
                        <?php endif; ?>
                    </td>
                    <td class="fl">العنوان الحالي:</td>
                    <td class="fv">
                        <?php echo e($orphan->address_gov ?? $na); ?>

                        <?php if($orphan->address_dist): ?> - <?php echo e($orphan->address_dist); ?> <?php endif; ?>
                        <?php if($orphan->address_village): ?> - <?php echo e($orphan->address_village); ?> <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        
        <div class="section">
            <div class="section-title">ثانياً: التعليم والصحة</div>
            <table class="fields-table">
                <tr>
                    <td class="fl">الحالة التعليمية:</td>
                    <td class="fv"><?php echo e(isset($orphan->education_status) ? ($eduMap[$orphan->education_status] ?? $orphan->education_status) : $na); ?></td>
                    <td class="fl">الصف الدراسي:</td>
                    <td class="fv"><?php echo e($orphan->academic_level ?? $na); ?></td>
                </tr>
                <tr>
                    <td class="fl">المدرسة:</td>
                    <td class="fv"><?php echo e($orphan->school_name ?? $na); ?></td>
                    <td class="fl">هاتف المدرسة:</td>
                    <td class="fv"><?php echo e($orphan->school_phone ?? $na); ?></td>
                </tr>
                <tr>
                    <td class="fl">الحالة الصحية:</td>
                    <td class="fv" colspan="3"><?php echo e($orphan->health_status ?? $na); ?></td>
                </tr>
            </table>
        </div>

        
        <div class="section">
            <div class="section-title">ثالثاً: بيانات الأسرة</div>
            <table class="fields-table">
                <tr>
                    <td class="fl">حالة الوالد:</td>
                    <td class="fv">
                        <?php if($orphan->father_death_cause || $orphan->father_death_date): ?>
                            متوفى
                        <?php else: ?>
                            <?php echo e($na); ?>

                        <?php endif; ?>
                    </td>
                    <td class="fl">سبب الوفاة:</td>
                    <td class="fv"><?php echo e($orphan->father_death_cause ?? $na); ?></td>
                </tr>
                <tr>
                    <td class="fl">تاريخ الوفاة:</td>
                    <td class="fv"><?php echo e($orphan->father_death_date ?? $na); ?></td>
                    <td class="fl">اسم الأم:</td>
                    <td class="fv"><?php echo e($orphan->mother_name ?? $na); ?></td>
                </tr>
                <tr>
                    <td class="fl">حالة الأم:</td>
                    <td class="fv" colspan="3">
                        <?php if($orphan->mother_status == 'alive'): ?>
                            على قيد الحياة
                        <?php elseif($orphan->mother_status == 'deceased'): ?>
                            متوفية
                        <?php else: ?>
                            <?php echo e($na); ?>

                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>

        
        <div class="section">
            <div class="section-title">رابعاً: بيانات المعيل</div>
            <table class="fields-table">
                <tr>
                    <td class="fl">اسم المعيل:</td>
                    <td class="fv"><?php echo e($orphan->guardian_name ?? $na); ?></td>
                    <td class="fl">صلة القرابة:</td>
                    <td class="fv"><?php echo e($orphan->guardian_relation ?? $na); ?></td>
                </tr>
                <tr>
                    <td class="fl">رقم البطاقة:</td>
                    <td class="fv"><?php echo e($orphan->guardian_card_id ?? $na); ?></td>
                    <td class="fl">رقم الهاتف:</td>
                    <td class="fv"><?php echo e($orphan->guardian_phone ?? $na); ?></td>
                </tr>
            </table>
        </div>

        
        <div class="section">
            <div class="section-title">خامساً: الملاحظات</div>
            <div class="notes-area">
                <?php if($orphan->talents || $orphan->quran_memorization): ?>
                    <?php if($orphan->talents): ?><div><strong>المواهب:</strong> <?php echo e($orphan->talents); ?></div><?php endif; ?>
                    <?php if($orphan->quran_memorization): ?><div><strong>حفظ القرآن:</strong> <?php echo e($orphan->quran_memorization); ?></div><?php endif; ?>
                <?php else: ?>
                    <?php echo e($na); ?>

                <?php endif; ?>
            </div>
        </div>

        
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

        
        <div class="footer">
            <?php if(!empty($settings->manager)): ?> <span>مدير المؤسسة: <?php echo e($settings->manager); ?></span> <?php endif; ?>
            <?php if(!empty($settings->phone)): ?> <span class="sep">|</span> <span>هاتف: <?php echo e($settings->phone); ?></span> <?php endif; ?>
            <?php if(!empty($settings->email)): ?> <span class="sep">|</span> <span>بريد: <?php echo e($settings->email); ?></span> <?php endif; ?>
            <?php if(!empty($settings->website)): ?> <span class="sep">|</span> <span>موقع: <?php echo e($settings->website); ?></span> <?php endif; ?>
            <?php if(!empty($settings->address)): ?> <span class="sep">|</span> <span>عنوان: <?php echo e($settings->address); ?></span> <?php endif; ?>
        </div>

    </div>

</body>
</html>
<?php /**PATH C:\Users\DELL\Desktop\تبع براءه\orphan-system\resources\views/reports/orphan-form-filled.blade.php ENDPATH**/ ?>