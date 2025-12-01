@if ($this->updateInfo['available'])
    <div class="card bg-blue-50 dark:bg-blue-950/30 border-blue-200 dark:border-blue-800">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <i class="iconoir-download-circle-solid text-blue-600 dark:text-blue-400 text-xl"></i>
                <div>
                    <p class="font-medium text-blue-900 dark:text-blue-100">{{ __('settings.other.update_available') }}</p>
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        {{ $this->updateInfo['current_version'] }} → {{ $this->updateInfo['new_version'] }}
                    </p>
                </div>
            </div>
            <x-ui.form.button
                type="button"
                wire:click="installUpdate"
                class="button w-12"
            >
                <i class="iconoir-download"></i>
                {{ __('settings.other.install_update') }}
            </x-ui.form.button>
        </div>
    </div>
@else
    <div class="card">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <i class="iconoir-check-circle-solid text-green-600 dark:text-green-400 text-xl"></i>
                <div>
                    <p class="font-medium">{{ __('settings.other.no_update_available') }}</p>
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        {{ __('settings.other.current_version') }}: {{ $this->updateInfo['current_version'] }}
                    </p>
                </div>
            </div>
            <button
                type="button"
                wire:click="checkForPurrAiAppUpdate"
                class="button w-12"
            >
                <i class="iconoir-refresh text-xl"></i>
            </button>
        </div>
    </div>
@endif
