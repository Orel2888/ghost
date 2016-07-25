<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public $table = 'clients';
    public $guarded = ['id'];

    public function goodsOrders()
    {
        return $this->hasMany('App\GoodsOrder');
    }
}
