<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\LocaleService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        app(LocaleService::class)->configure();
    }
}
