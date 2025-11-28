<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['availableModels' => [], 'selectedModel' => '']));

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

foreach (array_filter((['availableModels' => [], 'selectedModel' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $hasModels = !empty($availableModels) && collect($availableModels)->pluck('models')->flatten()->isNotEmpty();
?>

<div
    class="model-selector-container"
    x-data="{
        open: false,
        filterOpen: false,
        filterText: '',
        toggleFilter() {
            this.filterOpen = !this.filterOpen;
            if (this.filterOpen) {
                this.$nextTick(() => this.$refs.filterInput.focus());
            } else {
                this.filterText = '';
            }
        },
        closeFilter() {
            this.filterOpen = false;
            this.filterText = '';
        },
        matchesFilter(text) {
            if (!this.filterText) return true;
            return text.toLowerCase().includes(this.filterText.toLowerCase());
        }
    }"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasModels): ?>
        <div
            class="model-selector"
            @click.away="open = false"
        >
            <button
                type="button"
                @click="open = !open"
                class="model-selector-trigger"
            >
                <i class="iconoir-sparks text-sm"></i>
                <span class="model-selector-value">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedModel): ?>
                        <?php echo e(str_replace(['-', '_'], ' ', explode(':', $selectedModel)[1] ?? $selectedModel)); ?>

                    <?php else: ?>
                        <?php echo e(__('chat.model_selector.select_model')); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </span>
                <i
                    class="iconoir-nav-arrow-down text-xs transition-transform"
                    :class="{ 'rotate-180': open }"
                ></i>
            </button>

            <div
                x-show="open"
                x-transition
                class="model-selector-dropdown purrai-opacity-box"
                x-cloak
            >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $availableModels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $providerKey => $providerData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $providerIdentifier = str_replace('_config', '', $providerKey);
                    ?>
                    <div
                        class="model-selector-group"
                        x-show="[<?php echo e(implode(',', array_map(fn($m) => "'$m'", $providerData['models']))); ?>].some(model => matchesFilter(model.replace(/[-_]/g, ' ')))"
                    >
                        <div class="model-selector-group-label"><?php echo e($providerData['provider']); ?></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $providerData['models']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $model): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $fullModelName = $providerIdentifier . ':' . $model;
                            ?>
                            <button
                                type="button"
                                wire:click="$set('selectedModel', '<?php echo e($fullModelName); ?>')"
                                @click="open = false"
                                class="model-selector-option <?php echo e($selectedModel === $fullModelName ? 'active' : ''); ?>"
                                x-show="matchesFilter('<?php echo e(str_replace(['-', '_'], ' ', $model)); ?>')"
                            >
                                <span><?php echo e(str_replace(['-', '_'], ' ', $model)); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedModel === $fullModelName): ?>
                                    <i class="iconoir-check text-xs"></i>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="model-selector-footer">
                    <button
                        type="button"
                        @click="toggleFilter()"
                        class="py-3 pr-2 hover:opacity-75 shrink-0 cursor-pointer"
                        :class="{ 'opacity-100': filterOpen, 'opacity-75': !filterOpen }"
                        title="<?php echo e(__('chat.model_selector.filter_models')); ?>"
                    >
                        <i class="iconoir-search"></i>
                    </button>
                    <div
                        x-show="filterOpen"
                        x-transition
                        class="flex-1"
                        x-cloak
                    >
                        <?php if (isset($component)) { $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.input','data' => ['xRef' => 'filterInput','xModel' => 'filterText','@keydown.escape' => 'closeFilter()','type' => 'text','placeholder' => ''.e(__('chat.model_selector.filter_placeholder')).'','class' => 'py-1! px-2! text-xs w-full rounded-md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-ref' => 'filterInput','x-model' => 'filterText','@keydown.escape' => 'closeFilter()','type' => 'text','placeholder' => ''.e(__('chat.model_selector.filter_placeholder')).'','class' => 'py-1! px-2! text-xs w-full rounded-md']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $attributes = $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46)): ?>
<?php $component = $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46; ?>
<?php unset($__componentOriginal65bd7e7dbd93cec773ad6501ce127e46); ?>
<?php endif; ?>
                    </div>
                    <a
                        href="<?php echo e(route('settings')); ?>?tab=ai_providers"
                        wire:navigate
                        class="py-3 pl-2 hover:opacity-75 shrink-0"
                        title="<?php echo e(__('chat.model_selector.configure_providers')); ?>"
                    >
                        <i class="iconoir-plus-circle"></i>
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <a
            href="<?php echo e(route('settings')); ?>?tab=ai_providers"
            wire:navigate
            class="model-selector-empty"
        >
            <i class="iconoir-warning-triangle text-sm"></i>
            <span><?php echo e(__('chat.model_selector.configure_providers')); ?></span>
            <i class="iconoir-arrow-right text-xs"></i>
        </a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /home/bruno/Documents/projects/b7s/PurrAI/resources/views/components/chat/model-selector.blade.php ENDPATH**/ ?>