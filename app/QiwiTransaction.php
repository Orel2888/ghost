<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QiwiTransaction extends Model
{
    public $table = 'qiwi_transactions';
    public $guarded = ['id'];
}
