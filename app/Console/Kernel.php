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
        $schedule->command('send:yearly-emails')->yearlyOn(1, 1)->timezone('Asia/Dhaka');
        $schedule->command('send:yearly-emails')->quarterlyOn(1)->timezone('Asia/Dhaka');
        $schedule->command('send:yearly-emails')->yearlyOn(12, 31)->timezone('Asia/Dhaka');
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
