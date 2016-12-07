<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Longman\TelegramBot\Request as TgRequest;
use App\{
    Client,
    GoodsPrice
};
use App\Ghost\Repositories\Goods\GoodsOrder;

class JustTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $client = Client::whereTgUsername('sonicup')->first();
        $goods = GoodsPrice::query()->take(3)->get();

        $goodsOrder = new GoodsOrder();

        $orders = [];
        foreach ($goods as $goodsProduct) {
            $orders[] = $goodsOrder->create([
                'goods_id'   => $goodsProduct->goods_id,
                'client_id'  => $client->id,
                'weight'     => $goodsProduct->weight,
                'comment'    => $client->comment,
                'cost'       => $goodsProduct->cost
            ]);
        }

        foreach ($orders as $order) {
            $goodsOrder->buy($order);
        }

        $sep = str_repeat('❄️', 15);

        TgRequest::sendMessage([
            'chat_id'       => '284935778',
            'text'          => view('telegram.admin_about_purchase', compact('orders', 'sep'))->render(),
            'parse_mode'    => 'markdown'
        ]);
    }

    public function test_wcorrect()
    {
        $weights = [
            0.2,
            0.33,
            0.5,
            0.50,
            1.00,
            8.5,
            9,
            10
        ];

        $cor = function ($weight) {
            return preg_replace('|0+$|', '', $weight);
        };

        foreach ($weights as $weight) {
            echo $weight .'=>'. $cor($weight) . PHP_EOL;
        }
    }
}
