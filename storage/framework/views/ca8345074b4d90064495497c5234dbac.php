 <?php $__env->slot('headerActions', null, []); ?> 
    <div
        x-data="historyDropdown(<?php echo \Illuminate\Support\Js::from($conversations)->toHtml() ?>, <?php echo e($hasMorePages ? 'true' : 'false'); ?>, <?php echo \Illuminate\Support\Js::from($searchQuery)->toHtml() ?>)"
        class="flex items-center gap-2"
    >
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($conversation && $conversation->messages->count() > 0): ?>
            <span
                @click="startNewConversation()"
                x-transition
            >
                <?php if (isset($component)) { $__componentOriginal0b13408463faa13a13ad37dce6dd70f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b13408463faa13a13ad37dce6dd70f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.icon-button','data' => ['icon' => 'plus','title' => __('ui.tooltips.new_chat')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.icon-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'plus','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.tooltips.new_chat'))]); ?>
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
            </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <div class="relative">
            <?php if (isset($component)) { $__componentOriginal0b13408463faa13a13ad37dce6dd70f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0b13408463faa13a13ad37dce6dd70f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.icon-button','data' => ['@click' => 'open = !open','icon' => 'clock-rotate-right','title' => __('ui.tooltips.history')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.icon-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => 'open = !open','icon' => 'clock-rotate-right','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.tooltips.history'))]); ?>
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

            
            <div
                x-show="open"
                x-transition
                @click.away="open = false"
                @keydown.window.escape="open ? open = false : null"
                class="history-dropdown purrai-opacity-box"
            >
                
                <div class="history-mobile-header flex items-center justify-between gap-3">
                    <h3 class="history-mobile-title flex-1">
                        <?php echo e(__('chat.history_title')); ?>

                    </h3>
                    <div class="history-mobile-actions flex items-center gap-2">
                        <div
                            x-show="searchOpen"
                            x-transition
                            x-cloak
                            class="history-search-field flex items-center gap-2"
                        >
                            <?php if (isset($component)) { $__componentOriginal65bd7e7dbd93cec773ad6501ce127e46 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal65bd7e7dbd93cec773ad6501ce127e46 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.input','data' => ['type' => 'search','@keydown.escape.stop.prevent' => 'closeSearch()','autocomplete' => 'off','placeholder' => ''.e(__('chat.search_placeholder')).'','xRef' => 'historySearchInput','xModel' => 'searchTerm','class' => 'rounded-lg px-2 py-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'search','@keydown.escape.stop.prevent' => 'closeSearch()','autocomplete' => 'off','placeholder' => ''.e(__('chat.search_placeholder')).'','x-ref' => 'historySearchInput','x-model' => 'searchTerm','class' => 'rounded-lg px-2 py-1']); ?> <?php echo $__env->renderComponent(); ?>
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

                        <button
                            type="button"
                            @click="toggleSearch()"
                            :class="{ 'text-primary-500': searchOpen }"
                            class="history-mobile-btn"
                        >
                            <span class="sr-only"><?php echo e(__('chat.search_history')); ?></span>
                            <i class="iconoir-search text-lg"></i>
                        </button>

                        <button
                            type="button"
                            @click="open = false"
                            class="history-mobile-btn"
                        >
                            <span class="sr-only"><?php echo e(__('ui.cancel')); ?></span>
                            <i class="iconoir-xmark"></i>
                        </button>
                    </div>
                </div>

                <div class="history-list">
                    <template x-if="conversations.length === 0">
                        <div class="history-empty">
                            <i class="iconoir-chat-bubble-empty text-xl mb-4"></i>
                            <div>
                                <?php echo e(__('chat.no_conversations')); ?>

                            </div>
                        </div>
                    </template>

                    <template
                        x-for="conv in conversations"
                        :key="conv.id"
                    >
                        <div
                            class="history-item"
                            :data-history-item-id="conv.id"
                        >
                            <button
                                type="button"
                                @click="loadConv(conv.id)"
                                class="history-item-content"
                            >
                                <div
                                    class="history-item-title"
                                    x-text="conv.title"
                                >
                                </div>
                                <div class="history-item-meta">
                                    <span x-text="conv.created_at"></span>
                                    <span>&middot;</span>
                                    <?php echo e(__('chat.updated')); ?>:
                                    <span x-text="conv.updated_at_human"></span>
                                </div>
                            </button>
                            <button
                                type="button"
                                @click.stop="startEdit(conv.id, conv.title)"
                                :data-title="conv.title"
                                class="history-item-edit"
                            >
                                <i class="iconoir-edit-pencil"></i>
                            </button>
                        </div>
                    </template>
                </div>

                <template x-if="hasMorePages">
                    <div class="history-load-more">
                        <button
                            type="button"
                            @click="loadMore()"
                            :disabled="loadingMore"
                            class="history-load-more-btn"
                            :class="{ 'opacity-50 cursor-not-allowed': loadingMore }"
                        >
                            <span x-show="!loadingMore"><?php echo e(__('chat.load_more')); ?></span>
                            <span
                                x-show="loadingMore"
                                x-cloak
                            >
                                <?php if (isset($component)) { $__componentOriginal55e350ab9b24fff3313e622c84a115ea = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal55e350ab9b24fff3313e622c84a115ea = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.loading-icon','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.loading-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal55e350ab9b24fff3313e622c84a115ea)): ?>
