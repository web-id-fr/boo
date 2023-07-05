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
            $schedule
                ->command(trim('backup:run ' . config('backup.backup_command_extra_flags')))
                ->daily()
                ->at(config('backup.daily_backup_time'));
        }

        /** @var \Illuminate\Config\Repository $config */
        $config = config();

        for ($i = 1; $i < 100; $i++) {
            if ($config->has('backup.s3_backups.backup_' . $i . '.daily_s3_backup_time')
                && $config->has('backup.s3_backups.backup_' . $i . '.s3.rclone_source')
                && $config->has('backup.s3_backups.backup_' . $i . '.s3.rclone_destination')
                && is_string(config('backup.s3_backups.backup_' . $i . '.daily_s3_backup_time'))
                && !empty(config('backup.s3_backups.backup_' . $i . '.daily_s3_backup_time'))
                && is_string(config('backup.s3_backups.backup_' . $i . '.s3.rclone_source'))
                && is_string(config('backup.s3_backups.backup_' . $i . '.s3.rclone_destination'))
            ) {
                $schedule->command(sprintf(
                    'backup:s3-sync %s %s',
                    config('backup.s3_backups.backup_' . $i . '.s3.rclone_source'),
                    config('backup.s3_backups.backup_' . $i . '.s3.rclone_destination')
                ))->daily()->at(config('backup.s3_backups.backup_' . $i . '.daily_s3_backup_time'));
            }
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
