<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\QiwiTransaction;

class TestToolsTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function test_create_goods_to_price()
    {
        $goodsList = $this->testTools->createGoodsToPrice(1);

        $this->assertEquals(1, $goodsList->count());

        $goodsList = $this->testTools->createGoodsToPrice(2);

        $this->assertEquals(2, $goodsList->count());
    }

    public function test_create_transaction()
    {
        // Creating a one transaction
        $this->testTools->createTransaction(1000, 'comment');

        // Creating a few transaction
        $this->testTools->createTransaction(1000, 'comment', 2);
    }

    public function test_clear()
    {
        $this->testTools->cleaningTemporaryRows();

        $this->assertTrue(QiwiTransaction::all()->isEmpty());
    }
}
