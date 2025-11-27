<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SettingsController extends Controller
{
    private const ALLOWED_KEYS = [
        'selected_audio_device_id',
    ];

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'key' => ['required', 'string', 'in:'.implode(',', self::ALLOWED_KEYS)],
            'value' => ['required', 'string', 'max:255'],
        ]);

        Setting::set($request->input('key'), $request->input('value'));

        return response()->json(['success' => true]);
    }

    public function show(string $key): JsonResponse
    {
        if (! \in_array($key, self::ALLOWED_KEYS, true)) {
            return response()->json(['error' => 'Invalid key'], 400);
        }

        $value = Setting::get($key);

        return response()->json(['value' => $value]);
    }
}
