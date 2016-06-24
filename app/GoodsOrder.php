<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoodsOrder extends Model
{
    public $table = 'goods_orders';
    public $guarded = ['id'];

    public function client()
    {
        return $this->hasOne('App\Client', 'id', 'client_id');
    }
}
