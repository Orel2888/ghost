<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Ghost\Repositories\Tg\Tg;

class TgTest extends TestCase
{
    public function test_send_message_to_telegram()
    {
        $tg = new Tg();

        $response = $tg->sendMessageToUser(206441217, '*Hi world*');

        var_dump($response->ok);
    }

    public function test_send_newsletter()
    {
        $tg = new Tg();

        $tg->newsletter('Hi client');
    }
}
