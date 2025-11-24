<div class="glass-panel" x-data="{ activeTab: 'chat' }">
    <x-layouts.header>
        <a href="{{ route('chat') }}" wire:navigate class="circle-btn ghost">
            <i class="iconoir-arrow-left text-xl"></i>
        </a>
    </x-layouts.header>

    <div class="flex-1 overflow-y-auto p-6 md:p-10 pb-24">
        <div class="max-w-3xl mx-auto space-y-8">
            {{-- Header --}}
            <div class="space-y-2">
                <h1 class="text-3xl font-semibold text-neutral-900 dark:text-white">
                    {{ __('settings.title') }}
                </h1>
                <p class="text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('settings.auto_save_notice') }}
                </p>
            </div>

            {{-- Tabs --}}
            <div
                class="flex gap-2 p-1 bg-white/40 dark:bg-white/5 backdrop-blur-md rounded-2xl border border-white/50 dark:border-white/10">
                <button type="button" @click="activeTab = 'chat'" :class="activeTab === 'chat' 
                        ? 'bg-white dark:bg-white/20 text-neutral-900 dark:text-white shadow-sm' 
                        : 'text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white'"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
                    {{ __('settings.tabs.chat') }}
                </button>
                <button type="button" @click="activeTab = 'ai_providers'" :class="activeTab === 'ai_providers' 
                        ? 'bg-white dark:bg-white/20 text-neutral-900 dark:text-white shadow-sm' 
                        : 'text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white'"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
                    {{ __('settings.tabs.ai_providers') }}
                </button>
                <button type="button" @click="activeTab = 'other'" :class="activeTab === 'other' 
                        ? 'bg-white dark:bg-white/20 text-neutral-900 dark:text-white shadow-sm' 
                        : 'text-neutral-600 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white'"
                    class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
                    {{ __('settings.tabs.other') }}
                </button>
            </div>

            {{-- Chat Settings Tab --}}
            <div x-show="activeTab === 'chat'" x-transition class="space-y-6">
                <div class="settings-card">
                    <label class="settings-label">
                        {{ __('settings.chat.mascot_name') }}
                    </label>
                    <input type="text" wire:model.blur="mascotName"
                        placeholder="{{ __('settings.chat.mascot_name_placeholder') }}" class="settings-input">
                </div>

                <div class="settings-card">
                    <label class="settings-label">
                        {{ __('settings.chat.user_description') }}
                    </label>
                    <input type="text" wire:model.blur="userName"
                        placeholder="{{ __('settings.chat.user_name_placeholder') }}" class="settings-input">

                    <textarea wire:model.blur="userDescription"
                        placeholder="{{ __('settings.chat.user_description_placeholder') }}" rows="3"
                        class="settings-input resize-none mt-4"></textarea>
                </div>

                <div class="settings-card">
                    <label class="settings-label">
                        {{ __('settings.chat.response_detail') }}
                    </label>
                    <div class="flex gap-3">
                        <label class="settings-radio-card">
                            <input type="radio" wire:model.live="responseDetail" value="detailed" class="sr-only">
                            <span class="settings-radio-label">
                                {{ __('settings.chat.response_detail_detailed') }}
                            </span>
                        </label>
                        <label class="settings-radio-card">
                            <input type="radio" wire:model.live="responseDetail" value="short" class="sr-only">
                            <span class="settings-radio-label">
                                {{ __('settings.chat.response_detail_short') }}
                            </span>
                        </label>
                    </div>
                </div>

                <div class="settings-card">
                    <label class="settings-label">
                        {{ __('settings.chat.response_tone') }}
                    </label>
                    <select wire:model.live="responseTone" class="settings-input">
                        <option value="basic">{{ __('settings.tones.basic') }}</option>
                        <option value="professional">{{ __('settings.tones.professional') }}</option>
                        <option value="friendly">{{ __('settings.tones.friendly') }}</option>
                        <option value="frank">{{ __('settings.tones.frank') }}</option>
                        <option value="quirky">{{ __('settings.tones.quirky') }}</option>
                        <option value="efficient">{{ __('settings.tones.efficient') }}</option>
                        <option value="nerdy">{{ __('settings.tones.nerdy') }}</option>
                        <option value="cynical">{{ __('settings.tones.cynical') }}</option>
                    </select>
                </div>

                <div class="settings-card">
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="settings-label mb-0">
                            <img src="{{ asset('images/logo-PurrAI-64.webp') }}" class="w-6 h-6 inline-block mr-2">
                            {{ __('settings.chat.respond_as_cat') }}
                        </span>
                        <button type="button" wire:click="$toggle('respondAsACat')"
                            class="settings-toggle {{ $respondAsACat ? 'active' : '' }}">
                            <span class="settings-toggle-thumb"></span>
                        </button>
                    </label>
                </div>
            </div>

            {{-- AI Providers Tab --}}
            <div x-show="activeTab === 'ai_providers'" x-transition class="space-y-6">
                <p class="text-sm text-neutral-600 dark:text-neutral-400">
                    {{ __('settings.ai_providers.description') }}
                </p>

                <div class="settings-card">
                    <label class="settings-label">
                        {{ __('settings.ai_providers.openai') }}
                    </label>
                    <input type="password" wire:model.blur="openaiKey"
                        placeholder="{{ __('settings.ai_providers.openai_placeholder') }}"
                        class="settings-input font-mono text-sm">
                </div>

                <div class="settings-card">
                    <label class="settings-label">
                        {{ __('settings.ai_providers.anthropic') }}
                    </label>
                    <input type="password" wire:model.blur="anthropicKey"
                        placeholder="{{ __('settings.ai_providers.anthropic_placeholder') }}"
                        class="settings-input font-mono text-sm">
                </div>

                <div class="settings-card">
                    <label class="settings-label">
                        {{ __('settings.ai_providers.google') }}
                    </label>
                    <input type="password" wire:model.blur="googleKey"
                        placeholder="{{ __('settings.ai_providers.google_placeholder') }}"
                        class="settings-input font-mono text-sm">
                </div>

                <div class="settings-card">
                    <label class="settings-label">
                        {{ __('settings.ai_providers.ollama') }}
                    </label>
                    <input type="text" wire:model.blur="ollamaUrl"
                        placeholder="{{ __('settings.ai_providers.ollama_placeholder') }}"
                        class="settings-input font-mono text-sm">
                </div>
            </div>

            {{-- Other Settings Tab --}}
            <div x-show="activeTab === 'other'" x-transition class="space-y-6">
                <div class="settings-card">
                    <label class="settings-label">
                        {{ __('settings.other.delete_old_messages') }}
                    </label>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-3">
                        {{ __('settings.other.delete_old_messages_description') }}
                    </p>
                    <input type="number" wire:model.blur="deleteOldMessagesDays" class="settings-input w-full sm:w-40"
                        min="0" step="1" placeholder="0" />
                    <p class="help-text">
                        {{ __('settings.other.delete_old_messages_helper') }}
                    </p>
                </div>

                <div class="settings-card">
                    <label class="settings-label">
                        {{ __('settings.other.window_opacity') }}
                    </label>
                    <p class="text-sm text-neutral-600 dark:text-neutral-400 mb-3">
                        {{ __('settings.other.window_opacity_description') }}
                    </p>
                    <div class="flex items-center gap-4">
                        <input type="range" wire:model.blur="windowOpacity" min="50" max="100" class="settings-slider">
                        <span class="text-sm font-medium text-neutral-900 dark:text-white w-12 text-right">
                            {{ $windowOpacity }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Saving Indicator --}}
    <div wire:loading
        class="fixed bottom-6 right-6 px-4 py-2 rounded-full bg-green-500 text-white text-sm font-medium shadow-lg z-50">
        {{ __('settings.saving') }}
    </div>
</div>

@script
<script>
    $wire.on('settings-saved', () => {
        // Optional: Show a brief success message
    });

    $wire.on('opacity-changed', (event) => {
        const opacity = event.opacity / 100;
        document.documentElement.style.setProperty('--window-opacity', opacity);
    });
</script>
@endscript