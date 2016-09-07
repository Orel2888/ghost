<?php

use App\Ghost\Install\SetupDataDbCreator;

class InstallSetupDataDbCreatorTest extends TestCase
{

    public $setupDataDbCreator;

    public function setUp()
    {
        parent::setUp();

        $this->setupDataDbCreator = new SetupDataDbCreator([
            'APP_ENV'               => 'local',
            'after_each_unistall'   => true
        ]);
    }

    public function test_get_map_installers()
    {
        var_dump($this->setupDataDbCreator->installers);
        var_dump($this->setupDataDbCreator->uninstallers);
        var_dump($this->setupDataDbCreator->getUninstaller($this->setupDataDbCreator->installers[1]));
    }

    public function test_run_install()
    {
        $this->setupDataDbCreator->runInstall();
    }
}