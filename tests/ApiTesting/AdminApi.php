<?php

class AdminApi extends BaseApi
{
    protected $apiMethodMaps = [
        'qiwi.transaction',
        'product.purchase',
        'product.available',
        'product.list',
        'purse',
        'purse.set'
    ];

    public function __construct()
    {
        $this->baseApiUrl = env('API_URL') .'/admin/';

        parent::__construct();
    }

    /**
     * @param Closure $closure
     * @return bool|mixed
     */
    public function qiwiTransaction(Closure $closure)
    {
        return $this->run('qiwi.transaction', 'GET', null, $closure);
    }

    /**
     * @param $params
     * @param Closure $closure
     * @return bool|mixed
     */
    public function productPurchase($params, Closure $closure)
    {
        return $this->run('product.purchase', 'POST', $params, $closure);
    }

    /**
     * @param Closure $closure
     * @return bool|mixed
     */
    public function productAvailable(Closure $closure)
    {
        return $this->run('product.available', 'GET', null, $closure);
    }

    /**
     * @param Closure $closure
     * @return bool|mixed
     */
    public function productList(Closure $closure)
    {
        return $this->run('product.list', 'GET', null, $closure);
    }

    /**
     * @param Closure $closure
     * @return bool|mixed
     */
    public function purse(Closure $closure)
    {
        return $this->run('purse', 'GET', null, $closure);
    }

    /**
     * @param $id
     * @param Closure $closure
     * @return bool|mixed
     */
    public function purseSet($id, Closure $closure)
    {
        return $this->run('purse.set', 'GET', compact('id'), $closure);
    }
}