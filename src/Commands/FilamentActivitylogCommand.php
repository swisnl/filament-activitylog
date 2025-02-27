<?php

namespace Swis\FilamentActivitylog\Commands;

use Illuminate\Console\Command;

class FilamentActivitylogCommand extends Command
{
    public $signature = 'filament-activitylog';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
