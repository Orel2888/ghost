<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Miner extends Model
{
    public $table = 'miners';
    public $guarded  = ['id'];

    public function payments()
    {
        return $this->hasMany('App\MinerPayment');
    }
}
