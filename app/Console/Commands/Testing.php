<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan,
    DB;

class Testing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'testing {arg1}';

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

        if (method_exists($this, $this->argument('arg1'))) {
            $method = $this->argument('arg1');

            $this->$method();
        }
    }

    public function seed()
    {
        Artisan::call('db:seed', [
            '--class'   => 'TestDataSeeder'
        ]);

        $this->info('OK');
    }

    public function flush()
    {
        DB::statement('DELETE FROM clients');
        DB::statement('DELETE FROM citys');
        DB::statement('DELETE FROM miners');

        $this->info('OK');
    }
}
