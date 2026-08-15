<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Pembersih sesi kedaluwarsa (driver database). Jalankan via cron:
        // `* * * * * cd /path/project && php artisan schedule:run >> /dev/null 2>&1`
        $schedule->call(function () {
            $lifetimeMinutes = (int) config('session.lifetime', 720);
            $cutoff = time() - ($lifetimeMinutes * 60);

            DB::table('sessions')->where('last_activity', '<', $cutoff)->delete();
        })->daily()->name('cleanup-expired-sessions');

        // Cleanup expired chatbot conversations (reset state to idle after 24 hours)
        $schedule->command('chatbot:cleanup-expired')
            ->hourly()
            ->name('cleanup-expired-conversations')
            ->withoutOverlapping();

        // Process queued jobs (if not using supervisor/queue:work daemon)
        // Uncomment if using cron-based queue processing:
        // $schedule->command('queue:work --stop-when-empty --max-time=3600')
        //     ->everyMinute()
        //     ->withoutOverlapping()
        //     ->name('process-queue');
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
