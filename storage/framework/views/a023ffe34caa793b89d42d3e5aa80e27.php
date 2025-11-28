<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['type' => 'assistant', 'content' => '']));

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

foreach (array_filter((['type' => 'assistant', 'content' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $truncateLimit = config('purrai.limits.truncate_words', 45);
    $text = trim($content ?: $slot);
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($type === 'user'): ?>
    <div
        class="chat-bubble primary"
        x-data="{
            fullText: <?php echo e(json_encode($text)); ?>,
            expanded: false,
            wordLimit: <?php echo e($truncateLimit); ?>,
            get words() {
                return this.fullText.trim().split(/\s+/);
            },
            get needsTruncate() {
                return this.words.length > this.wordLimit;
            },
            get displayText() {
                if (!this.needsTruncate || this.expanded) {
                    return this.fullText;
                }
                return this.words.slice(0, this.wordLimit).join(' ') + '...';
            }
        }"
    >
        <span
            class="whitespace-pre-wrap"
            x-text="displayText"
        ></span>
        <button
            x-show="needsTruncate"
            @click="expanded = !expanded"
            class="text-sm opacity-70 hover:opacity-100 ml-1 cursor-pointer inline-flex items-center gap-1 select-none"
            type="button"
        >
            [
            <i class="iconoir-more-horiz-circle"></i>
            <span x-text="expanded ? '<?php echo e(__('ui.messages.see_less')); ?>' : '<?php echo e(__('ui.messages.see_more')); ?>'"></span>
            ]
        </button>
    </div>
<?php else: ?>
    <div
        class="chat-bubble secondary prose prose-sm dark:prose-invert max-w-none"
        x-data="{
            content: <?php echo e(json_encode($text)); ?>,
            rendered: '',
            init() {
                if (window.chatStream && window.chatStream.parseMarkdown) {
                    this.rendered = window.chatStream.parseMarkdown(this.content);
                } else {
                    this.rendered = this.content;
                }
            }
        }"
        x-html="rendered"
    ></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/bruno/Documents/projects/b7s/PurrAI/resources/views/components/chat/bubble.blade.php ENDPATH**/ ?>