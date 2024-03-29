<?php

// BACKUP_PROJECT_DIRECTORY is the path of the project to backup (source code, assets).
$projectDirectory = rtrim(env('BACKUP_PROJECT_DIRECTORY') ?? '', '/');

// BACKUP_EXCLUDE_DIRECTORIES is a list of directories to exclude from the backup.
$excludeDirectories = explode(',', env('BACKUP_EXCLUDE_DIRECTORIES') ?? '');
$excludeDirectories = array_map(function ($directory) use ($projectDirectory) {
    return $projectDirectory . '/' . trim($directory, '/');
}, $excludeDirectories);

/** For S3 backup with rclone */
$s3Backups = [
    'backup_1' => [
        'daily_s3_backup_time' => env('BACKUP_DAILY_BACKUP_S3_TIME'),
        's3' => [
            'rclone_source' => env('BACKUP_S3_RCLONE_SOURCE'),
            'rclone_destination' => env('BACKUP_S3_RCLONE_DESTINATION'),
        ]
    ]
];

for ($i = 2; $i < 100; $i++) {
    if (env('BACKUP_DAILY_BACKUP_S3_TIME_' . $i) !== null) {
        $s3Backups['backup_' . $i] = [
            'daily_s3_backup_time' => env('BACKUP_DAILY_BACKUP_S3_TIME_' . $i),
            's3' => [
                'rclone_source' => env('BACKUP_S3_RCLONE_SOURCE_'. $i),
                'rclone_destination' => env('BACKUP_S3_RCLONE_DESTINATION_'. $i),
            ]
        ];
    }
}

