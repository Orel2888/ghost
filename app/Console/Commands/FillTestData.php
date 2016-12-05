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
    protected $signature = 'fill_test_data {type}';

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
        if ($this->argument('type') == 'fill') {

            $datas = json_decode(\Crypt::decrypt(file_get_contents(storage_path('goods_test'))), true);

            $cities = $datas['cities'];
            $goodsTypes = $datas['goods_types'];
            $miners = $datas['miners'];
            $goodsStock = $datas['goods_stock'];

            foreach ($miners as $minerName) {
                Miner::create([
                    'name'  => $minerName
                ]);
            }

            $i = 0;
            foreach ($cities as $city) {

                $city = City::create([
                    'name'  => $city
                ]);

                foreach ($goodsTypes as $goodsType) {
                    $goods = $this->goodsManager->addGoods([
                        'city_id'       => $city->id,
                        'goods_name'    => $goodsType
                    ]);

                    foreach ($goodsStock[$goodsType] as $goodsItem) {
                        $this->goodsManager->addGoodsPrice([
                            'goods_id'  => $goods->id,
                            'miner_id'  => Miner::where('name', $goodsItem['miner'])->first()->id,
                            'weight'    => $goodsItem['weight'],
                            'address'   => $goodsItem['address'],
                            'cost'      => $goodsItem['cost']
                        ]);
                    }
                }

                if ($i == 0) break;

                $i++;
            }

            $this->info('OK');
        }

        if ($this->argument('type') == 'flush') {
            \DB::statement('DELETE FROM citys');
            \DB::statement('DELETE FROM miners');

            $this->info('OK');
        }

        if ($this->argument('type') == 'newgoods') {

            $goods = $this->goodsManager->addGoods([
                'city_id'       => 3,
                'goods_name'    => 'Ск'
            ]);

            $goodsPrice = $this->goodsManager->parseAddreses(file_get_contents(storage_path('newgoods')));

            foreach ($goodsPrice as $address) {
                $this->goodsManager->addGoodsPrice([
                    'goods_id'  => $goods->id,
                    'miner_id'  => 2,
                    'weight'    => 0.33,
                    'address'   => $address,
                    'cost'      => 1000
                ]);
            }
        }
    }
}
