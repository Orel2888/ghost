<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Endata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'endata {type} {--data=?}';

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
        if ($this->argument('type') == 'enc') {
            $file = file_get_contents(storage_path('description.txt'));

            file_put_contents(storage_path('description.txt'), \Crypt::encrypt($file));
        }

        if ($this->argument('type') == 'show') {
            $this->info(\Crypt::decrypt(file_get_contents(storage_path('description.txt'))));
        }

        if ($this->argument('type') == 'dec') {
            $this->info(\Crypt::decrypt($this->option('data')));
        }

        if ($this->argument('type') == 'dec2') {
            $this->info(\App\Ghost\Libs\GibberishAES::dec($this->option('data'), env('K5')));
        }
    }
}
