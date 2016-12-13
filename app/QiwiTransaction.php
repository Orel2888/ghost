<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Client;

class QiwiTransaction extends Model
{
    public $table = 'qiwi_transactions';
    public $guarded = ['id'];
}
