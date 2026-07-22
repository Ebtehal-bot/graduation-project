<?php $__env->startSection('title', 'السجل الشامل لبيانات الأيتام'); ?>
<?php $__env->startSection('report_title', 'السجل الشامل لبيانات الأيتام'); ?>

<?php $__env->startSection('content'); ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>اسم اليتيم</th>
                <th>العمر</th>
                <th>الجنس</th>
                <th>المحافظة</th>
                <th>الفرع</th>
                <th>حفظ القرآن</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $records; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td><?php echo e($record->name); ?></td>
                    <td><?php echo e($record->birth_date ? \Carbon\Carbon::parse($record->birth_date)->age : '---'); ?></td>
                    <td><?php echo e($record->gender == 'male' ? 'ذكر' : ($record->gender == 'female' ? 'أنثى' : $record->gender)); ?></td>
                    <td><?php echo e($record->address_gov ?? '---'); ?></td>
                    <td><?php echo e($record->branch?->name ?? 'غير متوفر'); ?></td>
                    <td><?php echo e($record->quran_memorization ?? '---'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" style="color: #dc3545; padding: 30px; font-weight: bold;">
                        لا توجد أي بيانات أيتام مسجلة في النظام
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="stats-container">
        <div class="stat-box info-box">
            إجمالي عدد الأيتام: <?php echo e(count($records)); ?> يتيم
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('reports.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\DELL\Desktop\مشروع التخرج\orphan-system\resources\views/reports/group-orphans.blade.php ENDPATH**/ ?>