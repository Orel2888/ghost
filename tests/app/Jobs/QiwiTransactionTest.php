<?php

use App\Jobs\QiwiTransaction;

class QiwiTransactionTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function test_qiwi_abuse_transaction()
    {
        $tran = $this->testTools->createTransaction(1000, 'abusecomment');

        dispatch(new QiwiTransaction(['transactions_ids_abuse' => [$tran->id]]));
    }
}