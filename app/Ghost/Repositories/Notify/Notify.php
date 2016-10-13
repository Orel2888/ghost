<?php

namespace App\Ghost\Notify;

use App\Admin;

abstract class Notify
{
    public function getAdmins()
    {
        return Admin::all();
    }
}