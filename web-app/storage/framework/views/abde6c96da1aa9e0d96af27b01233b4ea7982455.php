<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.widget','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::widget'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.card.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
        <h2 class="text-xl font-bold mb-4 text-center" style="color: rgb(46, 125, 50);">
            حالة النسخ الاحتياطي
        </h2>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600 font-medium">حالة النسخ الاحتياطي:</span>
                <span class="font-semibold"><?php echo e($status); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 font-medium">آخر تاريخ للنسخ الاحتياطي:</span>
                <span><?php echo e($lastBackup); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 font-medium">تاريخ النسخ الاحتياطي القادم:</span>
                <span><?php echo e($nextBackup); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 font-medium">وجهة التخزين:</span>
                <span><?php echo e($storage); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 font-medium">حجم النسخة الاحتياطية:</span>
                <span><?php echo e($size); ?></span>
            </div>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
<?php /**PATH C:\Users\DELL\Desktop\تبع براءه\orphan-system\resources\views/filament/widgets/backup-status.blade.php ENDPATH**/ ?>