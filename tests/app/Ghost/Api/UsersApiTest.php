<?php

use App\Client;

class UsersApiTest extends TestCase
{
    /**
     * @var UsersApi
     */
    public $api;

    /**
     * @var DataStore
     */
    public $dataStore;

    public function setUp()
    {
        parent::setUp();

        $this->api = new UsersApi();

        $this->api->saveToken();
    }

    public function test_authentication()
    {
        $this->api->authentication();
    }

    public function test_find()
    {
        $client = Client::first();

        $this->api->usersFind($client->tg_chatid, function ($responseData, $exception) use($client) {
            BaseApi::throwException($exception);

            $this->assertEquals($client->getAttributes(), (array) $responseData->data);
        });
    }

    public function test_find_not_exists_user()
    {
        $this->api->usersFind(222111211111, function ($responseData, $exception) {
            $this->assertEquals(404, $exception->getStatusCode());
        });
    }

    public function test_registration()
    {
        $this->api->usersReg([
            'name'          => 'newusertest',
            'tg_username'   => 'username',
            'tg_chatid'     => 44678
        ], function ($responseData, $exception) {
            BaseApi::throwException($exception);

            $this->assertTrue(is_numeric($responseData->client_id));

            $this->testTools->storage->add('clients', $responseData->client_id);
        });
    }

    public function test_update()
    {
        $client = Client::first();

        $this->api->usersUpdate([
            'name'          => 'new_name',
            'tg_username'   => 'new_username',
            'tg_chatid'     => $client->tg_chatid
        ], function ($responseData, $e) {
            BaseApi::throwException($e);

        });
    }

    public function test_get_purse()
    {
        $this->api->usersPurse(function ($responseData, $e) {
            BaseApi::throwException($e);

            $this->assertNotEmpty($responseData->data->phone);
        });
    }

    public function test_clear()
    {
        $this->testTools->cleaningTemporaryRows();
    }
}