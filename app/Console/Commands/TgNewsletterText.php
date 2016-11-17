<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TgNewsletterText extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tg_newsletter:make_text {puthfile?}';

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

        $file = storage_path('app/message_newsletter');

        if (file_put_contents($file, \Crypt::encrypt(file_get_contents($file)))) {
            $this->info('Text write and encrypted!');
        } else {
            $this->error('Cannot write text (');
        }
    }
}
