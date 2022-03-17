<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Carbon $started_at
 * @property Carbon $finished_at
 */
class Job extends Model
{
    protected $table = 'jobs';

    protected $fillable = [
        'name',
        'command',
        'status',
        'type',
        'output',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}
