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

    public function goods()
    {
        return $this->hasOne('App\Goods', 'id', 'goods_id');
    }

    public function purchase()
    {
        return $this->hasOne('App\GoodsPurchase', 'id', 'purchase_id');
    }
}
