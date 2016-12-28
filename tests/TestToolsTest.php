<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TestToolsTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        dump($this->getName());
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $testTools = new TestTools();

        //dump($testTools->clientWithOrder()->miner);

        //$testTools->cleaningTemporaryRows();
        echo 'Test 1';
    }

    public function testExample2()
    {
        echo 'Test 2';
    }

}
