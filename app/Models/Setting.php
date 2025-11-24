<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        // Check cache first
        $cacheKey = "settings.{$key}";
        $cached = cache()->get($cacheKey);

        if ($cached !== null) {
            return $cached;
        }

        $setting = static::where('key', $key)->first();
        $value = $setting ? $setting->value : $default;

        // Cache for 1 hour
        cache()->put($cacheKey, $value, 3600);

        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Update cache
        cache()->put("settings.{$key}", $value, 3600);
    }

    public static function getEncrypted(string $key, mixed $default = null): mixed
    {
        $value = static::get($key, $default);

        if ($value === null || $value === $default) {
            return $default;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return $default;
        }
    }

    public static function setEncrypted(string $key, mixed $value): void
    {
        static::set($key, Crypt::encryptString($value));
    }

    public static function getJson(string $key, mixed $default = null): mixed
    {
        $value = static::get($key, null);

        if ($value === null) {
            return $default;
        }

        try {
            $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

            return $decoded;
        } catch (\Exception) {
            return $default;
        }
    }

    public static function setJson(string $key, mixed $value): void
    {
        try {
            $encoded = json_encode($value, JSON_THROW_ON_ERROR);
            static::set($key, $encoded);
        } catch (\Exception) {
            // If encoding fails, don't save
        }
    }

    public static function getJsonDecrypted(string $key, mixed $default = null): mixed
    {
        $value = static::get($key, null);

        if ($value === null) {
            return $default;
        }

        try {
            $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);

            if (isset($decoded['key']) && ! empty($decoded['key'])) {
                try {
                    $decoded['key'] = Crypt::decryptString($decoded['key']);
                } catch (\Exception) {
                    $decoded['key'] = '';
                }
            }

            return $decoded;
        } catch (\Exception) {
            return $default;
        }
    }

    public static function setJsonEncrypted(string $key, mixed $value): void
    {
        try {
            if (isset($value['key']) && ! empty($value['key'])) {
                $value['key'] = Crypt::encryptString($value['key']);
            }

            $encoded = json_encode($value, JSON_THROW_ON_ERROR);
            static::set($key, $encoded);
        } catch (\Exception) {
            // If encoding fails, don't save
        }
    }
}
