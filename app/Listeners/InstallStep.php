<?php

namespace App\Listeners;

use App\Events\Install;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InstallStep
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Install  $event
     * @return void
     */
    public function handle(Install $event)
    {
        //
    }
}
