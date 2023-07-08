<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 31/01/2020
 * Time: 10:18
 */

namespace App\Http\Controllers\Api\Mpesa;


use App\Http\Controllers\Api\ApiController;
use App\SmartMicro\Repositories\Contracts\MpesaBulkPaymentInterface;
use App\SmartMicro\Repositories\Contracts\PaymentInterface;
use Illuminate\Http\Request;

class MpesaSummaryController extends ApiController
{
    protected $mpesaProxy, $mpesaBulkPaymentRepository, $paymentRepository;

    /**
     * MpesaSummaryController constructor.
     * @param MpesaProxy $mpesaProxy
     * @param MpesaBulkPaymentInterface $mpesaBulkPaymentRepository
     * @param PaymentInterface $paymentRepository
     */
    public function __construct(MpesaProxy $mpesaProxy, MpesaBulkPaymentInterface $mpesaBulkPaymentRepository,
                                PaymentInterface $paymentRepository)
    {
        $this->mpesaProxy = $mpesaProxy;
        $this->mpesaBulkPaymentRepository = $mpesaBulkPaymentRepository;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $transactionCount = count($this->mpesaBulkPaymentRepository->getAll());
        $customerCount = $this->mpesaBulkPaymentRepository->customerCount();
        $totalDisbursement = $this->mpesaBulkPaymentRepository->transactionValue();

        $totalReceivedViaMpesa = $this->paymentRepository->totalMpesaDeposits();

        $data = [];

        $data['transaction_count'] = $transactionCount;
        $data['customer_count'] = $customerCount;
        $data['total_disbursement'] = $this->formatMoney($totalDisbursement);
        $data['total_mpesa_received'] = $totalReceivedViaMpesa;

        return $data;
    }

    /**
     * @throws \Exception
     */
    public function mpesaBalance() {
        $this->mpesaProxy->accountBalance();
    }
}