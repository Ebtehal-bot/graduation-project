<?php
    $logo = \App\Models\Setting::getValue('site_logo', '');
    $logoDark = \App\Models\Setting::getValue('appearance_logo_dark', '');
    $brandName = config('filament.brand', 'منصة كفيل لرعاية وكفالة الأيتام');
    $isDark = config('filament.dark_mode');
?>

<?php if(filled($brandName)): ?>
    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'filament-brand text-xl font-bold tracking-tight flex items-center gap-3',
        'dark:text-white' => $isDark,
    ]) ?>">
        <?php if(!empty($logo)): ?>
            <img src="<?php echo e(asset('storage/' . $logo)); ?>" alt="<?php echo e($brandName); ?>"
                 class="h-10 w-auto object-contain">
        <?php elseif(!empty($logoDark) && $isDark): ?>
            <img src="<?php echo e(asset('storage/' . $logoDark)); ?>" alt="<?php echo e($brandName); ?>"
                 class="h-10 w-auto object-contain">
        <?php else: ?>
            <?php echo e($brandName); ?>

        <?php endif; ?>
    </div>
<?php endif; ?>
<?php /**PATH C:\Users\DELL\Desktop\مشروع التخرج\orphan-system\resources\views/vendor/filament/components/brand.blade.php ENDPATH**/ ?>