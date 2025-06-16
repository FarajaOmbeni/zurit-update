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
        $schedule->command('remove:past-event')->dailyAt('23:50');
        $schedule->command('app:send-email-reminder')->dailyAt('9:00');
        $schedule->command('app:send-goal-reminder')->dailyAt('9:00');
        $schedule->command('create:recurrent-transactions')->daily();
        $schedule->command('run:recurring')->dailyAt('00:05');
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
