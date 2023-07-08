<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 21/01/2020
 * Time: 11:03
 */

namespace App\Http\Controllers\Api\Mpesa;

use App\Events\Payment\PaymentReceived;
use App\Http\Controllers\Api\ApiController;
use App\SmartMicro\Repositories\Contracts\JournalInterface;
use App\SmartMicro\Repositories\Contracts\LoanInterface;
use App\SmartMicro\Repositories\Contracts\MemberInterface;

use App\SmartMicro\Repositories\Contracts\MpesaBulkPaymentInterface;
use App\SmartMicro\Repositories\Contracts\PaymentInterface;
use App\SmartMicro\Repositories\Contracts\PaymentMethodInterface;
use App\SmartMicro\Repositories\Contracts\WithdrawalInterface;
use App\Traits\CommunicationMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Contains Mpesa callbacks. We process responses here.
 * Class MpesaPaymentController
 * @package App\Http\Controllers\Api\Mpesa
 */
class MpesaPaymentController extends ApiController
{
    /**
     * @var MpesaProxy
     */
    protected $mpesaProxy, $paymentRepository, $mpesaPaymentMethod;

    protected $memberInterface, $loanInterface, $paymentMethodInterface, $mpesaBulkPaymentInterface, $journalRepository,
        $withdrawalRepository;

    /**
     * MpesaPaymentController constructor.
     * @param MpesaProxy $mpesaProxy
     * @param PaymentInterface $paymentRepository
     * @param MemberInterface $memberInterface
     * @param LoanInterface $loanInterface
     * @param PaymentMethodInterface $paymentMethodInterface
     * @param MpesaBulkPaymentInterface $mpesaBulkPaymentInterface
     * @param JournalInterface $journalRepository
     * @param WithdrawalInterface $withdrawalRepository
     */
    public function __construct(MpesaProxy $mpesaProxy, PaymentInterface $paymentRepository,
                                MemberInterface $memberInterface, LoanInterface $loanInterface,
                                PaymentMethodInterface $paymentMethodInterface, MpesaBulkPaymentInterface $mpesaBulkPaymentInterface,
                                JournalInterface $journalRepository, WithdrawalInterface $withdrawalRepository)
    {
        $this->paymentRepository = $paymentRepository;
        $this->mpesaProxy = $mpesaProxy;

        $this->memberInterface = $memberInterface;
        $this->loanInterface = $loanInterface;
        $this->paymentMethodInterface = $paymentMethodInterface;
        $this->mpesaBulkPaymentInterface = $mpesaBulkPaymentInterface;

        $this->journalRepository = $journalRepository;
        $this->withdrawalRepository = $withdrawalRepository;

        $this->mpesaPaymentMethod =  DB::table('payment_methods')
            ->where('payment_methods.name', '=', 'MPESA')
            ->first();
    }


    // This is test function
    public function index(Request $request){

        $data = '{
            "Result":{
                        "ResultType":0,
                        "ResultCode":0,
                        "ResultDesc":"The service request is processed successfully.",
                        "OriginatorConversationID":"11369-5305957-1",
                        "ConversationID":"AG_20190103_00004ad7c21029e28510",
                        "TransactionID":"NA30XPKKVCW",
                        "ResultParameters":{
                                    "ResultParameter":[
                            {
                                "Key":"TransactionAmount",
                               "Value":100
                            },
                            {
                                "Key":"TransactionReceipt",
                               "Value":"NA30XKHVCW"
                            },
                            {
                                "Key":"ReceiverPartyPublicName",
                               "Value":"2547XXXXXXX - JOHN DOE"
                            },
                            {
                                "Key":"TransactionCompletedDateTime",
                               "Value":"03.01.2019 17:48:32"
                            },
                            {
                                "Key":"B2CUtilityAccountAvailableFunds",
                               "Value":4425.00
                            },
                            {
                                "Key":"B2CWorkingAccountAvailableFunds",
                               "Value":0.00
                            },
                            {
                                "Key":"B2CRecipientIsRegisteredCustomer",
                               "Value":"Y"
                            },
                            {
                                "Key":"B2CChargesPaidAccountAvailableFunds",
                               "Value":0.00
                            }
                         ]
                      },
                      "ReferenceData":{
                                    "ReferenceItem":{
                                        "Key":"QueueTimeoutURL",
                            "Value":"http:\/\/internalapi.safaricom.co.ke\/mpesa\/b2cresults\/v1\/submit"
                         }
                      }
        }
}';

        $decodedData = json_decode($data);
      //  dd($decodedData);

