<?php

use Illuminate\Support\Facades\Route;
use WebId\Boo\Controllers\BackupController;

Route::get('test', [BackupController::class, 'index']);
