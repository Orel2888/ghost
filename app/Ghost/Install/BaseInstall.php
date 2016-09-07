<?php

namespace App\Ghost\Install;

abstract class BaseInstall
{
    /**
     * Data from settings.php
     * @var array
     */
    public $settings;

    public $config;

    public $installers = [];

    public $uninstallers = [];

    public function __construct($config)
    {
        $this->config = $config;

        $this->configure();

        $this->makeMapInstallers();
    }

    public function configure()
    {
        $this->settings = require(app_path('Ghost/Install/settings_'. $this->config['APP_ENV'] .'.php'));
    }

    public function makeMapInstallers()
    {
        $methods = get_class_methods($this);

        $filterMethod = function ($match) use ($methods) {
            return array_filter($methods, function ($name) use ($match) {
                return preg_match($match, $name);
            });
        };

        $this->installers = $filterMethod('/Apply$/i');

        $this->uninstallers = array_filter(array_map(function ($installer) use ($methods) {
            $installerCancelMethods = str_replace('Apply', 'Cancel', $installer);

            return in_array($installerCancelMethods, $methods) ? $installerCancelMethods : null;
        }, $this->installers), function ($item) {
            return $item !== null;
        });

    }

    public function getUninstaller($name)
    {
        $installerCancelMethods = str_replace('Apply', 'Cancel', $name);

        return in_array($installerCancelMethods, $this->uninstallers) ? $installerCancelMethods : null;
    }

    public function runInstall()
    {
        foreach ($this->installers as $installer) {
            $this->$installer();

            if ($this->getConfig('after_each_unistall')) {
                $this->unistall($installer);
            }
        }
    }

    public function runAllUnistall()
    {
        foreach ($this->installers as $installer) {
            if ($this->unistall($installer)) {

            }
        }
    }

    public function unistall($installer)
    {
        if (($unistaller = $this->getUninstaller($installer)) !== null) {
            return $this->$unistaller();
        }

        return false;
    }

    public function getConfig($key)
    {
        return $this->config[$key] ?? null;
    }
}