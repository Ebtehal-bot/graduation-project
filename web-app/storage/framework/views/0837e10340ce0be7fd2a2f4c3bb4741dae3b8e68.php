<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تقرير يتيم - <?php echo e($orphan->name); ?></title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; padding: 20px; line-height: 1.6; color: #333; }
        .form-container { border: 2px solid #1a237e; padding: 25px; position: relative; background: #fff; border-radius: 6px; }
        .header { text-align: center; border-bottom: 3px solid #1a237e; margin-bottom: 20px; padding-bottom: 15px; }
        .header h1 { color: #1a237e; margin: 6px 0; font-size: 22px; }
        .header h3 { color: #555; margin: 4px 0; font-weight: normal; font-size: 15px; }
        .header h4 { color: #333; margin: 6px 0; font-size: 16px; }
        .header p { color: #777; font-size: 13px; margin: 4px 0; }

        .photo-box {
            position: absolute; left: 25px; top: 110px;
            width: 120px; height: 150px; border: 2px solid #e2e8f0;
            display: flex; align-items: center; justify-content: center; overflow: hidden;
            border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .photo-box img { width: 100%; height: 100%; object-fit: cover; }

        .section-title {
            background: #f0f4ff; padding: 8px 14px; font-weight: bold; color: #1a237e;
            margin-top: 18px; border-right: 4px solid #1a237e; border-bottom: 1px solid #ddd;
            font-size: 15px; border-radius: 0 4px 0 0;
        }

        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table td { padding: 8px 12px; border-bottom: 1px solid #eee; vertical-align: top; }
        .label { font-weight: bold; width: 160px; color: #555; font-size: 14px; }
        .value { color: #000; font-size: 14px; }

        .thanks-section, .result-section {
            margin-top: 12px;
            border: 2px dashed #d97706;
            padding: 18px;
            text-align: center;
            page-break-inside: avoid;
            border-radius: 8px;
            background: #fffbeb;
        }
        .thanks-image, .result-image {
            max-width: 100%;
            max-height: 380px;
            margin-top: 10px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .footer-section { margin-top: 30px; display: flex; justify-content: space-between; align-items: center; padding-top: 20px; border-top: 2px solid #1a237e; }
        .footer-section div { text-align: center; font-size: 13px; }
        .org-stamp { border: 2px solid #1a237e; padding: 12px 20px; text-align: center; border-radius: 4px; color: #1a237e; font-weight: bold; font-size: 12px; }
        @media print { .no-print { display: none; } body { padding: 0; } .form-container { border: 1px solid #000; } }

    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 12px 28px; background: #1a237e; color: white; border: none; cursor: pointer; font-weight: bold; border-radius: 6px; font-size: 15px;">🖨️ طباعة تقرير الآن</button>
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
    ?>
    <div class="form-container">
        <div class="header">
            <?php if(!empty($sysLogo)): ?>
            <img src="<?php echo e(asset('storage/' . $sysLogo)); ?>" alt="<?php echo e($sysName); ?>"
                 style="max-height: 55px; margin-bottom: 6px;">
            <?php endif; ?>
            <h1><?php echo e($sysName); ?></h1>
            <h3><?php echo e($org); ?></h3>
            <h4>تقرير بيانات كفالة اليتيم</h4>
            <p>رقم الملف: <strong><?php echo e($orphan->file_number); ?></strong></p>
        </div>

        <div class="photo-box">
            <?php if($orphan->photo): ?>
                <img src="<?php echo e(asset('storage/' . $orphan->photo)); ?>">
            <?php else: ?>
                <div style="font-size: 11px; color: #999;">لا توجد صورة</div>
            <?php endif; ?>
        </div>

        <div style="width: 73%;">
            <div class="section-title">أولاً: بيانات اليتيم الشخصية</div>
            <table class="data-table">
                <tr><td class="label">اسم اليتيم:</td><td class="value"><?php echo e($orphan->name); ?></td></tr>
                <tr><td class="label">الجنس / الجنسية:</td><td class="value"><?php echo e($orphan->gender == 'male' ? 'ذكر' : 'أنثى'); ?> / <?php echo e($orphan->nationality); ?></td></tr>
                <tr><td class="label">العمر ومحل الميلاد:</td><td class="value"><?php echo e(\Carbon\Carbon::parse($orphan->birth_date)->age); ?> سنة - (<?php echo e($orphan->birth_place); ?>)</td></tr>
                <tr><td class="label">العنوان الحالي:</td><td class="value"><?php echo e($orphan->address_gov); ?> - <?php echo e($orphan->address_dist); ?> - <?php echo e($orphan->address_village); ?></td></tr>
            </table>

            <div class="section-title">ثانياً: التعليم والصحة</div>
            <table class="data-table">
                <tr><td class="label">الحالة التعليمية:</td><td class="value">
                    <?php $edu = ['studying'=>'يدرس', 'stopped'=>'منقطع', 'not_enrolled'=>'غير ملتحق']; ?>
                    <?php echo e($edu[$orphan->education_status] ?? '---'); ?> | الصف: <?php echo e($orphan->academic_level); ?>

                </td></tr>
                <tr><td class="label">المدرسة / الهاتف:</td><td class="value"><?php echo e($orphan->school_name); ?> (<?php echo e($orphan->school_phone); ?>)</td></tr>
                <tr><td class="label">الحالة الصحية:</td><td class="value"><?php echo e($orphan->health_status ?? 'سليم'); ?></td></tr>
                <tr><td class="label">حفظ القرآن:</td><td class="value"><?php echo e($orphan->quran_memorization ?? '---'); ?></td></tr>
            </table>
        </div>

        <div class="section-title">ثالثاً: بيانات الأسرة (الوالدين)</div>
        <table class="data-table">
            <tr><td class="label">وفاة الوالد:</td><td class="value">السبب: <?php echo e($orphan->father_death_cause); ?> | التاريخ: <?php echo e($orphan->father_death_date); ?></td></tr>
            <tr><td class="label">اسم الأم وحالتها:</td><td class="value"><?php echo e($orphan->mother_name); ?> (<?php echo e($orphan->mother_status == 'alive' ? 'على قيد الحياة' : 'متوفية'); ?>)</td></tr>
        </table>

        <div class="section-title">رابعاً: بيانات المعيل</div>
        <table class="data-table">
            <tr><td class="label">اسم المعيل:</td><td class="value"><?php echo e($orphan->guardian_name); ?> (صلة القرابة: <?php echo e($orphan->guardian_relation); ?>)</td></tr>
            <tr><td class="label">الهوية والهاتف:</td><td class="value">رقم البطاقة: <?php echo e($orphan->guardian_card_id); ?> | الهاتف: <?php echo e($orphan->guardian_phone); ?></td></tr>
        </table>

        <div class="section-title">خامساً: النتيجة المدرسية لليتيم</div>
        <div class="result-section">
            <?php if($orphan->academic_result): ?>
                <img src="<?php echo e(asset('storage/' . $orphan->academic_result)); ?>" class="result-image">
            <?php else: ?>
                <div style="padding: 25px; color: #999;">
                    [ لم يتم إرفاق صورة النتيجة المدرسية بعد ]
                </div>
            <?php endif; ?>
        </div>

        <div class="section-title">سادساً: رسالة شكر لفاعل الخير</div>
        <div class="thanks-section">
            <?php if($orphan->thank_you_letter): ?>
                <div style="margin-bottom: 8px; font-size: 14px; color: #92400e;">رسالة شكر بخط يد اليتيم</div>
                <img src="<?php echo e(asset('storage/' . $orphan->thank_you_letter)); ?>" class="thanks-image">
            <?php else: ?>
                <div style="padding: 25px; color: #999;">
                    [ لم يتم إرفاق صورة رسالة الشكر بخط اليد بعد ]
                </div>
            <?php endif; ?>
        </div>

        <div class="footer-section">
            <div>توقيع مسؤول الأيتام:<br><span style="display: inline-block; margin-top: 20px;">....................</span></div>
            <div>توقيع مدير الفرع:<br><span style="display: inline-block; margin-top: 20px;">....................</span></div>
            <div class="org-stamp">ختم<br>المؤسسة</div>
        </div>
    </div>

    <div style="width: 100%; margin-top: 35px; padding-top: 15px; border-top: 2px solid #1a237e; text-align: center;">
        <div style="font-size: 12px; font-weight: bold; color: #333; line-height: 1.8;">
            <?php if(!empty($settings->manager)): ?> <span style="display: inline-block;">مدير المؤسسة: <?php echo e($settings->manager); ?></span> <?php endif; ?>
            <?php if(!empty($settings->phone)): ?> <span style="display: inline-block; margin-right: 12px;">| هاتف: <?php echo e($settings->phone); ?></span> <?php endif; ?>
            <?php if(!empty($settings->email)): ?> <span style="display: inline-block; margin-right: 12px;">| البريد: <?php echo e($settings->email); ?></span> <?php endif; ?>
            <?php if(!empty($settings->website)): ?> <span style="display: inline-block; margin-right: 12px;">| الموقع: <?php echo e($settings->website); ?></span> <?php endif; ?>
            <?php if(!empty($settings->address)): ?> <span style="display: inline-block; margin-right: 12px;">| العنوان: <?php echo e($settings->address); ?></span> <?php endif; ?>
        </div>
    </div>
</body>
</html><?php /**PATH C:\Users\DELL\Desktop\مشروع التخرج\orphan-system\resources\views/reports/orphan_registration_form.blade.php ENDPATH**/ ?>