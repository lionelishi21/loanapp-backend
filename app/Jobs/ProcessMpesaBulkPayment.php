<?php

namespace App\Jobs;

use App\Http\Controllers\Api\Mpesa\MpesaProxy;
use App\Models\MpesaScheduledDisbursement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMpesaBulkPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $disbursement;

    /**
     * ProcessMpesaBulkPayment constructor.
     * @param MpesaScheduledDisbursement $disbursement
     */
    public function __construct(MpesaScheduledDisbursement $disbursement)
    {
        $this->disbursement = $disbursement;
    }

    /**
     * @param MpesaProxy $mpesaProxy
     * @throws \Exception
     */
    public function handle(MpesaProxy $mpesaProxy)
    {
        $mpesaProxy->sendMoneyB2C($this->disbursement->mpesa_number, $this->disbursement->amount);
    }
}
