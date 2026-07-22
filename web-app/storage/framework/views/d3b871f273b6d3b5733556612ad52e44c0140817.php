<?php $__env->startSection('title', 'السجل الشامل لبيانات الكفلاء'); ?>
<?php $__env->startSection('report_title', 'السجل الشامل لبيانات الكفلاء'); ?>

<?php $__env->startSection('content'); ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>اسم الكفيل</th>
                <th>رقم الهاتف</th>
                <th>البريد الإلكتروني</th>
                <th>عدد الكفالات</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $sponsors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sponsor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td><?php echo e($sponsor->name); ?></td>
                    <td><?php echo e($sponsor->phone ?? '---'); ?></td>
                    <td><?php echo e($sponsor->email ?? '---'); ?></td>
                    <td><?php echo e($sponsor->sponsorships_count ?? $sponsor->sponsorships->count()); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" style="color: #dc3545; padding: 30px; font-weight: bold;">
                        لا توجد أي بيانات كفلاء مسجلة في النظام
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="stats-container">
        <div class="stat-box info-box">
            إجمالي عدد الكفلاء: <?php echo e(count($sponsors)); ?> كفيل
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('reports.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\DELL\Desktop\مشروع التخرج\orphan-system\resources\views/reports/group-sponsors.blade.php ENDPATH**/ ?>