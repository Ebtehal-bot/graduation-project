<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'allRecordsCount',
    'colspan',
    'selectedRecordsCount',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'allRecordsCount',
    'colspan',
    'selectedRecordsCount',
]); ?>
<?php foreach (array_filter(([
    'allRecordsCount',
    'colspan',
    'selectedRecordsCount',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div
    wire:key="<?php echo e($this->id); ?>.table.selection.indicator"
    x-cloak
    <?php echo e($attributes->class(['filament-tables-selection-indicator bg-primary-500/10 px-4 py-2 whitespace-nowrap text-sm'])); ?>

>
    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-support::components.loading-indicator','data' => ['xShow' => 'isLoading','class' => 'inline-block w-4 h-4 mr-3 rtl:mr-0 rtl:ml-3 text-primary-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament-support::loading-indicator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-show' => 'isLoading','class' => 'inline-block w-4 h-4 mr-3 rtl:mr-0 rtl:ml-3 text-primary-500']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>

    <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(['dark:text-white' => config('tables.dark_mode')]) ?>" x-text="window.pluralize(<?php echo \Illuminate\Support\Js::from(__('tables::table.selection_indicator.selected_count'))->toHtml() ?>, selectedRecords.length, { count: selectedRecords.length })"></span>

    <span id="<?php echo e($this->id); ?>.table.selection.indicator.record-count.<?php echo e($allRecordsCount); ?>" x-show="<?php echo e($allRecordsCount); ?> !== selectedRecords.length">
        <button x-on:click="selectAllRecords" class="text-sm font-medium text-primary-600">
            <?php echo e(__('tables::table.selection_indicator.buttons.select_all.label', ['count' => $allRecordsCount])); ?>.
        </button>
    </span>

    <span>
        <button x-on:click="deselectAllRecords" class="text-sm font-medium text-primary-600">
            <?php echo e(__('tables::table.selection_indicator.buttons.deselect_all.label')); ?>.
        </button>
    </span>
</div>
<?php /**PATH C:\Users\NEW RAM\Desktop\orphan-system\vendor\filament\tables\resources\views/components/selection-indicator.blade.php ENDPATH**/ ?>