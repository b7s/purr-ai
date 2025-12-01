<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Native\Desktop\Facades\System;

class LocaleService
{
    public function configure(): void
    {
        $this->configureTimezone();
        $this->configureLocale();
    }

    public function configureTimezone(): void
    {
        try {
            $timezone = Setting::get('timezone');

            if (empty($timezone)) {
                $timezone = System::timezone();
            }

            if (! \in_array($timezone, timezone_identifiers_list(), true)) {
                $timezone = config('app.timezone');
            }

            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        } catch (\Exception) {
        }
    }

    public function configureLocale(): void
    {
        try {
            $locale = Setting::get('response_language');

            if (empty($locale)) {
                $locale = $this->detectPreferredLocale();
                Setting::set('response_language', $locale);
            }

            if (! is_dir(lang_path($locale))) {
                $locale = config('app.locale');
            }

            app()->setLocale($locale);
        } catch (\Exception) {
        }
    }

    public function detectPreferredLocale(): string
    {
        try {
            $availableLocales = $this->getAvailableLocales();

            if (empty($availableLocales)) {
                return config('app.locale');
            }

            $preferredLanguages = request()->getLanguages();

            foreach ($preferredLanguages as $preferred) {
                if (\in_array($preferred, $availableLocales, true)) {
                    return $preferred;
                }

                foreach ($availableLocales as $available) {
                    if (str_starts_with($available, $preferred)) {
                        return $available;
                    }
                }

                $normalizedPreferred = str_replace('-', '_', $preferred);
                if (\in_array($normalizedPreferred, $availableLocales, true)) {
                    return $normalizedPreferred;
                }

                foreach ($availableLocales as $available) {
                    if (str_starts_with($available, str_replace('-', '_', explode('-', $preferred)[0]))) {
                        return $available;
                    }
                }
            }

            return config('app.locale');
        } catch (\Exception) {
            return config('app.locale');
        }
    }

    /**
     * @return array<string>
     */
    public function getAvailableLocales(): array
    {
        try {
            $langPath = lang_path();

            if (! is_dir($langPath)) {
                return [];
            }

            $directories = scandir($langPath);
            if ($directories === false) {
                return [];
            }

            return array_values(array_filter($directories, fn ($item) => $item !== '.' && $item !== '..' && is_dir("{$langPath}/{$item}")));
        } catch (\Exception) {
            return [];
        }
    }
}
