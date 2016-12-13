<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\QiwiTransaction as QiwiTransactionModel;
use App\Events\QiwiTransaction as QiwiTransactionEvent;

class QiwiTransaction extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    public $dataProcessing;

    /**
     * Create a new job instance.
     * @param $dataProcessing array
     * @return void
     */
    public function __construct(array $dataProcessing)
    {
        //
        $this->dataProcessing = $dataProcessing;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        if (!empty($this->dataProcessing['transactions_ids_abuse'])) {
            $qiwiTransactions = QiwiTransactionModel::findMany($this->dataProcessing['transactions_ids_abuse']);

            event(new QiwiTransactionEvent($qiwiTransactions));
        }
    }
}
