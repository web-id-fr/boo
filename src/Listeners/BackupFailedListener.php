<?php

namespace WebId\Boo\Listeners;

use Spatie\Backup\Events\BackupHasFailed;

class BackupFailedListener
{
    public function handle(BackupHasFailed $event)
    {
    }
}
