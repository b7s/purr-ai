<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['icon' => '', 'title' => '', 'type' => 'button']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['icon' => '', 'title' => '', 'type' => 'button']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<button type="<?php echo e($type); ?>" <?php echo e($attributes->merge(['class' => 'btn-icon'])); ?> <?php if($title): ?> title="<?php echo e($title); ?>" <?php endif; ?>>
    <i class="iconoir-<?php echo e($icon); ?> text-xl"></i>
</button><?php /**PATH /home/bruno/Documents/projects/b7s/PurrAI/resources/views/components/ui/icon-button.blade.php ENDPATH**/ ?>