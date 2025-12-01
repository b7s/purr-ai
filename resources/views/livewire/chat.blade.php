<x-slot name="headerActions">
    @include('livewire.chat.header-actions')
</x-slot>

<div
    class="h-full flex flex-col relative"
    x-data="{
        isDragging: false,
        dragCounter: 0,
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
        },
        handleDragEnter(e) {
            e.preventDefault();
            e.stopPropagation();
            this.dragCounter++;
            this.isDragging = true;
        },
        handleDragLeave(e) {
            e.preventDefault();
            e.stopPropagation();
            this.dragCounter--;
            if (this.dragCounter === 0) {
                this.isDragging = false;
            }
        },
        handleDragOver(e) {
            e.preventDefault();
            e.stopPropagation();
        },
        async handleDrop(e) {
            e.preventDefault();
            e.stopPropagation();
            this.isDragging = false;
            this.dragCounter = 0;
    
            const files = Array.from(e.dataTransfer?.files || []);
            if (files.length > 0) {
                $wire.uploadMultiple('pendingFiles', files, () => {
                    console.log('Files uploaded successfully');
                }, (error) => {
                    console.error('Upload error:', error);
                });
            }
        }
    }"
    x-init="focusInput()"
    @scroll-to-user-message.window="scrollToBottom()"
    @dragenter="handleDragEnter($event)"
    @dragleave="handleDragLeave($event)"
    @dragover="handleDragOver($event)"
    @drop="handleDrop($event)"
    @keydown.escape.window="isDragging = false; dragCounter = 0"
    @mouseup.window="if (isDragging && $event.buttons === 0) { isDragging = false; dragCounter = 0; }"
>

    @include('livewire.chat.messages')

    <x-chat.media-modal />

    @include('livewire.chat.input-dock')

    {{-- Drag & Drop Overlay --}}
    <div
        x-show="isDragging"
        x-transition
        class="absolute inset-0 z-50 flex items-center justify-center purrai-opacity-box bg-slate-900/80! dark:bg-slate-950/90! pointer-events-none"
        style="display: none;"
    >
        <div class="text-center">
            <i class="iconoir-cloud-upload text-6xl text-white mb-4"></i>
            <p class="text-xl font-medium text-white">{{ __('chat.drop_files_here') }}</p>
            <p class="text-sm text-slate-300 mt-2">{{ __('chat.drop_files_description') }}</p>
        </div>
    </div>
</div>
