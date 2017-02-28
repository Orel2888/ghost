<?php

class SystemApi extends BaseApi
{
    protected $apiMethodMaps = [
        'sys.processing_goods_orders',
        'sys.purse_update_balance'
    ];

    /**
     * @param Closure $closure
     * @return bool|mixed
     */
    public function sysProcessingGoodsOrders(Closure $closure)
    {
        return $this->run('sys.processing_goods_orders', 'GET', null, $closure);
    }

    /**
     * @param $params
     * @param Closure $closure
     * @return bool|mixed
     */
    public function sysPurseUpdateBalance($params, Closure $closure)
    {
        return $this->run('sys.purse_update_balance', 'POST', $params, $closure);
    }
}