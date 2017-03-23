<?php

class OrderApi extends BaseApi
{
    protected $apiMethodMaps = [
        'order.create',
        'order.find',
        'order.list',
        'order.del',
        'order.delall'
    ];

    /**
     * @param array $params
     * @param Closure $closure
     * @return bool|mixed
     */
    public function orderCreate(array $params, Closure $closure)
    {
        return $this->run('order.create', 'POST', $params, $closure);
    }

    /**
     * @param $params
     * @param Closure $closure
     * @return bool|mixed
     */
    public function orderFind($params, Closure $closure)
    {
        return $this->run('order.find', 'GET', $params, $closure);
    }

    /**
     * @param $client_id
     * @param $status
     * @param Closure $closure
     * @return bool|mixed
     */
    public function orderList($client_id, $status = null, Closure $closure)
    {
        $formParams = compact('client_id');

        if (!is_null($status)) {
            $formParams += compact('status');
        }

        return $this->run('order.list', 'GET', $formParams, $closure);
    }

    /**
     * @param $params
     * @param Closure $closure
     * @return bool|mixed
     */
    public function orderDel($params, Closure $closure)
    {
        return $this->run('order.del', 'POST', $params, $closure);
    }

    /**
     * @param $client_id
     * @param Closure $closure
     * @return bool|mixed
     */
    public function orderDelAll($client_id, Closure $closure)
    {
        return $this->run('order.delall', 'POST', compact('client_id'), $closure);
    }
}