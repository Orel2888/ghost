<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiUsersTest extends TestCase
{
    use ApiTrait, BaseTestsHelper;

    public function test_update()
    {
        $accessToken = $this->authenticateUser();

        $client = $this->createClient([
            'tg_chatid' => 123,
            'comment'   => 'tgname'
        ]);

        $response = $this->call('POST', 'api/users.update', [
            'tg_chatid' => $client->tg_chatid,
            'name'      => 'New Name',
            'comment'   => 'New Comment',
            'tg_username'   => 'New username',
            'access_token'  => $accessToken
        ]);

        $this->assertEquals(200, $response->getStatusCode());

        $client->delete();
    }


    public function test_end()
    {
        \Cache::flush();
    }
}
