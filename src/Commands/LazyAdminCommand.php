<?php

namespace Step2dev\LazyAdmin\Commands;

use Illuminate\Console\Command;

class LazyAdminCommand extends Command
{
    public $signature = 'lazy-admin';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