        $resultVariables = [];
        // success
        if($decodedData->Result->ResultCode == 0){
            $resultParameters = $decodedData->Result->ResultParameters;
            $resultParameter = $resultParameters->ResultParameter;

            foreach ($resultParameter as $result) {
                $resultVariables[$result->Key] = $result->Value;
            }
        }

        $receiverPartyPublicName = $resultVariables['ReceiverPartyPublicName'];

        $phone = substr($receiverPartyPublicName, 0, 12);
        $phoneWithLeadingZero = str_replace("254",0, $phone);

        $phoneWithLeadingZeroxxx = '0724475357';


        $mpesaPaymentMethod =  $this->paymentMethodInterface->getWhere('name', 'MPESA');

        $mpesaPaymentMethod =  DB::table('payment_methods')
            ->where('payment_methods.name', '=', 'MPESA')
            ->first();




        dd($this->mpesaPaymentMethod);
       // dd($resultVariables);


        return $resultVariables;


      //  return json_decode($data)->Result;

        return $this->mpesaProxy->generateToken();
    }

    /**
     * This is optional (Will need to register with safcom for this). Validate the transaction before mpesa can complete it.
     * e.g we may need to check whther this customer should be making payment to our paybill number.
     *
     * @param Request $request
     * @return string
     */
    public function validationC2B(Request $request){
        Log::info('validation ... user profile for user: xxxx ');
        // Log::info($request->all());

        return 'at validation c2b';
    }

    /**
     * After payment is done, Mpesa sends its response here
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function confirmationC2B(Request $request){
        $data = $request->all();

        $memberId = null;
        $paymentMethodId = null;

        $mpesaPaymentMethod =  $this->paymentMethodInterface->getWhere('name', 'MPESA');
        $loan =  $this->loanInterface->getWhere('loan_reference_number', $data['InvoiceNumber']);

        $member = null;
        if (isset($loan))
            $member =  $this->memberInterface->getById($loan['member_id']);

        if(isset($mpesaPaymentMethod)){
            $paymentMethodId = $mpesaPaymentMethod['id'];
        }
        if(isset($member)){
            $memberId = $member['id'];
        }

        $confirmation = [
            'transaction_type'      => array_key_exists('TransactionType', $data) ? $data['TransactionType'] : null,
            'trans_id'              => array_key_exists('TransID', $data) ? $data['TransID'] : null,
            'trans_time'            => array_key_exists('TransTime', $data) ? $data['TransTime'] : null,
            'amount'                => array_key_exists('TransAmount', $data) ? $data['TransAmount'] : null,
            'business_short_code'   => array_key_exists('BusinessShortCode', $data) ? $data['BusinessShortCode'] : null,
            'bill_ref_number'       => array_key_exists('BillRefNumber', $data) ? $data['BillRefNumber'] : null,
            'invoice_number'        => array_key_exists('InvoiceNumber', $data) ? $data['InvoiceNumber'] : null,
            'mpesa_number'          => array_key_exists('MSISDN', $data) ? $data['MSISDN'] : null,
            'mpesa_first_name'      => array_key_exists('FirstName', $data) ? $data['FirstName'] : null,
            'mpesa_middle_name'     => array_key_exists('MiddleName', $data) ? $data['MiddleName'] : null,
            'mpesa_last_name'       => array_key_exists('LastName', $data) ? $data['LastName'] : null,
            'org_account_balance'   => array_key_exists('OrgAccountBalance', $data) ?  $data['OrgAccountBalance'] : null,
            'third_party_trans_id'  => array_key_exists('ThirdPartyTransID', $data) ? $data['ThirdPartyTransID'] : null,
            'is_mpesa'              => true,
            'method_id'             => $paymentMethodId,
            'member_id'             => $memberId,
        ];

        DB::beginTransaction();
        try
        {
            // New Payment - payments table
            $newPayment =  $this->paymentRepository->create($confirmation);

            // Journal entry for the deposit received
            $this->journalRepository->paymentReceivedEntryMpesa($newPayment);

            if(isset($newPayment))
                event(new PaymentReceived($newPayment['member_id']));
            DB::commit();

            // Send sms and email notification
            if(!is_null($member) && !is_null($newPayment))
                CommunicationMessage::send('payment_received', $member, $newPayment);

            return response()->json([
                'ResultCode' => 0,
                'ResultDescription' => 'Confirmation received successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * The path that stores information of time out transaction
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountBalanceQueueTimeOut(Request $request){
        Log::info('AccountBalanceQueueTimeOut ... .... ');
        Log::info($request->all());

        return response()->json([
            'ResultCode' => 00000000,
            'ResultDescription' => 'Confirmation received successfully'
        ]);
    }

    /**
     * The path that stores information of transactions
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountBalanceResult(Request $request){
        Log::info('AccountBalanceResult ... .... ');
        Log::info($request->all());

        return response()->json([
            'ResultCode' => 00000000,
            'ResultDescription' => 'Confirmation received successfully'
        ]);
    }

    /**
     * The path that stores information of time out transaction
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function b2CPaymentRequestTimeOut(Request $request) {
        Log::info('B2CPaymentRequestTimeOut ... .... ');
        Log::info($request->all());

        return response()->json([
            'ResultCode' => 00000000,
            'ResultDescription' => 'Confirmation received successfully'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function b2CPaymentRequestResult(Request $request) {
        $data = $request->all();

      //  $decodedData = json_decode($data);
        $decodedData = $data;
        //  dd($decodedData);

        $resultVariables = [];
        // success
        if($decodedData->Result->ResultCode == 0){
            $resultParameters = $decodedData->Result->ResultParameters;
            $resultParameter = $resultParameters->ResultParameter;

            foreach ($resultParameter as $result) {
                $resultVariables[$result->Key] = $result->Value;
            }

            $receiverPartyPublicName = $resultVariables['ReceiverPartyPublicName'];

            $phone = substr($receiverPartyPublicName, 0, 12);
            $phoneWithLeadingZero = str_replace("254",0, $phone);

            $member =  DB::table('members')
                ->where('members.phone', $phoneWithLeadingZero)
                ->first();

            // Record this as an MPESA Withdrawal
            if (isset($member)) {
                $withdrawalData = $this->withdrawalRepository->create([
                    'branch_id'         => $member->branch_id,
                    'member_id'         => $member->id,
                    'amount'            => array_key_exists('TransactionAmount', $resultVariables) ? $resultVariables['TransactionAmount'] : null,
                    'withdrawal_date'   => array_key_exists('TransactionCompletedDateTime', $resultVariables) ? $resultVariables['TransactionCompletedDateTime'] : null,
                    'method_id'         => $this->mpesaPaymentMethod->id,
                ]);
                if (isset($withdrawalData))
                    $this->journalRepository->withdrawalEntryMpesa($withdrawalData);
            }

            $confirmation = [
                'transaction_amount'                        => array_key_exists('TransactionAmount', $resultVariables) ? $resultVariables['TransactionAmount'] : null,
                'transaction_receipt'                       => array_key_exists('TransactionReceipt', $resultVariables) ? $resultVariables['TransactionReceipt'] : null,
                'b2C_recipientIs_registered_customer'       => array_key_exists('B2CRecipientIsRegisteredCustomer', $resultVariables) ? $resultVariables['B2CRecipientIsRegisteredCustomer'] : null,
                'b2C_charges_paid_account_available_funds'  => array_key_exists('B2CChargesPaidAccountAvailableFunds', $resultVariables) ? $resultVariables['B2CChargesPaidAccountAvailableFunds'] : null,
                'receiver_party_public_name'                => array_key_exists('ReceiverPartyPublicName', $resultVariables) ? $resultVariables['ReceiverPartyPublicName'] : null,
                'transaction_completed_date_time'           => array_key_exists('TransactionCompletedDateTime', $resultVariables) ? $resultVariables['TransactionCompletedDateTime'] : null,
                'b2C_utility_account_available_funds'       => array_key_exists('B2CUtilityAccountAvailableFunds', $resultVariables) ? $resultVariables['B2CUtilityAccountAvailableFunds'] : null,
                'b2C_working_account_available_funds'       => array_key_exists('B2CWorkingAccountAvailableFunds', $resultVariables) ? $resultVariables['B2CWorkingAccountAvailableFunds'] : null
            ];

            DB::beginTransaction();
            try
            {
                $this->mpesaBulkPaymentInterface->create($confirmation);
                DB::commit();

                return response()->json([
                    'ResultCode' => 00000000,
                    'ResultDescription' => 'Confirmation received successfully'
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                Log::info('B2CPaymentRequestResult ... .... ');
                Log::info($data);
                throw new \Exception($e->getMessage());
            }
        }
        Log::info('B2CPaymentRequestResult ... .... ');
        Log::info($request->all());
    }

    /**
     * The path that stores information of time out transaction
     * @param Request $request
     */
    public function transactionStatusQueueTimeOut(Request $request) {}

    /**
     * The path that stores information of transaction
     * @param Request $request
     */
    public function transactionStatusResult(Request $request) {}

    /**
     * The path that stores information of time out transaction
     * @param Request $request
     */
    public function reverseTransactionQueueTimeOut(Request $request) {}

    /**
     * The path that stores information of transaction
     * @param Request $request
     */
    public function reverseTransactionResult(Request $request) {}
}