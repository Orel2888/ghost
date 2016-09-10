<?php

namespace App;

class QiwiTransactionAbuse extends \Eloquent
{
    public $table   = 'qiwi_transactions_abuse';
    public $guarded = ['id'];
}