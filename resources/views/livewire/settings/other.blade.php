<x-ui.form.radio-group
    :label="__('settings.other.theme_mode')"
    :description="__('settings.other.theme_mode_description')"
    :options="$themeModeOptions"
    model="themeMode"
/>

<div class="card">
    <x-ui.form.input
        type="number"
        :label="__('settings.other.delete_old_messages')"
        :description="__('settings.other.delete_old_messages_description')"
        :helpText="__('settings.other.delete_old_messages_helper')"
        model="deleteOldMessagesDays"
        class="w-full sm:w-40"
        min="0"
        step="1"
        placeholder="0"
    />
</div>

<div
    class="card"
    x-data="timezoneSelector"
>
    <x-ui.form.input
        :label="__('settings.other.timezone')"
        :description="__('settings.other.timezone_description')"
        :helpText="__('settings.other.timezone_helper')"
        model="timezone"
        :placeholder="__('settings.other.timezone_placeholder')"
        x-ref="timezoneInput"
    />
</div>

@if (!is_linux())
    <x-ui.form.toggle
        :label="__('settings.other.open_at_login')"
        :description="__('settings.other.open_at_login_description')"
        model="openAtLogin"
        :checked="$openAtLogin"
    />
@endif

<div class="card space-y-4">
    <x-ui.form.slider
        :label="__('settings.other.window_opacity')"
        :description="__('settings.other.window_opacity_description')"
        model="windowOpacity"
        min="50"
        max="100"
        :value="$windowOpacity"
        suffix="%"
    />

    <x-ui.form.slider
        :label="__('settings.other.window_blur')"
        :description="__('settings.other.window_blur_description')"
        :helpText="__('settings.other.window_blur_helper')"
        model="windowBlur"
        min="0"
        max="100"
        :value="$windowBlur"
        suffix="px"
    >
    </x-ui.form.slider>

    <x-ui.form.toggle
        :label="__('settings.other.disable_transparency_maximized')"
        :description="__('settings.other.disable_transparency_maximized_description')"
        model="disableTransparencyMaximized"
        :checked="$disableTransparencyMaximized"
        class="mt-4"
    />
</div>

{{-- Danger Zone --}}
<div class="card border-2 border-red-500/20! dark:border-red-500/30! space-y-4">
    <div class="flex items-start gap-3">
        <div class="shrink-0 w-10 h-10 rounded-lg bg-red-500/10 dark:bg-red-500/20 flex items-center justify-center">
            <i class="iconoir-warning-triangle text-red-600 dark:text-red-400 text-xl"></i>
        </div>
        <div class="flex-1">
            <h3 class="text-base font-semibold text-red-600 dark:text-red-400 mb-1">
                {{ __('settings.danger_zone.title') }}
            </h3>
            <p class="text-sm text-slate-600 dark:text-slate-400">
                {{ __('settings.danger_zone.description') }}
            </p>
        </div>
    </div>

    <div class="pt-2 border-t border-red-500/20 dark:border-red-500/30">
        <x-ui.form.toggle
            :label="__('settings.danger_zone.allow_destructive_operations')"
            :description="__('settings.danger_zone.allow_destructive_operations_description')"
            model="allowDestructiveFileOperations"
            :checked="$allowDestructiveFileOperations"
        />
    </div>
</div>
