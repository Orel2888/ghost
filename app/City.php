<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $table = 'citys';
    public $guarded = ['id'];

    public function goods()
    {
        return $this->hasMany('App\Goods');
    }
}
