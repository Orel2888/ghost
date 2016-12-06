<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoodsPurchase extends Model
{
    public $table = 'goods_purchases';
    public $guarded = ['id'];

    public function client()
    {
        return $this->hasOne('App\Client', 'id', 'client_id');
    }

    public function goods()
    {
        return $this->hasOne('App\Goods', 'id', 'goods_id');
    }
}
