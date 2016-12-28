<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DataStoreTest extends TestCase
{
    /**
     * @var DataStore
     */
    public $dataStore;

    public function setUp()
    {
        parent::setUp();

        $this->dataStore = new DataStore('data_store_test');
    }

    public function test_add_and_get()
    {
        $testSection = ['section1', 'section2'];

        $this->assertTrue($this->dataStore->add($testSection[0], 1));
        $this->assertTrue($this->dataStore->add($testSection[0], 2));

        $this->assertEquals([1, 2], $this->dataStore->get('section1'));

        $this->assertTrue($this->dataStore->add($testSection[1], 1));
        $this->assertTrue($this->dataStore->add($testSection[1], 2));

        $this->assertEquals([1, 2], $this->dataStore->get('section2'));
    }

    public function test_flush()
    {
        $this->assertTrue($this->dataStore->flush());
    }
}
