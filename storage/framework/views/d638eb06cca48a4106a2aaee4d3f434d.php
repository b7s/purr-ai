<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title' => '', 'message' => '']));

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

foreach (array_filter((['title' => '', 'message' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="welcome-container select-none">
    <div class="purr-ai-logo welcome-logo animate-welcome-logo">
        <img src="<?php echo e(asset('images/mascot/position-out-of-screen.webp')); ?>" alt="<?php echo e(config('app.name')); ?>"
            class="w-full h-full">
    </div>
    <div>
        <h2 class="welcome-title">
            <?php echo e($title); ?>

        </h2>
        <p class="welcome-message">
            <?php echo e($message); ?>

        </p>
    </div>
</div><?php /**PATH /home/bruno/Documents/projects/b7s/PurrAI/resources/views/components/chat/welcome.blade.php ENDPATH**/ ?>