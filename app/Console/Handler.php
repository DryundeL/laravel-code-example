<?php

namespace App\Console;

use Illuminate\Cache\Console\PruneStaleTagsCommand;
use Illuminate\Console\Scheduling\Schedule;

class Handler
{
    public function __invoke(Schedule $schedule): void
    {
        $schedule->command(PruneStaleTagsCommand::class)->hourly();
    }
}
