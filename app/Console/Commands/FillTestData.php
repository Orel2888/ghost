<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Miner;
use App\City;
use App\Ghost\Repositories\Goods\GoodsManager;

class FillTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fill_test_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var GoodsManager
     */
    protected $goodsManager;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GoodsManager $goodsManager)
    {
        parent::__construct();

        $this->goodsManager = new GoodsManager();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $city = City::create([
            'name'  => 'Светлоград'
        ]);

        $miner = Miner::create([
            'name'  => 'Vano'
        ]);

        $goods = $this->goodsManager->addGoods([
            'city_id'       => $city->id,
            'goods_name'    => 'Реагент'
        ]);

        $goodsAddresses = $this->goodsManager->parseAddreses(file_get_contents(storage_path('app/goods')));

        foreach ($goodsAddresses as $address) {
            $this->goodsManager->addGoodsPrice([
                'goods_id'  => $goods->id,
                'miner_id'  => $miner->id,
                'weight'    => 0.5,
                'address'   => $address,
                'cost'      => 1500
            ]);
        }
    }
}
