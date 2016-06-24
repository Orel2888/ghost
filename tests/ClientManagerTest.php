<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClientManagerTest extends TestCase
{
    public function test_gen()
    {
        $token = bin2hex(openssl_random_pseudo_bytes(40));

        echo $token . PHP_EOL;
        echo "Size:". strlen($token);
    }
}
