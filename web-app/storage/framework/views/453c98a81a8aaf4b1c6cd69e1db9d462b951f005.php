<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title><?php echo $__env->yieldContent('title', 'تقرير'); ?> - <?php echo e($sysName ?? \App\Models\Setting::getValue('site_name', 'منصة كفيل لرعاية وكفالة الأيتام')); ?></title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; text-align: center; padding: 25px; color: #333; background-color: #fff; margin: 0; line-height: 1.5; }
        .header { margin-bottom: 30px; border-bottom: 3px solid #1a237e; padding-bottom: 18px; }
        .header h1 { margin: 8px 0 4px; color: #1a237e; font-size: 24px; }
        .header h2 { color: #333; margin: 8px 0; font-size: 18px; }
        .header h3 { color: #666; margin: 4px 0; font-weight: normal; font-size: 15px; }
        .header h4 { color: #888; margin: 4px 0; font-weight: normal; font-size: 14px; }
        .header .report-meta { color: #666; font-size: 13px; margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; border-radius: 8px; overflow: hidden; }
        th { background-color: #1a237e; color: #fff; padding: 12px 10px; text-align: center; font-size: 14px; font-weight: bold; border: 1px solid #1a237e; }
        td { border: 1px solid #ddd; padding: 10px; text-align: center; font-size: 13px; }
        tr:nth-child(even) { background-color: #f8faff; }
        tr:hover { background-color: #eef2ff; }
        .stats-container { margin-top: 30px; display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; }
        .stat-box { padding: 18px 22px; border-radius: 8px; min-width: 220px; font-size: 16px; }
        .total-box { border: 2px solid #16a34a; background-color: #f0fdf4; color: #166534; font-weight: bold; }
        .info-box { border: 2px solid #64748b; background-color: #f8fafc; color: #334155; }
        .no-print { margin-bottom: 25px; }
        .btn-print { padding: 12px 28px; background: #1a237e; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 15px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.15); }
        .btn-print:hover { background: #283593; }
        .signature-section { margin-top: 50px; display: flex; justify-content: space-around; font-weight: bold; padding-top: 20px; }
        .signature-section p { margin: 5px 0; }
        .footer { width: 100%; margin-top: 35px; padding-top: 15px; border-top: 2px solid #1a237e; text-align: center; }
        .footer table { width: 100%; border-collapse: collapse; box-shadow: none; margin-top: 0; }
        .footer td { text-align: center !important; font-size: 12px; font-weight: bold; color: #333; line-height: 1.8; border: none; padding: 3px 5px; }
        .footer span { display: inline-block; }
        .footer .sep { margin-right: 12px; color: #1a237e; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            table { box-shadow: none; }
            th { background-color: #1a237e !important; color: #fff !important; }
        }
        <?php echo $__env->yieldContent('extra_css'); ?>
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ طباعة التقرير (PDF)</button>
        <p style="color: #888; font-size: 13px; margin-top: 6px;">سيتم إخفاء هذا الزر تلقائياً عند الطباعة</p>
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
            <img src="<?php echo e(asset('storage/' . $sysLogo)); ?>" alt="<?php echo e($sysName); ?>" style="max-height: 55px; margin-bottom: 8px;">
        <?php endif; ?>
        <h1><?php echo e($sysName); ?></h1>
        <h3><?php echo e($org); ?></h3>
        <hr style="border: 1px solid #1a237e; margin: 12px 0;">
        <h2><?php echo $__env->yieldContent('report_title'); ?></h2>
        <div class="report-meta"><?php echo $__env->yieldContent('subtitle'); ?></div>
    </div>

    <?php echo $__env->yieldContent('content'); ?>

    <div class="signature-section">
        <div style="text-align: center; border-top: 1px solid #ddd; padding-top: 10px; min-width: 180px;">
            <p style="font-size: 14px;">توقيع المسؤول المالي</p>
            <p style="margin-top: 30px;">............................</p>
        </div>
        <div style="text-align: center; border-top: 1px solid #ddd; padding-top: 10px; min-width: 180px;">
            <p style="font-size: 14px;">ختم المؤسسة</p>
            <br>
            <p>( ............................ )</p>
        </div>
    </div>

    <div class="footer">
        <table>
            <tr>
                <td>
                    <?php if(!empty($settings->manager)): ?> <span>مدير المؤسسة: <?php echo e($settings->manager); ?></span> <?php endif; ?>
                    <?php if(!empty($settings->phone)): ?> <span class="sep">| هاتف: <?php echo e($settings->phone); ?></span> <?php endif; ?>
                    <?php if(!empty($settings->email)): ?> <span class="sep">| البريد: <?php echo e($settings->email); ?></span> <?php endif; ?>
                    <?php if(!empty($settings->website)): ?> <span class="sep">| الموقع: <?php echo e($settings->website); ?></span> <?php endif; ?>
                    <?php if(!empty($settings->address)): ?> <span class="sep">| العنوان: <?php echo e($settings->address); ?></span> <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
<?php /**PATH C:\Users\DELL\Desktop\تبع براءه\orphan-system\resources\views/reports/layout.blade.php ENDPATH**/ ?>