<?php $attributes = $__attributesOriginal55e350ab9b24fff3313e622c84a115ea; ?>
<?php unset($__attributesOriginal55e350ab9b24fff3313e622c84a115ea); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal55e350ab9b24fff3313e622c84a115ea)): ?>
<?php $component = $__componentOriginal55e350ab9b24fff3313e622c84a115ea; ?>
<?php unset($__componentOriginal55e350ab9b24fff3313e622c84a115ea); ?>
<?php endif; ?>
                            </span>
                        </button>
                    </div>
                </template>
            </div>

            
            <div
                x-show="editModalOpen"
                x-transition.opacity
                @click="cancelEdit()"
                class="edit-modal-overlay"
            >
                <div
                    @click.stop
                    class="edit-modal-content"
                    x-transition
                >
                    <h3 class="edit-modal-title">
                        <?php echo e(__('chat.edit_title')); ?>

                    </h3>

                    <input
                        type="text"
                        x-model="editingTitle"
                        x-ref="editInput"
                        class="edit-modal-input"
                        @keydown.enter="saveEdit()"
                        @keydown.escape="cancelEdit()"
                        placeholder="<?php echo e(__('chat.title_placeholder')); ?>"
                    >

                    <div class="edit-modal-actions">
                        <button
                            type="button"
                            @click="cancelEdit()"
                            class="edit-modal-btn edit-modal-btn-cancel"
                        >
                            <?php echo e(__('ui.cancel')); ?>

                        </button>
                        <button
                            type="button"
                            @click="saveEdit()"
                            class="edit-modal-btn edit-modal-btn-confirm"
                        >
                            <?php echo e(__('ui.confirm')); ?>

                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
 <?php $__env->endSlot(); ?>

<div
    class="h-full flex flex-col"
    x-data="{
        scrollToBottom() {
                let container = document.getElementById('messages-container');
                if (container) {
                    setTimeout(() => {
                        container.scrollTo({
                            top: container.scrollHeight,
                            behavior: 'smooth'
                        });
                    }, 50);
                }
            },
            focusInput() {
                let textarea = document.querySelector('.input-field');
                if (textarea) {
                    setTimeout(() => {
                        textarea.focus();
                        this.scrollToBottom();
                    }, 150);
                }
            }
    }"
    x-init="focusInput()"
    @scroll-to-user-message.window="scrollToBottom()"
