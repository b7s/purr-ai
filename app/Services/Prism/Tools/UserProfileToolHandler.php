<?php

declare(strict_types=1);

namespace App\Services\Prism\Tools;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class UserProfileToolHandler
{
    public function getProfile(): string
    {
        try {
            $profile = [
                'user_name' => Setting::get('user_name', ''),
                'user_description' => Setting::get('user_description', ''),
                'mascot_name' => Setting::get('mascot_name', config('app.name')),
                'response_detail' => Setting::get('response_detail', 'detailed'),
                'response_tone' => Setting::get('response_tone', 'normal'),
                'timezone' => Setting::get('timezone', config('app.timezone')),
                'respond_as_cat' => (bool) Setting::get('respond_as_cat', false),
            ];

            $missingFields = [];
            if (empty($profile['user_name'])) {
                $missingFields[] = __('tools.user_profile.fields.user_name');
            }
            if (empty($profile['user_description'])) {
                $missingFields[] = __('tools.user_profile.fields.user_description');
            }

            $userMessage = empty($missingFields)
                ? __('tools.user_profile.profile_complete', ['name' => $profile['user_name']])
                : __('tools.user_profile.profile_incomplete', ['fields' => implode(', ', $missingFields)]);

            return json_encode([
                'success' => true,
                'profile' => $profile,
                'missing_fields' => $missingFields,
                'user_message' => $userMessage,
            ]);
        } catch (\Throwable $e) {
            Log::error('UserProfileTool: Failed to get profile', ['error' => $e->getMessage()]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('tools.user_profile.errors.get_failed'),
            ]);
        }
    }

    public function updateProfile(
        ?string $userName,
        ?string $userDescription,
        ?string $responseDetail,
        ?string $responseTone,
        ?string $timezone
    ): string {
        try {
            $updates = [];
            $errors = [];

            if ($userName !== null) {
                $userName = trim($userName);
                if (mb_strlen($userName) > 100) {
                    $errors[] = __('tools.user_profile.errors.name_too_long');
                } else {
                    Setting::set('user_name', $userName);
                    $this->clearCache('user_name');
                    $updates[] = __('tools.user_profile.fields.user_name');
                }
            }

            if ($userDescription !== null) {
                $userDescription = trim($userDescription);
                if (mb_strlen($userDescription) > 1000) {
                    $errors[] = __('tools.user_profile.errors.description_too_long');
                } else {
                    Setting::set('user_description', $userDescription);
                    $this->clearCache('user_description');
                    $updates[] = __('tools.user_profile.fields.user_description');
                }
            }

            if ($responseDetail !== null) {
                $validDetails = ['detailed', 'short'];
                if (! \in_array($responseDetail, $validDetails, true)) {
                    $errors[] = __('tools.user_profile.errors.invalid_detail', ['valid' => implode(', ', $validDetails)]);
                } else {
                    Setting::set('response_detail', $responseDetail);
                    $this->clearCache('response_detail');
                    $updates[] = __('tools.user_profile.fields.response_detail');
                }
            }

            if ($responseTone !== null) {
                $validTones = collect(config('purrai.response_tones', []))->pluck('value')->toArray();
                if (! \in_array($responseTone, $validTones, true)) {
                    $errors[] = __('tools.user_profile.errors.invalid_tone', ['valid' => implode(', ', $validTones)]);
                } else {
                    Setting::set('response_tone', $responseTone);
                    $this->clearCache('response_tone');
                    $updates[] = __('tools.user_profile.fields.response_tone');
                }
            }

            if ($timezone !== null) {
                if (! \in_array($timezone, timezone_identifiers_list(), true)) {
                    $errors[] = __('tools.user_profile.errors.invalid_timezone');
                } else {
                    Setting::set('timezone', $timezone);
                    $this->clearCache('timezone');
                    $updates[] = __('tools.user_profile.fields.timezone');
                }
            }

            if (empty($updates) && empty($errors)) {
                return json_encode([
                    'success' => false,
                    'user_message' => __('tools.user_profile.errors.no_updates'),
                ]);
            }

            if (! empty($errors)) {
                return json_encode([
                    'success' => false,
                    'errors' => $errors,
                    'updates' => $updates,
                    'user_message' => __('tools.user_profile.errors.validation_failed', ['errors' => implode('; ', $errors)]),
                ]);
            }

            return json_encode([
                'success' => true,
                'updated_fields' => $updates,
                'user_message' => __('tools.user_profile.profile_updated', ['fields' => implode(', ', $updates)]),
            ]);
        } catch (\Throwable $e) {
            Log::error('UserProfileTool: Failed to update profile', ['error' => $e->getMessage()]);

            return json_encode([
                'error' => $e->getMessage(),
                'user_message' => __('tools.user_profile.errors.update_failed'),
            ]);
        }
    }

    private function clearCache(string $key): void
    {
        cache()->forget("settings.{$key}");
    }
}
