<?php

use App\City;
use App\Miner;
use App\Client;
use Carbon\Carbon;
use App\Ghost\Repositories\Goods\GoodsManager;
use App\Goods;
use Faker\Factory as Faker;

trait BaseTestsHelper
{

    protected static $database;

    public function createOrder()
    {
        $goods = Goods::first();

        $faker = Faker::create();

        $clientName = $faker->name;

        $client = Client::create([
            'name'      => $clientName,
            'comment'   => $clientName
        ]);

        $goodsFirstPrice = $goods->goodsPrice()->first();

        return $this->goodsOrder->create([
            'goods_id'  => $goods->id,
            'client_id' => $client->id,
            'weight'    => $goodsFirstPrice->weight,
            'comment'   => $client->comment,
            'cost'      => $goodsFirstPrice->cost
        ]);
    }

    public function createCity()
    {
        return City::create(['name' => 'Новосибирск']);
    }

    public function createGoods()
    {
        return $this->goodsManager->addGoods([
            'goods_name'    => 'Бананы',
            'city_id'       => $this->createCity()->id
        ]);
    }

    public function createGoodsPrice($reserved = 0)
    {
        $goods = $this->createGoods();

        $goodsData = [
            'miner_id'  => $this->createMiner()->id,
            'goods_id'  => $goods->id,
            'city_id'   => $goods->city_id,
            'weight'    => '0.5',
            'address'   => 'The backup and restore operations for global variables and static',
            'cost'      => 1000
        ];

        if (!$reserved) {
            return $this->goodsManager->addGoodsPrice($goodsData);
        }

        if ($reserved) {
            return $this->goodsManager->addGoodsPrice($goodsData, [
                'client_id' => $this->createClient()->id,
                'time'      => Carbon::now()->addDay(1)->toDateTimeString()
            ]);
        }
    }

    public function createClient($attributes = [])
    {
        return Client::create(array_merge(['name' => 'Vasya'], $attributes));
    }

    public function createMiner()
    {
        return Miner::create([
            'name'  => 'Vadim'
        ]);
    }

    public function createData()
    {
        self::$database['citys'] = [
            City::create(['name' => 'Новосибирск']),
            City::create(['name' => 'Иркутск'])
        ];

        $goodsManager = new GoodsManager();

        self::$database['goods']   = [
            $goodsManager->addGoods([
                'goods_name'    => 'Бананы',
                'city_id'       => self::$database['citys'][0]->id
            ]),
            $goodsManager->addGoods([
                'goods_name'    => 'Лимоны',
                'city_id'       => self::$database['citys'][0]->id
            ]),
            $goodsManager->addGoods([
                'goods_name'    => 'Апельсины',
                'city_id'       => self::$database['citys'][1]->id
            ]),
            $goodsManager->addGoods([
                'goods_name'    => 'Киви',
                'city_id'       => self::$database['citys'][1]->id
            ])
        ];
        self::$database['miners']  = [
            Miner::create([
                'name'  => 'Vadim'
            ]),
            Miner::create([
                'name'  => 'Олег'
            ])
        ];
        self::$database['clients'] = [
            Client::create(['name' => 'Vasya']),
            Client::create(['name' => 'Petya'])
        ];

        self::$database['goods_price'] = [
            $goodsManager->addGoodsPrice([
                'miner_id'  => self::$database['miners'][0]->id,
                'goods_id'  => self::$database['goods'][0]->id,
                'weight'    => '0.5',
                'address'   => 'The backup and restore operations for global variables and static',
                'cost'      => 1000
            ]),
            $goodsManager->addGoodsPrice([
                'miner_id'  => self::$database['miners'][0]->id,
                'goods_id'  => self::$database['goods'][0]->id,
                'weight'    => '1.0',
                'address'   => 'The backup and restore operations for global variables and static',
                'cost'      => 1000
            ]),
            $goodsManager->addGoodsPrice([
                'miner_id'  => self::$database['miners'][1]->id,
                'goods_id'  => self::$database['goods'][1]->id,
                'weight'    => '0.5',
                'address'   => 'The backup and restore operations for global variables and static',
                'cost'      => 1000
            ]),
            $goodsManager->addGoodsPrice([
                'miner_id'  => self::$database['miners'][1]->id,
                'goods_id'  => self::$database['goods'][1]->id,
                'weight'    => '1.0',
                'address'   => 'The backup and restore operations for global variables and static',
                'cost'      => 1000
            ]),
            $goodsManager->addGoodsPrice([
                'miner_id'  => self::$database['miners'][0]->id,
                'goods_id'  => self::$database['goods'][2]->id,
                'weight'    => '0.5',
                'address'   => 'The backup and restore operations for global variables and static',
                'cost'      => 1000
            ]),
            $goodsManager->addGoodsPrice([
                'miner_id'  => self::$database['miners'][0]->id,
                'goods_id'  => self::$database['goods'][2]->id,
                'weight'    => '1.0',
                'address'   => 'The backup and restore operations for global variables and static',
                'cost'      => 1000
            ]),
            $goodsManager->addGoodsPrice([
                'miner_id'  => self::$database['miners'][1]->id,
                'goods_id'  => self::$database['goods'][3]->id,
                'weight'    => '0.5',
                'address'   => 'The backup and restore operations for global variables and static',
                'cost'      => 1000
            ]),
            $goodsManager->addGoodsPrice([
                'miner_id'  => self::$database['miners'][1]->id,
                'goods_id'  => self::$database['goods'][3]->id,
                'weight'    => '1.0',
                'address'   => 'The backup and restore operations for global variables and static',
                'cost'      => 1000
            ])
        ];
    }

    public static function removeData()
    {
        \DB::statement('DELETE FROM citys');
        \DB::statement('DELETE FROM clients');
        \DB::statement('DELETE FROM miners');
    }
}