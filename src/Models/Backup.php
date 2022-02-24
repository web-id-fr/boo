<?php

namespace WebId\Boo\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Carbon $backup_at
 */
class Backup extends Model
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;

    const STATUS = [
        self::STATUS_SUCCESS => 'Success',
        self::STATUS_FAILED => 'Failed',
    ];

    const TYPE_DATABASE = 1;
    const TYPE_FILE = 2;

    const TYPES = [
        self::TYPE_DATABASE => 'Database',
        self::TYPE_FILE => 'File',
    ];

    protected $table = 'boo';

    protected $fillable = [
        'name',
        'status',
        'type',
        'backup_at'
    ];

    protected $casts = [
        'backup_at' => 'datetime'
    ];
}
