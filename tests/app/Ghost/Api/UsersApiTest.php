<?php

use App\Client;

class UsersApiTest extends TestCase
{
    /**
     * @var UsersApi
     */
    public $usersApi;

    /**
     * @var DataStore
     */
    public $dataStore;

    public function setUp()
    {
        parent::setUp();

        $this->usersApi = new UsersApi();

        $this->usersApi->saveToken();
    }

    public function test_authentication()
    {
        $this->usersApi->authentication();
    }

    public function test_find()
    {
        $client = Client::first();

        $this->usersApi->run('users.find', $client->tg_chatid, function ($responseData, $exception) use($client) {
            BaseApi::throwException($exception);

            $this->assertEquals($client->getAttributes(), (array) $responseData->data);
        });
    }

    public function test_find_not_exists_user()
    {
        $this->usersApi->run('users.find', 222111211111, function ($responseData, $exception) {
            $this->assertEquals(404, $exception->getStatusCode());
        });
    }

    public function test_registration()
    {
        $this->usersApi->run('users.reg', [
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

        $this->usersApi->run('users.update', [
            'name'          => 'new_name',
            'tg_username'   => 'new_username',
            'tg_chatid'     => $client->tg_chatid
        ], function ($responseData, $e) {
            BaseApi::throwException($e);

        });
    }

    public function test_get_purse()
    {
        $this->usersApi->run('users.purse', null, function ($responseData, $e) {
            BaseApi::throwException($e);

            $this->assertNotEmpty($responseData->data->phone);
        });
    }

    public function test_clear()
    {
        $this->testTools->cleaningTemporaryRows();
    }
}