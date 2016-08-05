<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoodsPrice extends Model
{
    public $table = 'goods_price';
    public $guarded = ['id'];

    public function goods()
    {
        return $this->belongsTo('App\Goods', 'goods_id', 'id');
    }
}
