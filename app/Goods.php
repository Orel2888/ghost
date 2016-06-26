<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Ghost\Libs\GibberishAES;

class Goods extends Model
{
    public $table = 'goods';
    public $guarded = ['id'];

    public function getNameAttribute($value)
    {
        if (base64_encode(base64_decode($value)) === $value) {
            return GibberishAES::dec($value, env('K5'));
        } else {
            return $value;
        }
    }

    public function goodsPrice()
    {
        return $this->hasMany('App\GoodsPrice');
    }

    public function city()
    {
        return $this->belongsTo('App\City', 'city_id', 'id');
    }
}
