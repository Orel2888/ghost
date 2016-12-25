<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://gst.dev';

    protected $sqlite_in_memory = false;

    protected $databaseSeed = false;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function setUp()
    {
        parent::setUp();

        if ($this->databaseSeed) {
            if (!$this->sqlite_in_memory) {
                /*DB::statement('DELETE FROM clients');
                DB::statement('DELETE FROM citys');
                DB::statement('DELETE FROM miners');*/
            } else {
                $this->app->config->set('database.default', 'testing');

                Artisan::call('migrate');
                Artisan::call('db:seed');
            }
        }
    }
}
