<?php

namespace App\Console;

use App\Console\Commands\CrawlNctCommand;
use App\Console\Commands\CrawlNctDetailCommand;
use App\Console\Commands\UpdateMediaExpiredDownloadableUrlCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CrawlNctCommand::class,
        CrawlNctDetailCommand::class,
        UpdateMediaExpiredDownloadableUrlCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
