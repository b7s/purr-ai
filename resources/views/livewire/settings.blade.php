<x-slot name="headerActions">
    <a
        href="{{ getPreviousChatUrl() }}"
        wire:navigate
        class="circle-btn ghost"
    >
        <i class="iconoir-arrow-left text-xl"></i>
    </a>
</x-slot>

@php
    $tabs = [
        'chat' => ['label' => __('settings.tabs.chat')],
        'ai_providers' => ['label' => __('settings.tabs.ai_providers'), 'icon' => 'sparks'],
        'other' => ['label' => __('settings.tabs.other')],
    ];

    $responseDetailOptions = [
        'detailed' => ['label' => __('settings.chat.response_detail_detailed')],
        'short' => ['label' => __('settings.chat.response_detail_short')],
    ];

    $responseToneOptions = collect(config('purrai.response_tones'))
        ->mapWithKeys(function ($tone) {
            return [
                $tone['value'] => [
                    'icon' => $tone['icon'],
                    'label' => __($tone['label']),
                    'description' => __($tone['description']),
                    'height' => 'h-24',
                    'class' => 'flex flex-col items-center justify-center gap-1.5 h-full px-2',
                    'labelClass' => 'text-sm font-medium',
                ],
            ];
        })
        ->toArray();

    $themeModeOptions = [
        'light' => [
            'icon' => 'sun-light',
            'iconClass' => 'text-lg mr-1.5',
            'label' => __('settings.other.theme_light'),
            'class' => 'flex items-center justify-center',
        ],
        'dark' => [
            'icon' => 'half-moon',
            'iconClass' => 'text-lg mr-1.5',
            'label' => __('settings.other.theme_dark'),
            'class' => 'flex items-center justify-center',
        ],
        'automatic' => [
            'icon' => 'settings',
            'iconClass' => 'text-lg mr-1.5',
            'label' => __('settings.other.theme_automatic'),
            'class' => 'flex items-center justify-center',
        ],
    ];
@endphp

<div
    class="h-full flex flex-col overflow-y-auto"
    @keydown.escape.window="window.location.href = '{{ getPreviousChatUrl() }}'"
    tabindex="-1"
>
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
        <x-ui.form.tabs
            :tabs="$tabs"
            :active="request()->input('tab')"
        >
            {{-- Chat Settings Tab --}}
            <x-ui.form.tab-content name="chat">
                @include('livewire.settings.chat')
            </x-ui.form.tab-content>

            {{-- AI Providers Tab --}}
            <x-ui.form.tab-content name="ai_providers">
                @include('livewire.settings.ai-providers')
            </x-ui.form.tab-content>

            {{-- Other Settings Tab --}}
            <x-ui.form.tab-content name="other">
                @include('livewire.settings.other')
            </x-ui.form.tab-content>
        </x-ui.form.tabs>
    </div>

    <div class="mt-auto mb-8 text-center text-sm text-slate-500 select-none">
        Made with ❤️ and 🐱 by <button
            type="button"
            wire:click="openExternal('https://github.com/b7s')"
            class="link"
        >Bruno</button>
    </div>

    {{-- Saving Indicator --}}
    <div
        wire:loading
        class="fixed bottom-6 right-6 z-50 flex items-center gap-2 text-xs bg-slate-500/50 p-1 px-2 text-slate-50 text-center rounded-xl"
    >
        <x-ui.loading-icon />
        <span>{{ __('settings.saving') }}</span>
    </div>
</div>
