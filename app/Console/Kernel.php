<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
        Commands\FillTestData::class,
        Commands\PathUpdateDb::class,
        Commands\TgNewsletter::class,
        Commands\TgNewsletterText::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        //$schedule->command('tg_newsletter:go')->at('23:59');

        if (env('TEST_CRON')) {
            $schedule->command('tg_newsletter:go --testing')->everyMinute();
        }
    }
}
