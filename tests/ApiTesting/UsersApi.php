<?php

class UsersApi extends BaseApi
{
    protected $apiMethodMaps = [
        'users.find',
        'users.reg',
        'users.update',
        'users.purse'
    ];

    /**
     * @throws BaseApiException
     * @param $tg_chatid
     * @param $closure
     * @return mixed
     */
    public function usersFind($tg_chatid, Closure $closure)
    {
        return $this->run('users.find', 'GET', compact('tg_chatid'), $closure);
    }

    /**
     * @throws BaseApiException
     * @param $params
     * @param $closure
     * @return mixed
     */
    public function usersReg($params, Closure $closure)
    {
        return $this->run('users.reg', 'POST', $params, $closure);
    }

    /**
     * @param $params
     * @param Closure $closure
     * @return bool|mixed
     */
    public function usersUpdate($params, Closure $closure)
    {
        return $this->run('users.update', 'POST', $params, $closure);
    }

    /**
     * @param Closure $closure
     * @return bool|mixed
     */
    public function usersPurse(Closure $closure)
    {
        return $this->run('users.purse', 'GET', null, $closure);
    }
}