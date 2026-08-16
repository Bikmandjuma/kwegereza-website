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
        // $schedule->command('inspire')->hourly();

        // Spec §28: retention cleanup for group chat — runs daily, but the
        // command itself is a no-op on any day nothing has actually aged
        // past the configured windows, so running it daily rather than
        // "once a month" just means retention limits are enforced
        // promptly instead of drifting by up to 30 days.
        $schedule->command('group-chat:cleanup-retention')->daily();
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