>

    
    <div
        class="chat-messages"
        id="messages-container"
    >
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($conversation && $conversation->messages->count() > 0): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $conversation->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if (isset($component)) { $__componentOriginal40e53bb428b26cb80e1cf1a2cce92533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal40e53bb428b26cb80e1cf1a2cce92533 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat.message','data' => ['role' => $message->role,'content' => $message->content,'time' => $message->created_at,'attachments' => $message->attachments]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat.message'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['role' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message->role),'content' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message->content),'time' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message->created_at),'attachments' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($message->attachments)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal40e53bb428b26cb80e1cf1a2cce92533)): ?>
<?php $attributes = $__attributesOriginal40e53bb428b26cb80e1cf1a2cce92533; ?>
<?php unset($__attributesOriginal40e53bb428b26cb80e1cf1a2cce92533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal40e53bb428b26cb80e1cf1a2cce92533)): ?>
<?php $component = $__componentOriginal40e53bb428b26cb80e1cf1a2cce92533; ?>
<?php unset($__componentOriginal40e53bb428b26cb80e1cf1a2cce92533); ?>
<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php else: ?>
            <?php if (isset($component)) { $__componentOriginal239e9076e152ac8289c94d9ab2fe3e8b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal239e9076e152ac8289c94d9ab2fe3e8b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat.welcome','data' => ['title' => __('chat.welcome_title'),'message' => __('chat.welcome_message')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat.welcome'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('chat.welcome_title')),'message' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('chat.welcome_message'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal239e9076e152ac8289c94d9ab2fe3e8b)): ?>
<?php $attributes = $__attributesOriginal239e9076e152ac8289c94d9ab2fe3e8b; ?>
<?php unset($__attributesOriginal239e9076e152ac8289c94d9ab2fe3e8b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal239e9076e152ac8289c94d9ab2fe3e8b)): ?>
<?php $component = $__componentOriginal239e9076e152ac8289c94d9ab2fe3e8b; ?>
<?php unset($__componentOriginal239e9076e152ac8289c94d9ab2fe3e8b); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isProcessing): ?>
            <div class="chat-row">
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
                <div class="space-y-2">
                    <div
                        id="streaming-response"
                        class="chat-bubble secondary prose prose-sm dark:prose-invert max-w-none"
                    >
                        <?php if (isset($component)) { $__componentOriginal55e350ab9b24fff3313e622c84a115ea = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal55e350ab9b24fff3313e622c84a115ea = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.loading-icon','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.loading-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal55e350ab9b24fff3313e622c84a115ea)): ?>
