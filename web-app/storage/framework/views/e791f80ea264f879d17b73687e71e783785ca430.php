<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تقرير الكفيل - <?php echo e($sponsor->name); ?></title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; text-align: center; padding: 30px; color: #333; background-color: #fff; }
        .header { margin-bottom: 40px; border-bottom: 3px solid #444; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #000; font-size: 28px; }
        .header h2 { color: #555; margin: 10px 0; }
        .info-section { text-align: right; margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #fafafa; }
        .info-section .row { display: flex; flex-wrap: wrap; margin-bottom: 10px; }
        .info-section .row .label { font-weight: bold; min-width: 180px; color: #333; }
        .info-section .row .value { color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #000; padding: 15px; text-align: center; font-size: 16px; }
        th { background-color: #f8f9fa; font-weight: bold; }
        tr:nth-child(even) { background-color: #fafafa; }
        .stats-container { margin-top: 40px; display: flex; justify-content: center; gap: 20px; }
        .stat-box { padding: 20px; border-radius: 8px; min-width: 250px; font-size: 18px; }
        .total-box { border: 2px solid #28a745; background-color: #d4edda; color: #155724; font-weight: bold; }
        .info-box { border: 2px solid #6c757d; background-color: #f8f9fa; color: #495057; }
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

    <div class="header">
        <?php if(!empty($sysLogo)): ?>
            <img src="<?php echo e(asset('storage/' . $sysLogo)); ?>" alt="<?php echo e($sysName); ?>"
                 style="max-height: 50px; margin-bottom: 5px;">
        <?php endif; ?>
        <h1><?php echo e($sysName); ?></h1>
        <h3><?php echo e($org); ?></h3>
        <hr style="border: 1px solid #444; margin: 10px 0;">
        <h2>تقرير الكفيل التفصيلي</h2>
        <h3>اسم الكفيل: <?php echo e($sponsor->name); ?></h3>
    </div>

    <div class="info-section">
        <div class="row">
            <span class="label">اسم الكفيل:</span>
            <span class="value"><?php echo e($sponsor->name); ?></span>
        </div>
        <div class="row">
            <span class="label">رقم الهاتف:</span>
            <span class="value"><?php echo e($sponsor->phone); ?></span>
        </div>
        <div class="row">
            <span class="label">البريد الإلكتروني:</span>
            <span class="value"><?php echo e($sponsor->email); ?></span>
        </div>
        <?php if($sponsor->address): ?>
        <div class="row">
            <span class="label">العنوان:</span>
            <span class="value"><?php echo e($sponsor->address); ?></span>
        </div>
        <?php endif; ?>
        <div class="row">
            <span class="label">تاريخ التسجيل:</span>
            <span class="value"><?php echo e($sponsor->created_at->format('Y-m-d')); ?></span>
        </div>
        <div class="row">
            <span class="label">عدد الأيتام المكفولين:</span>
            <span class="value"><?php echo e($totalOrphans); ?></span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>اسم اليتيم</th>
                <th>الجنس</th>
                <th>العمر</th>
                <th>مبلغ الكفالة</th>
                <th>حالة الكفالة</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $orphans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orphan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($orphan->name); ?></td>
                    <td><?php echo e($orphan->gender == 'male' ? 'ذكر' : 'أنثى'); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($orphan->birth_date)->age); ?> سنة</td>
                    <td><?php echo e(number_format($orphan->sponsorship_amount)); ?> ريال</td>
                    <td>
                        <span style="color: <?php echo e($orphan->sponsorship_status == 'active' ? '#28a745' : '#dc3545'); ?>">
                            <?php echo e($orphan->sponsorship_status == 'active' ? 'نشط' : ($orphan->sponsorship_status == 'ended' ? 'منتهي' : 'غير نشط')); ?>

                        </span>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" style="color: #dc3545; padding: 30px; font-weight: bold;">
                        لا يوجد أيتام مكفولين لهذا الكفيل
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="stats-container">
        <div class="stat-box info-box">
            إجمالي الأيتام المكفولين: <?php echo e($totalOrphans); ?>

        </div>
        <div class="stat-box info-box">
            إجمالي مبلغ الكفالة الشهري: <?php echo e(number_format($totalMonthly)); ?> ريال
        </div>
        <div class="stat-box total-box">
            إجمالي مبلغ الكفالة السنوي: <?php echo e(number_format($totalAnnual)); ?> ريال
        </div>
    </div>

    <div style="margin-top: 60px; display: flex; justify-content: space-around; font-weight: bold;">
        <div style="text-align: center;">
            <p>توقيع المسؤول المالي</p>
            <p>............................</p>
        </div>
        <div style="text-align: center;">
            <p>ختم المؤسسة</p>
            <br>
            <p>( ............................ )</p>
        </div>
    </div>

    <div style="width: 100%; margin-top: 40px; padding-top: 15px; border-top: 2px solid #333; direction: rtl; text-align: center;">
        <table style="width: 100%; border-collapse: collapse; direction: rtl;">
            <tr>
                <td style="text-align: center !important; font-size: 13px; font-weight: bold; color: #222; font-family: sans-serif; line-height: 1.6; direction: rtl !important;">
                    <?php if(!empty($settings->manager)): ?> <span style="display: inline-block;">مدير المؤسسة: <?php echo e($settings->manager); ?></span> <?php endif; ?>
                    <?php if(!empty($settings->phone)): ?> <span style="display: inline-block; margin-right: 15px;">| هاتف الرسمي: <?php echo e($settings->phone); ?></span> <?php endif; ?>
                    <?php if(!empty($settings->email)): ?> <span style="display: inline-block; margin-right: 15px;">| البريد الرسمي: <?php echo e($settings->email); ?></span> <?php endif; ?>
                    <?php if(!empty($settings->website)): ?> <span style="display: inline-block; margin-right: 15px;">| الموقع الإلكتروني: <?php echo e($settings->website); ?></span> <?php endif; ?>
                    <?php if(!empty($settings->address)): ?> <span style="display: inline-block; margin-right: 15px;">| العنوان: <?php echo e($settings->address); ?></span> <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
<?php /**PATH C:\Users\DELL\Desktop\تبع براءه\orphan-system\resources\views/reports/sponsor_report.blade.php ENDPATH**/ ?>