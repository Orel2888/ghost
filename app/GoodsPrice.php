<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Ghost\Libs\GibberishAES;

class GoodsPrice extends Model
{
    public $table = 'goods_price';
    public $guarded = ['id'];

    public function goods()
    {
        return $this->belongsTo('App\Goods', 'goods_id', 'id');
    }

    public function getAddressAttribute($value)
    {
        if (base64_encode(base64_decode($value)) === $value) {
            return GibberishAES::dec($value, env('K5'));
        } else {
            return $value;
        }
    }
}
