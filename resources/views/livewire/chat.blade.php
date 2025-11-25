<x-slot name="headerActions">
    @if($conversation)
        <x-ui.icon-button wire:click="newConversation" icon="plus" :title="__('ui.tooltips.new_chat')" />
    @endif

    <div x-data="{ 
        open: false,
        editModalOpen: false,
        editingId: null,
        editingTitle: '',
        loadConv(id) {
            @this.call('loadConversation', id);
        },
        startEdit(id, title) {
            this.editingId = id;
            this.editingTitle = title;
            this.editModalOpen = true;
            this.$nextTick(() => {
                this.$refs.editInput?.select();
            });
        },
        saveEdit() {
            if (!this.editingTitle.trim()) {
                return;
            }
            
            @this.call('saveTitleDirect', this.editingId, this.editingTitle).then(() => {
                document.querySelector(`[data-history-item-id='${this.editingId}'] .history-item-title`).textContent = this.editingTitle;
                document.querySelector(`[data-history-item-id='${this.editingId}'] .history-item-edit[data-title]`).dataset.title = this.editingTitle;
                this.editModalOpen = false;
                this.editingId = null;
                this.editingTitle = '';
            });
        },
        cancelEdit() {
            this.editModalOpen = false;
            this.editingId = null;
            this.editingTitle = '';
        },
        loadMoreConv() {
            @this.call('nextPage');
        }
    }" class="relative">
        <x-ui.icon-button @click="open = !open" icon="clock" :title="__('ui.tooltips.history')" />

        {{-- History Dropdown/Modal --}}
        <div x-show="open" x-transition @click.away="open = false" class="history-dropdown">
            {{-- Mobile Header --}}
            <div class="history-mobile-header">
                <h3 class="history-mobile-title">{{ __('chat.history_title') }}</h3>
                <button type="button" @click="open = false" class="history-mobile-close">
                    <i class="iconoir-xmark"></i>
                </button>
            </div>

            <div class="history-list">
                @forelse($conversations as $conv)
                    <div class="history-item" data-history-item-id="{{ $conv->id }}"
                        wire:key="conversation-{{ $conv->id }}">
                        <button type="button" @click="loadConv({{ $conv->id }})" class="history-item-content">
                            <div class="history-item-title">{{ $conv->title }}</div>
                            <div class="history-item-meta">
                                <span>
                                    {{ __('chat.created') }}:
                                    {{ $conv->updated_at->format(__('chat.date_format')) }}
                                    &middot;
                                    {{ __('chat.updated') }}:
                                    {{ $conv->updated_at->diffForHumans() }}
                                </span>
                            </div>
                        </button>
                        <button type="button" @click.stop="startEdit({{ $conv->id }}, $el.dataset.title)"
                            data-title="{{ $conv->title }}" class="history-item-edit">
                            <i class="iconoir-edit-pencil"></i>
                        </button>
                    </div>
                @empty
                    <div class="history-empty">
                        {{ __('chat.no_conversations') }}
                    </div>
                @endforelse
            </div>

            @if($conversations->hasMorePages())
                <div class="history-load-more">
                    <button type="button" @click="loadMoreConv()" class="history-load-more-btn">
                        {{ __('chat.load_more') }}
                    </button>
                </div>
            @endif
        </div>

        {{-- Edit Title Modal --}}
        <div x-show="editModalOpen" x-transition.opacity @click="cancelEdit()" class="edit-modal-overlay">
            <div @click.stop class="edit-modal-content" x-transition>
                <h3 class="edit-modal-title">{{ __('chat.edit_title') }}</h3>

                <input type="text" x-model="editingTitle" x-ref="editInput" class="edit-modal-input"
                    @keydown.enter="saveEdit()" @keydown.escape="cancelEdit()"
                    placeholder="{{ __('chat.title_placeholder') }}">

                <div class="edit-modal-actions">
                    <button type="button" @click="cancelEdit()" class="edit-modal-btn edit-modal-btn-cancel">
                        {{ __('ui.cancel') }}
                    </button>
                    <button type="button" @click="saveEdit()" class="edit-modal-btn edit-modal-btn-confirm">
                        {{ __('ui.confirm') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-slot>

<div class="h-full flex flex-col" x-data="{ 
    scrollToBottom() {
        let container = document.getElementById('messages-container');
        if (container) {
            setTimeout(() => container.scrollTop = container.scrollHeight, 100);
        }
    },
    focusInput() {
        let textarea = document.querySelector('.input-field');
        if (textarea) {
            setTimeout(() => textarea.focus(), 100);
        }
    }
}" x-init="focusInput()">

    {{-- Messages --}}
    <div class="chat-messages" id="messages-container">
        @if($conversation && $conversation->messages->count() > 0)
            @foreach($conversation->messages as $message)
                <x-chat.message :role="$message->role" :content="$message->content"
                    :timestamp="$message->created_at->diffForHumans()" :attachments="$message->attachments" />
            @endforeach
        @else
            <x-chat.welcome :title="__('chat.welcome_title')" :message="__('chat.welcome_message')" />
        @endif

        @if($isProcessing ?? false)
            <x-chat.loading />
        @endif
    </div>

    {{-- Input Dock --}}
    <form wire:submit="sendMessage" class="input-dock" @submit="scrollToBottom(); $nextTick(() => focusInput())">
        <x-ui.button type="button" variant="ghost" icon="plus" :title="__('ui.tooltips.attach_file')" />

        <textarea wire:model.live.debounce.1000ms="message" wire:change="saveDraft"
            placeholder="{{ __('chat.placeholder') }}" rows="1"
            maxlength="{{ config('purrai.limits.max_message_length') }}" class="input-field"
            x-on:input="$el.style.height = 'auto'; $el.style.height = ($el.scrollHeight) + 'px'"
            @keydown.ctrl.enter="$wire.sendMessage()"></textarea>

        <div class="flex gap-1">
            <x-ui.button type="button" variant="ghost" icon="microphone" :title="__('ui.tooltips.record_audio')" />
            <x-ui.button type="submit" variant="primary" :title="__('ui.tooltips.send_message')">
                <i class="iconoir-arrow-up text-xl font-bold stroke-[3px]"></i>
            </x-ui.button>
        </div>

        @error('message')
            <span class="absolute -top-8 left-4 text-xs text-red-600 dark:text-red-400">{{ $message }}</span>
        @enderror
    </form>
</div>