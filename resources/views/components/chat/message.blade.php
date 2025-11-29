@props(['role' => 'assistant', 'content' => '', 'time' => null, 'attachments' => []])

<div class="chat-row {{ $role === 'user' ? 'user flex-col items-end' : '' }}">
    @if ($role === 'assistant')
        <x-chat.avatar type="ai" />
    @endif

    <div class="space-y-2 {{ $role === 'user' ? 'w-full max-w-2xl' : 'flex-1' }}">
        <x-chat.bubble
            :type="$role"
            :content="$content"
        >
            {{ $slot }}
        </x-chat.bubble>

        @if (count($attachments) > 0)
            <x-chat.attachments :attachments="$attachments" />
        @endif

        @if ($time && config('purrai.ui.show_timestamps', false) && $role === 'assistant')
            <div
                class="message-timestamp"
                x-data="{
                    showFull: false,
                    time: '{{ $time->diffForHumans() }}',
                    fullTime: '{{ $time->format(__('chat.date_format_full')) }}',
                    isVisible: false,
                    intervalId: null,
                    observer: null,
                    init() {
                        const observer = new IntersectionObserver((entries) => {
                            this.isVisible = entries[0].isIntersecting;
                
                            if (this.isVisible) {
                                this.time = this.formatTime('{{ $time->toIso8601String() }}');
                
                                if (!this.intervalId) {
                                    this.intervalId = setInterval(() => {
                                        this.time = this.formatTime('{{ $time->toIso8601String() }}');
                                    }, 30000);
                                }
                            } else {
                                if (this.intervalId) {
                                    clearInterval(this.intervalId);
                                    this.intervalId = null;
                                }
                            }
                        }, { threshold: 0.1 });
                
                        this.$nextTick(() => observer.observe(this.$el));
                        this.observer = observer;
                    },
                    destroy() {
                        if (this.intervalId) {
                            clearInterval(this.intervalId);
                        }
                        if (this.observer) {
                            this.observer.disconnect();
                        }
                    },
                    formatTime(timestamp) {
                        const date = new Date(timestamp);
                        const now = new Date();
                        const diffInSeconds = Math.floor((now - date) / 1000);
                
                        if (diffInSeconds < 60) return '{{ __('chat.just_now') }}';
                        if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' {{ __('chat.min_ago') }}';
                        if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' {{ __('chat.hours_ago') }}';
                        if (diffInSeconds < 604800) return Math.floor(diffInSeconds / 86400) + ' {{ __('chat.days_ago') }}';
                        return date.toLocaleDateString();
                    }
                }"
                @mouseenter="showFull = true"
                @mouseleave="showFull = false"
            >
                <span
                    x-show="!showFull"
                    x-text="time"
                    x-transition:enter.duration.500ms
                ></span>
                <span
                    x-show="showFull"
                    x-text="fullTime"
                    x-transition
                    x-cloak
                ></span>
            </div>
        @endif
    </div>

    @if ($role === 'user')
        <div class="flex justify-end gap-2 items-center select-none">
            @if ($time && config('purrai.ui.show_timestamps', false))
                <div
                    class="message-timestamp text-right"
                    x-data="{
                        showFull: false,
                        time: '{{ $time->diffForHumans() }}',
                        fullTime: '{{ $time->format(__('chat.date_format_full')) }}',
                        isVisible: false,
                        intervalId: null,
                        observer: null,
                        init() {
                            const observer = new IntersectionObserver((entries) => {
                                this.isVisible = entries[0].isIntersecting;
                    
                                if (this.isVisible) {
                                    this.time = this.formatTime('{{ $time->toIso8601String() }}');
                    
                                    if (!this.intervalId) {
                                        this.intervalId = setInterval(() => {
                                            this.time = this.formatTime('{{ $time->toIso8601String() }}');
                                        }, 30000);
                                    }
                                } else {
                                    if (this.intervalId) {
                                        clearInterval(this.intervalId);
                                        this.intervalId = null;
                                    }
                                }
                            }, { threshold: 0.1 });
                    
                            this.$nextTick(() => observer.observe(this.$el));
                            this.observer = observer;
                        },
                        destroy() {
                            if (this.intervalId) {
                                clearInterval(this.intervalId);
                            }
                            if (this.observer) {
                                this.observer.disconnect();
                            }
                        },
                        formatTime(timestamp) {
                            const date = new Date(timestamp);
                            const now = new Date();
                            const diffInSeconds = Math.floor((now - date) / 1000);
                    
                            if (diffInSeconds < 60) return '{{ __('Just now') }}';
                            if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' {{ __('min ago') }}';
                            if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' {{ __('hours ago') }}';
                            if (diffInSeconds < 604800) return Math.floor(diffInSeconds / 86400) + ' {{ __('days ago') }}';
                            return date.toLocaleDateString();
                        }
                    }"
                    @mouseenter="showFull = true"
                    @mouseleave="showFull = false"
                >
                    <span
                        x-show="!showFull"
                        x-text="time"
                        x-transition:enter.duration.500ms
                    ></span>
                    <span
                        x-show="showFull"
                        x-text="fullTime"
                        x-transition
                        x-cloak
                    ></span>
                </div>
            @endif
            <x-chat.avatar type="user" />
        </div>
    @endif
</div>