<?php $attributes = $__attributesOriginal55e350ab9b24fff3313e622c84a115ea; ?>
<?php unset($__attributesOriginal55e350ab9b24fff3313e622c84a115ea); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal55e350ab9b24fff3313e622c84a115ea)): ?>
<?php $component = $__componentOriginal55e350ab9b24fff3313e622c84a115ea; ?>
<?php unset($__componentOriginal55e350ab9b24fff3313e622c84a115ea); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="input-dock-wrapper">
        
        <?php if (isset($component)) { $__componentOriginal4ef7c878696acc1605e9f39505621539 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4ef7c878696acc1605e9f39505621539 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat.model-selector','data' => ['availableModels' => $availableModels,'selectedModel' => $selectedModel]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat.model-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['available-models' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($availableModels),'selected-model' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedModel)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4ef7c878696acc1605e9f39505621539)): ?>
<?php $attributes = $__attributesOriginal4ef7c878696acc1605e9f39505621539; ?>
<?php unset($__attributesOriginal4ef7c878696acc1605e9f39505621539); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4ef7c878696acc1605e9f39505621539)): ?>
<?php $component = $__componentOriginal4ef7c878696acc1605e9f39505621539; ?>
<?php unset($__componentOriginal4ef7c878696acc1605e9f39505621539); ?>
<?php endif; ?>

        
        <form
            wire:submit="sendMessage"
            class="purrai-opacity-box input-dock"
        >
            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','icon' => 'plus','title' => __('ui.tooltips.attach_file')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','icon' => 'plus','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.tooltips.attach_file'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>

            <div
                x-data="{
                    maxHeight: 100,
                    adjustHeight() {
                        const textarea = $refs.messageInput;
                        const container = $refs.textareaContainer;
                        if (!textarea || !container) return;
                        textarea.style.height = 'auto';
                        const newHeight = Math.min(textarea.scrollHeight, this.maxHeight);
                        textarea.style.height = newHeight + 'px';
                        container.style.height = newHeight + 'px';
                        textarea.style.overflowY = textarea.scrollHeight > this.maxHeight ? 'auto' : 'hidden';
                    },
                    syncValue() {
                        const textarea = $refs.messageInput;
                        if (!textarea) return;
                        $wire.set('message', textarea.value);
                        this.adjustHeight();
                    }
                }"
                x-init="const textarea = $refs.messageInput;
                textarea.value = $wire.message || '';
                adjustHeight();
                $watch('$wire.message', (value) => {
                    if (textarea.value !== value) {
                        textarea.value = value || '';
                        $nextTick(() => adjustHeight());
                    }
                });"
                wire:ignore
                x-ref="textareaContainer"
                class="flex-1"
            >
                <textarea
                    wire:ignore
                    x-ref="messageInput"
                    @input.debounce.500ms="syncValue()"
                    @input="adjustHeight()"
                    @change="$wire.call('saveDraft')"
                    placeholder="<?php echo e(__('chat.placeholder')); ?>"
                    rows="1"
                    maxlength="<?php echo e(config('purrai.limits.max_message_length')); ?>"
                    class="input-field"
                    @keydown.ctrl.enter="$wire.sendMessage()"
                ></textarea>
            </div>

            <div class="flex gap-1">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','icon' => 'microphone','title' => __('ui.tooltips.record_audio'),'id' => 'audio_device-button']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','icon' => 'microphone','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.tooltips.record_audio')),'id' => 'audio_device-button']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'primary','title' => __('ui.tooltips.send_message'),'id' => 'send-message-btn']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('ui.tooltips.send_message')),'id' => 'send-message-btn']); ?>
                    <i class="iconoir-arrow-up text-xl font-bold stroke-[3px]"></i>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            </div>

            
            <script>
                window
                    .speechRecognitionTranslations =
                    <?php echo \Illuminate\Support\Js::from([
    'settings' => __('chat.speech_recognition.settings'),
    'audio_device' => __('chat.speech_recognition.audio_device'),
    'default_audio_device' => __('chat.speech_recognition.default_audio_device'),
    'speech_provider' => __('chat.speech_recognition.speech_provider'),
    'auto_send' => __('settings.speech.auto_send'),
])->toHtml() ?>;
                window
                    .noiseSuppressionLevel =
                    <?php echo \Illuminate\Support\Js::from(\App\Models\Setting::get('noise_suppression_level', 'medium'))->toHtml() ?>;
                window
                    .useLocalSpeech =
                    <?php echo \Illuminate\Support\Js::from((bool) \App\Models\Setting::get('use_local_speech', true))->toHtml() ?>;
                window
                    .speechProviderOptions =
                    <?php echo \Illuminate\Support\Js::from(\App\Models\Setting::getSpeechProviderOptions())->toHtml() ?>;
                window
                    .selectedSpeechProvider =
                    <?php echo \Illuminate\Support\Js::from(\App\Models\Setting::get('speech_provider', ''))->toHtml() ?>;
                window
                    .autoSendAfterTranscription =
                    <?php echo \Illuminate\Support\Js::from((bool) \App\Models\Setting::get('auto_send_after_transcription', false))->toHtml() ?>;
            </script>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="absolute -top-8 left-4 text-red-600 dark:text-red-400 flex items-center gap-1">
                    <i class="iconoir-message-alert"></i>
                    <span class="text-xs"><?php echo e($message); ?></span>
                </div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </form>
    </div>
</div>
<?php /**PATH /home/bruno/Documents/projects/b7s/PurrAI/resources/views/livewire/chat.blade.php ENDPATH**/ ?>