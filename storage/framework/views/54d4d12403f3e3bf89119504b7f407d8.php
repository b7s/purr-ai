<header class="top-header">
    <div class="flex items-center gap-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_native() && is_mac() && !is_menubar()): ?>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('window-controls', []);

$key = 'window-controls-mac';

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3261833935-0', 'window-controls-mac');

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="purr-ai-logo <?php echo e(is_native() && is_mac() ? 'ml-2' : ''); ?>">
            <span class="w-full h-full"
                style="background: url(<?php echo e(asset('images/mascot/logo.svg')); ?>) center no-repeat; background-size: contain;"></span>
        </div>

        <span class="window-title"><?php echo e(config('app.name')); ?></span>
    </div>

    <div class="flex items-center gap-1">
        <?php echo e($slot); ?>


        <a href="<?php echo e(route('settings')); ?>" wire:navigate class="relative">
            <?php if (isset($component)) { $__componentOriginal0b13408463faa13a13ad37dce6dd70f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b13408463faa13a13ad37dce6dd70f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.icon-button','data' => ['icon' => 'settings','title' => __('ui.tooltips.settings')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.icon-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'settings','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.tooltips.settings'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0b13408463faa13a13ad37dce6dd70f7)): ?>
<?php $attributes = $__attributesOriginal0b13408463faa13a13ad37dce6dd70f7; ?>
<?php unset($__attributesOriginal0b13408463faa13a13ad37dce6dd70f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0b13408463faa13a13ad37dce6dd70f7)): ?>
<?php $component = $__componentOriginal0b13408463faa13a13ad37dce6dd70f7; ?>
<?php unset($__componentOriginal0b13408463faa13a13ad37dce6dd70f7); ?>
<?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(hasSettingsAlert()): ?>
                <span class="settings-alert-badge"></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </a>

        <?php if(is_native() && !is_mac() && !is_menubar()): ?>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('window-controls', []);

$key = 'window-controls';

$key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3261833935-1', 'window-controls');

$__html = app('livewire')->mount($__name, $__params, $key);

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</header><?php /**PATH /home/bruno/Documents/projects/b7s/PurrAI/resources/views/components/app-header.blade.php ENDPATH**/ ?>