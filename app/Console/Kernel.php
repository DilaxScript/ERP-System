<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // You don't need to register GenerateDailyQRCodes here manually
        // Laravel auto-loads it if it's in app/Console/Commands
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // ✅ Schedule the QR generation command daily at 12:00 AM
        $schedule->command('generate:daily-qr')
                 ->dailyAt('00:00')
                 ->appendOutputTo(storage_path('logs/qr_cron.log')); // Optional: Logs output
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
