<?php

namespace App\Ghost\Install;

use Closure;
use Event;

class Installer
{
    /**
     * Settings from file settings_{env}.php
     * @var array
     */
    public $settings = [];

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    public function runInstall()
    {
        foreach ($this->settings['installers'] as $installerClass) {
            (new $installerClass($this->settings))->runInstall();
        }
    }

    public function on(Closure $callback)
    {

    }
}