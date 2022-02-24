<?php

namespace WebId\Boo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \WebId\Boo\Boo
 */
class Boo extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'boo';
    }
}
