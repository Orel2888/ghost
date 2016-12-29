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
        $userChatId = Client::first()->tg_chatid;


    }
}