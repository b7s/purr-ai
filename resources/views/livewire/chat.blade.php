<x-slot name="headerActions">
    @include('livewire.chat.header-actions')
</x-slot>

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

    @include('livewire.chat.messages')

    <x-chat.media-modal />

    @include('livewire.chat.input-dock')
</div>
