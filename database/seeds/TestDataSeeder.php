<?php

use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $this->call(ClientsTableSeeder::class);

        $this->citiesGoodsAndPriceWithMiner();
    }

    public function citiesGoodsAndPriceWithMiner()
    {
        $cities = factory(App\City::class, 2)->create()->each(function ($city) {
            $city->goods()->saveMany([
                factory(App\Goods::class)->make(),
                factory(App\Goods::class)->make()
            ]);
        })->each(function ($city) {
            $city->goods->each(function ($goods) {
                $goods->goodsPrice()->saveMany([
                    new App\GoodsPrice([
                        'miner_id'  => factory(App\Miner::class)->create()->id,
                        'weight'    => 0.5,
                        'address'   => 'Here buyed a address',
                        'cost'      => 1500
                    ]),
                    new App\GoodsPrice([
                        'miner_id'  => factory(App\Miner::class)->create()->id,
                        'weight'    => 1,
                        'address'   => 'Here buyed a address',
                        'cost'      => 2500
                    ])
                ]);
            });
        });
    }
}
