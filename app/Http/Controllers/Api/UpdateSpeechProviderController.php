<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class UpdateSpeechProviderController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => ['required', 'string'],
        ]);

        $provider = $request->input('provider');

        // Validate provider format (should be "provider:model")
        $parts = explode(':', $provider);
        if (\count($parts) !== 2) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid provider format',
            ], 400);
        }

        [$providerKey, $model] = $parts;

        // Validate provider exists in config
        $providers = config('purrai.ai_providers', []);
        $providerConfig = collect($providers)->firstWhere('key', $providerKey);

        if (! $providerConfig) {
            return response()->json([
                'success' => false,
                'error' => 'Provider not found',
            ], 400);
        }

        // Validate model exists in provider's speech_to_text models
        $availableModels = $providerConfig['models']['speech_to_text'] ?? [];
        if (! \in_array($model, $availableModels, true)) {
            return response()->json([
                'success' => false,
                'error' => 'Model not available for this provider',
            ], 400);
        }

        // Save to settings
        Setting::set('speech_provider', $provider);

        return response()->json([
            'success' => true,
        ]);
    }
}
