<?php

namespace WebId\Boo\Listeners;

use Spatie\Backup\Events\BackupWasSuccessful;

class BackupSuccessfulListener
{
    public function handle(BackupWasSuccessful $event)
    {
    }
}
