<?php

namespace WebId\Boo\Controllers;

use WebId\Boo\Models\Backup;
use WebId\Boo\Resources\BackupResource;

class BackupController
{
    public function index()
    {
        $backups = Backup::all();

        return view('boo::backup.index', [
            'backups' => BackupResource::collection($backups)->response()->getData(true)['data'],
        ]);
    }
}
