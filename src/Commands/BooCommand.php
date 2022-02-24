<?php

namespace WebId\Boo\Commands;

use Illuminate\Console\Command;

class BooCommand extends Command
{
    public $signature = 'boo';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
