<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Ghost\Repositories\Tg\Tg;

class TgNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tg_newsletter:go {--file_text=} {--message=} {--testing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //

        $tg = new Tg('sync');

        if ($this->option('testing')) {
            $tg->forceTest();
        }

        $tg->newsletter($this->option('message') ?? file_get_contents(storage_path('app/message_newsletter')));
    }
}
