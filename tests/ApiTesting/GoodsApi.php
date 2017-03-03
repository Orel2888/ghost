<?php

class GoodsApi extends BaseApi
{
    protected $apiMethodMaps = [
        'goods.pricelist',
        'goods.list'
    ];

    /**
     * @param Closure $closure
     * @return bool|mixed
     */
    public function goodsPricelist(Closure $closure)
    {
        return $this->run('goods.pricelist', 'GET', null, $closure);
    }

    public function goodsList(Closure $closure)
    {
        return $this->run('goods.list', 'GET', null, $closure);
    }
}