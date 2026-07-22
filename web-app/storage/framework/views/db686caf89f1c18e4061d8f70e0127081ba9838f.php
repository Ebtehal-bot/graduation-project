<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.page','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php
        $branchWidget = \App\Filament\Widgets\BranchPerformanceWidget::class;
        $paymentWidget = \App\Filament\Widgets\PaymentChart::class;
        $statusWidget = \App\Filament\Widgets\SponsorshipStatusChart::class;
        $typeWidget = \App\Filament\Widgets\SponsorshipTypeChart::class;
        $chartWidgets = [$statusWidget, $typeWidget, $branchWidget, $paymentWidget];
    ?>

    
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php $__currentLoopData = $chartWidgets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $widget): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 dark:bg-gray-800 dark:border-gray-700 p-4
                <?php echo e($widget === $paymentWidget ? 'md:col-span-2' : ''); ?>

                <?php echo e($widget === $branchWidget ? 'md:col-span-2' : ''); ?>">
                <div class="h-full">
                    <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount($widget)->html();
} elseif ($_instance->childHasBeenRendered('l2486684310-0')) {
    $componentId = $_instance->getRenderedChildComponentId('l2486684310-0');
    $componentTag = $_instance->getRenderedChildComponentTagName('l2486684310-0');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('l2486684310-0');
} else {
    $response = \Livewire\Livewire::mount($widget);
    $html = $response->html();
    $_instance->logRenderedChild('l2486684310-0', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?><?php /**PATH C:\Users\DELL\Desktop\تبع براءه\orphan-system\resources\views/filament/pages/analytics-dashboard.blade.php ENDPATH**/ ?>