<?php

namespace App\Ghost\Repositories\Common;

use App\BlackListUser as BlackListModel;

class BlackListUser
{

    public $blackListUser;

    public function __construct()
    {
        $this->blackListUser = new BlackListModel();
    }

    public function checkUsername($username)
    {
        return !is_null($this->blackListUser->whereUsername($username)->first());
    }

    public function checkPhone($phone)
    {
        return !is_null($this->blackListUser->wherePhone($phone)->first());
    }

    public function addBlackUser($value, $type)
    {
        $rows = [];

        $rows[$type]  = $value;
        $rows['type'] = $type;

        return $this->blackListUser->create($rows);
    }
}