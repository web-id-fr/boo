<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (is_string(config('backup.daily_clean_time'))) {
            $schedule->command('backup:clean')->daily()->at(config('backup.daily_clean_time'));
        }

        if (is_string(config('backup.daily_backup_time'))) {
            $schedule->command('backup:run')->daily()->at(config('backup.daily_backup_time'));
        }

        if (is_string(config('backup.daily_s3_backup_time'))
            && !empty(config('backup.daily_s3_backup_time'))
            && is_string(config('backup.s3.rclone_source'))
            && is_string(config('backup.s3.rclone_destination'))
        ) {
            $schedule->command(sprintf(
                'backup:s3-sync %s %s',
                config('backup.s3.rclone_source'),
                config('backup.s3.rclone_destination')
            ))->daily()->at(config('backup.daily_s3_backup_time'));
        }
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
