@props(['type' => 'info', 'message' => '', 'duration' => 3000])

<div x-data="{
    show: false,
    message: @js($message),
    type: @js($type),
    duration: @js($duration),
    init() {
        if (this.message) {
            this.showToast(this.message, this.type, this.duration);
        }
        
        window.addEventListener('show-toast', (event) => {
            this.showToast(event.detail.message, event.detail.type || 'info', event.detail.duration || 3000);
        });
    },
    showToast(msg, toastType = 'info', dur = 3000) {
        this.message = msg;
        this.type = toastType;
        this.show = true;
        
        setTimeout(() => {
            this.show = false;
        }, dur);
    }
}" x-show="show" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4" class="toast-container" style="display: none;" x-cloak>
    <div class="toast-content" :class="{
        'toast-info': type === 'info',
        'toast-success': type === 'success',
        'toast-warning': type === 'warning',
        'toast-error': type === 'error'
    }">
        <div class="toast-icon">
            <template x-if="type === 'info'">
                <i class="iconoir-info-circle"></i>
            </template>
            <template x-if="type === 'success'">
                <i class="iconoir-check-circle"></i>
            </template>
            <template x-if="type === 'warning'">
                <i class="iconoir-warning-triangle"></i>
            </template>
            <template x-if="type === 'error'">
                <i class="iconoir-xmark-circle"></i>
            </template>
        </div>
        <div class="toast-message" x-text="message"></div>
        <button type="button" @click="show = false" class="toast-close">
            <i class="iconoir-xmark"></i>
        </button>
    </div>
</div>