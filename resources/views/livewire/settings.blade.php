<x-slot name="headerActions">
    <a href="{{ getPreviousChatUrl() }}" wire:navigate class="circle-btn ghost">
        <i class="iconoir-arrow-left text-xl"></i>
    </a>
</x-slot>

<div class="h-full flex flex-col overflow-y-auto" x-data="{ activeTab: 'chat' }"
    @keydown.escape.window="window.location.href = '{{ getPreviousChatUrl() }}'" tabindex="-1">
    <div class="w-full max-w-4xl mx-auto px-6 md:px-10 py-6 md:py-10 pb-24 space-y-8">
        {{-- Header --}}
        <div class="space-y-2">
            <h1 class="settings-title">
                {{ __('settings.title') }}
            </h1>
            <p class="settings-description">
                {{ __('settings.auto_save_notice') }}
            </p>
        </div>

        {{-- Tabs --}}
        <div class="settings-tabs">
            <button type="button" @click="activeTab = 'chat'"
                :class="activeTab === 'chat' ? 'settings-tab-active' : 'settings-tab-inactive'" class="settings-tab">
                {{ __('settings.tabs.chat') }}
            </button>
            <button type="button" @click="activeTab = 'ai_providers'"
                :class="activeTab === 'ai_providers' ? 'settings-tab-active' : 'settings-tab-inactive'"
                class="settings-tab">
                {{ __('settings.tabs.ai_providers') }}
            </button>
            <button type="button" @click="activeTab = 'other'"
                :class="activeTab === 'other' ? 'settings-tab-active' : 'settings-tab-inactive'" class="settings-tab">
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
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @foreach (config('purrai.response_tones') as $tone)
                        <label class="settings-radio-card h-24">
                            <input type="radio" wire:model.live="responseTone" value="{{ $tone['value'] }}" class="sr-only">
                            <span
                                class="settings-radio-label flex flex-col items-center justify-center gap-1.5 h-full px-2">
                                <i class="iconoir-{{ $tone['icon'] }} text-xl"></i>
                                <span class="text-sm font-medium">{{ __($tone['label']) }}</span>
                                <span
                                    class="text-xs opacity-70 text-center leading-tight">{{ __($tone['description']) }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
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
        </div>

        {{-- AI Providers Tab --}}
        <div x-show="activeTab === 'ai_providers'" x-transition class="space-y-6">
            <p class="settings-description">
                {{ __('settings.ai_providers.description') }}
            </p>

            <div class="settings-card">
                <label class="settings-label">
                    {{ __('settings.ai_providers.openai') }}
                </label>
                <input type="password" wire:model.blur="openaiKey"
                    placeholder="{{ __('settings.ai_providers.openai_placeholder') }}"
                    class="settings-input font-mono text-sm">

                <label class="settings-label mt-4">
                    {{ __('settings.ai_providers.openai_models') }}
                </label>
                <input type="text" wire:model.blur="openaiModels"
                    placeholder="{{ __('settings.ai_providers.openai_models_placeholder') }}"
                    class="settings-input font-mono text-sm">
                <p class="help-text">
                    {{ __('settings.ai_providers.models_helper') }}
                </p>
            </div>

            <div class="settings-card">
                <label class="settings-label">
                    {{ __('settings.ai_providers.anthropic') }}
                </label>
                <input type="password" wire:model.blur="anthropicKey"
                    placeholder="{{ __('settings.ai_providers.anthropic_placeholder') }}"
                    class="settings-input font-mono text-sm">

                <label class="settings-label mt-4">
                    {{ __('settings.ai_providers.anthropic_models') }}
                </label>
                <input type="text" wire:model.blur="anthropicModels"
                    placeholder="{{ __('settings.ai_providers.anthropic_models_placeholder') }}"
                    class="settings-input font-mono text-sm">
                <p class="help-text">
                    {{ __('settings.ai_providers.models_helper') }}
                </p>
            </div>

            <div class="settings-card">
                <label class="settings-label">
                    {{ __('settings.ai_providers.google') }}
                </label>
                <input type="password" wire:model.blur="googleKey"
                    placeholder="{{ __('settings.ai_providers.google_placeholder') }}"
                    class="settings-input font-mono text-sm">

                <label class="settings-label mt-4">
                    {{ __('settings.ai_providers.google_models') }}
                </label>
                <input type="text" wire:model.blur="googleModels"
                    placeholder="{{ __('settings.ai_providers.google_models_placeholder') }}"
                    class="settings-input font-mono text-sm">
                <p class="help-text">
                    {{ __('settings.ai_providers.models_helper') }}
                </p>
            </div>

            <div class="settings-card">
                <label class="settings-label">
                    {{ __('settings.ai_providers.ollama') }}
                </label>
                <input type="text" wire:model.blur="ollamaUrl"
                    placeholder="{{ __('settings.ai_providers.ollama_placeholder') }}"
                    class="settings-input font-mono text-sm">

                <label class="settings-label mt-4">
                    {{ __('settings.ai_providers.ollama_models') }}
                </label>
                <input type="text" wire:model.blur="ollamaModels"
                    placeholder="{{ __('settings.ai_providers.ollama_models_placeholder') }}"
                    class="settings-input font-mono text-sm">
                <p class="help-text">
                    {{ __('settings.ai_providers.models_helper') }}
                </p>
            </div>
        </div>

        {{-- Other Settings Tab --}}
        <div x-show="activeTab === 'other'" x-transition class="space-y-6">
            <div class="settings-card">
                <label class="settings-label">
                    {{ __('settings.other.theme_mode') }}
                </label>
                <p class="settings-description mb-3">
                    {{ __('settings.other.theme_mode_description') }}
                </p>
                <div class="flex gap-3">
                    <label class="settings-radio-card">
                        <input type="radio" wire:model.live="themeMode" value="light" class="sr-only">
                        <span class="settings-radio-label flex items-center justify-center">
                            <i class="iconoir-sun-light text-lg mr-1.5"></i>
                            {{ __('settings.other.theme_light') }}
                        </span>
                    </label>
                    <label class="settings-radio-card">
                        <input type="radio" wire:model.live="themeMode" value="dark" class="sr-only">
                        <span class="settings-radio-label flex items-center justify-center">
                            <i class="iconoir-half-moon text-lg mr-1.5"></i>
                            {{ __('settings.other.theme_dark') }}
                        </span>
                    </label>
                    <label class="settings-radio-card">
                        <input type="radio" wire:model.live="themeMode" value="automatic" class="sr-only">
                        <span class="settings-radio-label flex items-center justify-center">
                            <i class="iconoir-settings text-lg mr-1.5"></i>
                            {{ __('settings.other.theme_automatic') }}
                        </span>
                    </label>
                </div>
            </div>

            <div class="settings-card">
                <label class="settings-label">
                    {{ __('settings.other.delete_old_messages') }}
                </label>
                <p class="settings-description mb-3">
                    {{ __('settings.other.delete_old_messages_description') }}
                </p>
                <input type="number" wire:model.blur="deleteOldMessagesDays" class="settings-input w-full sm:w-40"
                    min="0" step="1" placeholder="0" />
                <p class="help-text">
                    {{ __('settings.other.delete_old_messages_helper') }}
                </p>
            </div>

            @if(!is_linux())
                <div class="settings-card">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <span class="settings-label mb-0">
                                {{ __('settings.other.open_at_login') }}
                            </span>
                            <p class="settings-description mt-1">
                                {{ __('settings.other.open_at_login_description') }}
                            </p>
                        </div>
                        <button type="button" wire:click="$toggle('openAtLogin')"
                            class="settings-toggle {{ $openAtLogin ? 'active' : '' }}">
                            <span class="settings-toggle-thumb"></span>
                        </button>
                    </label>
                </div>
            @endif

            <div class="settings-card">
                <label class="settings-label">
                    {{ __('settings.other.window_opacity') }}
                </label>
                <p class="settings-description mb-3">
                    {{ __('settings.other.window_opacity_description') }}
                </p>
                <div class="flex items-center gap-4">
                    <input type="range" wire:model.live.debounce.300ms="windowOpacity" min="50" max="100"
                        class="settings-slider">
                    <span class="settings-value">
                        {{ $windowOpacity }}%
                    </span>
                </div>

                <label class="settings-label mt-6">
                    {{ __('settings.other.window_blur') }}
                </label>
                <p class="settings-description mb-3">
                    {{ __('settings.other.window_blur_description') }}
                </p>
                <div class="flex items-center gap-4">
                    <input type="range" wire:model.live.debounce.300ms="windowBlur" min="0" max="100"
                        class="settings-slider">
                    <span class="settings-value">
                        {{ $windowBlur }}px
                    </span>
                </div>
                <p class="help-text">
                    {{ __('settings.other.window_blur_helper') }}
                </p>

                <label class="flex items-center justify-between cursor-pointer mt-4">
                    <div>
                        <span class="settings-label mb-0">
                            {{ __('settings.other.disable_transparency_maximized') }}
                        </span>
                        <p class="settings-description mt-1">
                            {{ __('settings.other.disable_transparency_maximized_description') }}
                        </p>
                    </div>
                    <button type="button" wire:click="$toggle('disableTransparencyMaximized')"
                        class="settings-toggle {{ $disableTransparencyMaximized ? 'active' : '' }}">
                        <span class="settings-toggle-thumb"></span>
                    </button>
                </label>
            </div>
        </div>
    </div>

    {{-- Saving Indicator --}}
    <div wire:loading
        class="fixed bottom-6 right-6 px-4 py-2 rounded-full bg-green-500 text-white text-sm font-medium shadow-lg z-50">
        {{ __('settings.saving') }}
    </div>
</div>