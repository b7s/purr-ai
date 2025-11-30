<?php

namespace App\Providers;

use App\Console\Commands\PruneOldConversations;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class ScheduleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            // Clear old conversations every 4 hours
            $schedule->command(PruneOldConversations::class)
                ->everyFourHours()
                ->onOneServer()
                ->runInBackground()
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/schedule.log'));
        });
    }
}
