<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Ghost\Repositories\Tg\Tg;

class TgTest extends TestCase
{
    /**
     * @var Tg
     */
    public $tg;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->tg = new Tg();
    }

    public function test_get_clients_testing()
    {
        $clientsTesting = $this->tg->getClientsTesting();
        //dump($clientsTesting);
        $this->assertNotEmpty($clientsTesting);
    }
    
    public function test_send_message_to_telegram()
    {
        $response = $this->tg->sendMessageToUser($this->tg->getClientsTesting()[0], '*Hi world*');

        var_dump($response->ok);
    }

    public function test_send_newsletter()
    {
        $this->tg->newsletter('Hi client');
    }
}
