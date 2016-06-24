<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public $table = 'clients';
    public $guarded = ['id'];

}
