<?php

use App\Client;

class UsersApiTest extends TestCase
{
    /**
     * @var UsersApi
     */
    public $usersApi;

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

        $responseData = $this->usersApi->find($client->tg_chatid);

        $this->assertEquals($client->getAttributes(), (array) $responseData->data);
    }

    public function test_find_not_exists_user()
    {
        try {
            $this->usersApi->find(222111211111);
        } catch (UsersApiException $e) {
            $this->assertEquals(404, $e->getStatusCode());
        }
    }

    public function test_registration()
    {
        /*try {
            $responseData = $this->usersApi->reg();
        } catch (UsersApiException $e) {

        }*/

        $this->usersApi->run('users.find', [
            'name'          => 'newusertest',
            'tg_username'   => 'username',
            'tg_chatid'     => '44678'
        ], function ($responseData, $exception) {
            BaseApi::throwException($exception);

            $this->assertNotEmpty($responseData->client_id);
        });
    }
}