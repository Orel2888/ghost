<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoodsReserve extends Model
{
    public $table = 'goods_reserved';
    public $guarded = ['id'];
}
