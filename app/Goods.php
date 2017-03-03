<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\GoodsPrice;

class Goods extends Model
{
    public $table = 'goods';
    public $guarded = ['id'];
    
    public function goodsPrice()
    {
        return $this->hasMany('App\GoodsPrice');
    }

    public function product()
    {
        return $this->hasMany(GoodsPrice::class);
    }

    public function city()
    {
        return $this->belongsTo('App\City', 'city_id', 'id');
    }
}
