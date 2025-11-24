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
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
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
}
