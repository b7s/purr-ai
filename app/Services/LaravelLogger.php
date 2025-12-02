<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;
use LaravelWhisper\Logger;

final class LaravelLogger implements Logger
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function info(string $message, array $context = []): void
    {
        Log::info($message, $context);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function error(string $message, array $context = []): void
    {
        Log::error($message, $context);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    public function warning(string $message, array $context = []): void
    {
        Log::warning($message, $context);
    }
}
