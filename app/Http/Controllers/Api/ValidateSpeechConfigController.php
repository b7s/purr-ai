<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\WhisperService;
use Illuminate\Http\JsonResponse;

final class ValidateSpeechConfigController extends Controller
{
    public function __invoke(): JsonResponse
    {
        // Check if speech-to-text is enabled
        if ((int) Setting::get('speech_to_text_enabled') !== 1) {
            return response()->json([
                'valid' => false,
                'reason' => 'speech_disabled',
            ]);
        }

        // Check if using local speech
        $useLocal = (int) Setting::get('use_local_speech') === 1;

        if ($useLocal) {
            // Validate local configuration
            if (WhisperService::hasPendingConfiguration()) {
                return response()->json([
                    'valid' => false,
                    'reason' => 'local_pending_configuration',
                ]);
            }

            return response()->json(['valid' => true]);
        }

        // Validate online configuration
        $model = Setting::getValidatedSpeechModel();

        if ($model === false) {
            return response()->json([
                'valid' => false,
                'reason' => 'invalid_configuration',
            ]);
        }

        return response()->json(['valid' => true]);
    }
}
