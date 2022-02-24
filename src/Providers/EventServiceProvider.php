<?php

namespace WebId\Boo\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Spatie\Backup\Events\BackupHasFailed;
use Spatie\Backup\Events\BackupWasSuccessful;
use WebId\Boo\Listeners\BackupFailedListener;
use WebId\Boo\Listeners\BackupSuccessfulListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BackupWasSuccessful::class => [
            BackupSuccessfulListener::class
        ],

        BackupHasFailed::class => [
            BackupFailedListener::class
        ]
    ];
}
