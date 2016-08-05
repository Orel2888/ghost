<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Goods;
use App\GoodsPrice;
use App\Ghost\Libs\GibberishAES;

class PathUpdateDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pathupdatedb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $goodsStock = GoodsPrice::all();

        foreach ($goodsStock as $goods) {
            $goods->update(['address' => GibberishAES::dec($goods->address, env('K5'))]);
        }

        $goodsCategories = Goods::all();

        foreach ($goodsCategories as $goods) {
            $goods->update(['name' => GibberishAES::dec($goods->name, env('K5'))]);
        }

        $this->info('OK');
    }
}
