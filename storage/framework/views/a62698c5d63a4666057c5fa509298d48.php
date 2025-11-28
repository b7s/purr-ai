<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['role' => 'assistant', 'content' => '', 'time' => null, 'attachments' => []]));

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

foreach (array_filter((['role' => 'assistant', 'content' => '', 'time' => null, 'attachments' => []]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="chat-row <?php echo e($role === 'user' ? 'user flex-col items-end' : ''); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($role === 'assistant'): ?>
        <?php if (isset($component)) { $__componentOriginalcbea3b9853f865588887f68ba68bc075 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcbea3b9853f865588887f68ba68bc075 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat.avatar','data' => ['type' => 'ai']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'ai']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcbea3b9853f865588887f68ba68bc075)): ?>
<?php $attributes = $__attributesOriginalcbea3b9853f865588887f68ba68bc075; ?>
<?php unset($__attributesOriginalcbea3b9853f865588887f68ba68bc075); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcbea3b9853f865588887f68ba68bc075)): ?>
<?php $component = $__componentOriginalcbea3b9853f865588887f68ba68bc075; ?>
<?php unset($__componentOriginalcbea3b9853f865588887f68ba68bc075); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="space-y-2 <?php echo e($role === 'user' ? 'w-full max-w-2xl' : 'flex-1'); ?>">
        <?php if (isset($component)) { $__componentOriginala7ac5648419cfa0f69580f6118e1963e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala7ac5648419cfa0f69580f6118e1963e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat.bubble','data' => ['type' => $role,'content' => $content]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat.bubble'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($role),'content' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($content)]); ?>
            <?php echo e($slot); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala7ac5648419cfa0f69580f6118e1963e)): ?>
<?php $attributes = $__attributesOriginala7ac5648419cfa0f69580f6118e1963e; ?>
<?php unset($__attributesOriginala7ac5648419cfa0f69580f6118e1963e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala7ac5648419cfa0f69580f6118e1963e)): ?>
<?php $component = $__componentOriginala7ac5648419cfa0f69580f6118e1963e; ?>
<?php unset($__componentOriginala7ac5648419cfa0f69580f6118e1963e); ?>
<?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($attachments) > 0): ?>
            <?php if (isset($component)) { $__componentOriginal3d37ba118d635b7a62218459c3e21588 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3d37ba118d635b7a62218459c3e21588 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat.attachments','data' => ['attachments' => $attachments]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat.attachments'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['attachments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($attachments)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3d37ba118d635b7a62218459c3e21588)): ?>
<?php $attributes = $__attributesOriginal3d37ba118d635b7a62218459c3e21588; ?>
<?php unset($__attributesOriginal3d37ba118d635b7a62218459c3e21588); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3d37ba118d635b7a62218459c3e21588)): ?>
<?php $component = $__componentOriginal3d37ba118d635b7a62218459c3e21588; ?>
<?php unset($__componentOriginal3d37ba118d635b7a62218459c3e21588); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($time && config('purrai.ui.show_timestamps', false) && $role === 'assistant'): ?>
            <div
                class="message-timestamp"
                x-data="{ showFull: false }"
                @mouseenter="showFull = true"
                @mouseleave="showFull = false"
            >
                <span
                    x-show="!showFull"
                    x-transition:enter.duration.500ms
                ><?php echo e($time->diffForHumans()); ?></span>
                <span
                    x-show="showFull"
                    x-transition
                    x-cloak
                ><?php echo e($time->format(__('chat.date_format_full'))); ?></span>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($role === 'user'): ?>
        <div class="flex justify-end gap-2 items-center select-none">
            <?php if($time && config('purrai.ui.show_timestamps', false)): ?>
                <div
                    class="message-timestamp text-right"
                    x-data="{ showFull: false }"
                    @mouseenter="showFull = true"
                    @mouseleave="showFull = false"
                >
                    <span
                        x-show="!showFull"
                        x-transition:enter.duration.500ms
                    ><?php echo e($time->diffForHumans()); ?></span>
                    <span
                        x-show="showFull"
                        x-transition
                        x-cloak
                    ><?php echo e($time->format(__('chat.date_format_full'))); ?></span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalcbea3b9853f865588887f68ba68bc075 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcbea3b9853f865588887f68ba68bc075 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat.avatar','data' => ['type' => 'user']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'user']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcbea3b9853f865588887f68ba68bc075)): ?>
<?php $attributes = $__attributesOriginalcbea3b9853f865588887f68ba68bc075; ?>
<?php unset($__attributesOriginalcbea3b9853f865588887f68ba68bc075); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcbea3b9853f865588887f68ba68bc075)): ?>
<?php $component = $__componentOriginalcbea3b9853f865588887f68ba68bc075; ?>
<?php unset($__componentOriginalcbea3b9853f865588887f68ba68bc075); ?>
<?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /home/bruno/Documents/projects/b7s/PurrAI/resources/views/components/chat/message.blade.php ENDPATH**/ ?>