return [
    /** These are boo specific */
    'daily_clean_time' => env('BACKUP_DAILY_CLEAN_TIME'),
    'daily_backup_time' => env('BACKUP_DAILY_BACKUP_TIME'),

    's3_backups' => $s3Backups,

    'backup_command_extra_flags' => env('BACKUP_COMMAND_EXTRA_FLAGS', ''),

    'attempts' => (int) env('BACKUP_ATTEMPTS', '1'),

    'backup' => [

        /*
         * The name of this application. You can use this name to monitor
         * the backups.
         */
        'name' => env('APP_NAME', 'laravel-backup'),

        'source' => [

            'files' => [

                /*
                 * The list of directories and files that will be included in the backup.
                 */
                'include' => [
                    $projectDirectory,
                ],

                /*
                 * These directories and files will be excluded from the backup.
                 *
                 * Directories used by the backup process will automatically be excluded.
                 */
                'exclude' => $excludeDirectories,

                /*
                 * Determines if symlinks should be followed.
                 */
                'follow_links' => env('BACKUP_FOLLOW_LINKS', false),

                /*
                 * Determines if it should avoid unreadable folders.
                 */
                'ignore_unreadable_directories' => false,

                /*
                 * This path is used to make directories in resulting zip-file relative
                 * Set to `null` to include complete absolute path
                 * Example: base_path()
                 */
                'relative_path' => null,
            ],

            /*
             * The names of the connections to the databases that should be backed up
             * MySQL, PostgreSQL, SQLite and Mongo databases are supported.
             *
             * The content of the database dump may be customized for each connection
             * by adding a 'dump' key to the connection settings in config/database.php.
             * E.g.
             * 'mysql' => [
             *       ...
             *      'dump' => [
             *           'excludeTables' => [
             *                'table_to_exclude_from_backup',
             *                'another_table_to_exclude'
             *            ]
             *       ],
             * ],
             *
             * If you are using only InnoDB tables on a MySQL server, you can
             * also supply the useSingleTransaction option to avoid table locking.
             *
             * E.g.
             * 'mysql' => [
             *       ...
             *      'dump' => [
             *           'useSingleTransaction' => true,
             *       ],
             * ],
             *
             * For a complete list of available customization options, see https://github.com/spatie/db-dumper
             */
            'databases' => [
                env('DB_CONNECTION', 'mysql'),
            ],
        ],

        /*
         * The database dump can be compressed to decrease diskspace usage.
         *
         * Out of the box Laravel-backup supplies
         * Spatie\DbDumper\Compressors\GzipCompressor::class.
         *
         * You can also create custom compressor. More info on that here:
         * https://github.com/spatie/db-dumper#using-compression
         *
         * If you do not want any compressor at all, set it to null.
         */
        'database_dump_compressor' => null,

        /*
         * The file extension used for the database dump files.
         *
         * If not specified, the file extension will be .archive for MongoDB and .sql for all other databases
         * The file extension should be specified without a leading .
         */
        'database_dump_file_extension' => '',

        'destination' => [

            /*
             * The filename prefix used for the backup zip file.
             */
            'filename_prefix' => '',

            /*
             * The disk names on which the backups will be stored.
             */
            'disks' => [
                'backup',
            ],
        ],

        /*
         * The directory where the temporary files will be stored.
         */
        'temporary_directory' => storage_path('app/backup-temp'),

        /*
         * The password to be used for archive encryption.
         * Set to `null` to disable encryption.
         */
        'password' => env('BACKUP_ARCHIVE_PASSWORD'),

        /*
         * The encryption algorithm to be used for archive encryption.
         * You can set it to `null` or `false` to disable encryption.
         *
         * When set to 'default', we'll use ZipArchive::EM_AES_256 if it is
         * available on your system.
         */
        'encryption' => 'default',
    ],

    /*
     * You can get notified when specific events occur. Out of the box you can use 'mail' and 'slack'.
     * For Slack you need to install laravel/slack-notification-channel.
     *
     * You can also use your own notification classes, just make sure the class is named after one of
     * the `Spatie\Backup\Notifications\Notifications` classes.
     */
    'notifications' => [

        'notifications' => [
            \Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => ['slack'],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => ['slack'],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => ['slack'],
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class => ['slack'],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => ['slack'],
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class => ['slack'],
        ],

        /*
         * Here you can specify the notifiable to which the notifications should be sent. The default
         * notifiable will use the variables specified in this config file.
         */
        'notifiable' => \Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
            'to' => 'your@example.com',

            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Example'),
            ],
        ],

        'slack' => [
            'webhook_url' => env('BACKUP_SLACK_WEBHOOK'),

            /*
             * If this is set to null the default channel of the webhook will be used.
             */
            'channel' => env('BACKUP_SLACK_CHANNEL'),

            'username' => env('BACKUP_SLACK_USERNAME'),

            'icon' => env('BACKUP_SLACK_ICON'),

        ],

        'discord' => [
            'webhook_url' => '',

            'username' => null,

            'avatar_url' => null,
        ],
    ],

    /*
     * Here you can specify which backups should be monitored.
     * If a backup does not meet the specified requirements the
     * UnHealthyBackupWasFound event will be fired.
     */
    'monitor_backups' => [
        [
            'name' => env('APP_NAME', 'laravel-backup'),
            'disks' => ['backup'],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => (int) env('BACKUP_MAXIMUM_STORAGE_IN_MEGABYTES', 5000), //phpcs:ignore
            ],
        ],

        /*
        [
            'name' => 'name of the second app',
            'disks' => ['local', 's3'],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
        */
    ],

    'cleanup' => [
        /*
         * The strategy that will be used to cleanup old backups. The default strategy
         * will keep all backups for a certain amount of days. After that period only
         * a daily backup will be kept. After that period only weekly backups will
         * be kept and so on.
         *
         * No matter how you configure it the default strategy will never
         * delete the newest backup.
         */
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'default_strategy' => [

            /*
             * The number of days for which backups must be kept.
             */
            'keep_all_backups_for_days' => (int) env('BACKUP_KEEP_ALL_BACKUPS_FOR_DAYS', 7),

            /*
             * The number of days for which daily backups must be kept.
             */
            'keep_daily_backups_for_days' => (int) env('BACKUP_KEEP_DAILY_BACKUPS_FOR_DAYS', 7),

            /*
             * The number of weeks for which one weekly backup must be kept.
             */
            'keep_weekly_backups_for_weeks' => (int) env('BACKUP_KEEP_WEEKLY_BACKUPS_FOR_WEEKS', 4),

            /*
             * The number of months for which one monthly backup must be kept.
             */
            'keep_monthly_backups_for_months' => (int) env('BACKUP_KEEP_MONTHLY_BACKUPS_FOR_MONTHS', 4),

            /*
             * The number of years for which one yearly backup must be kept.
             */
            'keep_yearly_backups_for_years' => (int) env('BACKUP_KEEP_YEARLY_BACKUPS_FOR_YEARS', 0),

            /*
             * After cleaning up the backups remove the oldest backup until
             * this amount of megabytes has been reached.
             */
            'delete_oldest_backups_when_using_more_megabytes_than' => (int) env('BACKUP_MAXIMUM_STORAGE_IN_MEGABYTES', 5000), //phpcs:ignore
        ],
    ],

];
