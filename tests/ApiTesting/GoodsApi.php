<?php

class GoodsApi extends BaseApi
{
    protected $apiMethodMaps = [
        'goods.pricelist'
    ];

    /**
     * @param Closure $closure
     * @return bool|mixed
     */
    public function goodsPricelist(Closure $closure)
    {
        return $this->run('goods.pricelist', 'GET', null, $closure);
    }
}