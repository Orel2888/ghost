<?php

namespace  App;

class BlackListUser extends \Eloquent
{
    public $table = 'black_list_users';
    public $guarded = ['id'];

}