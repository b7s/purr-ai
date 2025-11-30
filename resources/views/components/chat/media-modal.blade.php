@props(['show' => false])

<div
    x-data="{
        show: false,
        mediaUrl: '',
        mediaType: 'image',
        open(url, type) {
            this.mediaUrl = url;
            this.mediaType = type;
            this.show = true;
            document.body.style.overflow = 'hidden';
        },
        close() {
            this.show = false;
            document.body.style.overflow = '';
        }
    }"
    @open-media-modal.window="open($event.detail.url, $event.detail.type)"
    @keydown.escape.window="close()"
    x-show="show"
    x-cloak
    class="media-modal-overlay"
    @click="close()"
>
    <div
        class="media-modal-content"
        @click.stop
    >
        <button
            @click="close()"
            class="media-modal-close"
            type="button"
            aria-label="{{ __('ui.close') }}"
        >
            <i class="iconoir-xmark"></i>
        </button>

        <div class="media-modal-body">
            <template x-if="mediaType === 'image'">
                <img
                    :src="mediaUrl"
                    alt="Full size image"
                    class="media-modal-image"
                />
            </template>
        </div>
    </div>
</div>
