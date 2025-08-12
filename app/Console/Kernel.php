<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Send insurance expiry reminders daily at 9:00 AM
        $schedule->command('insurance:send-expiry-reminders')
                 ->dailyAt('09:00')
                 ->withoutOverlapping()
                 ->runInBackground();

        // Existing insurance reminders (keep for backwards compatibility)
        $schedule->command('insurance:send-reminders')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
