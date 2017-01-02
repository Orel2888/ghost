<?php

class OrderTest extends TestCase
{
    /**
     * @var OrderApi
     */
    public $api;

    public function setUp()
    {
        $this->api = new OrderApi();

        $this->api->saveToken();
    }

    public function test_authenticate()
    {
        $this->api->authentication();
    }

    public function test_create()
    {

    }
}