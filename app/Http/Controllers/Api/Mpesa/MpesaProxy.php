<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 21/01/2020
 * Time: 10:45
 */

namespace App\Http\Controllers\Api\Mpesa;

use Exception;
use GuzzleHttp\Client;

/**
 * Safaricom MPESA API wrapper
 * Class MpesaProxy
 * @package App\Http\Controllers\Api\Mpesa
 */
class MpesaProxy {

    protected $isLive, $shortCode, $safaricomSandboxUrl, $safaricomLiveUrl, $consumerKey,
        $consumerSecret, $initiatorUsername, $initiatorPassword, $testPhone, $securityCredential, $apiBaseUrl;

    /**
     * S# __construct() function
     *
     * Constructor
     *
     */
    public function __construct() {
        $this->isLive = false;
        $this->apiBaseUrl = 'https://january.robisignals.com/backend/api/v1';

        $this->safaricomSandboxUrl = 'https://sandbox.safaricom.co.ke';
        $this->safaricomLiveUrl = 'https://api.safaricom.co.ke';

        $this->shortCode = '601362';

        $this->consumerKey = 'BAUAi4032V3OCWNpNA7aRURX00Gj6lpe';
        $this->consumerSecret = 'DI23AkPVpAe1VAu3';

        $this->initiatorUsername = 'testapi113'; //your production username
        $this->initiatorPassword = "Safaricom007@"; //your production password

        $this->securityCredential = 'PTDCcx1+8obfpti1nYvGyTIkE5xIWn+MI0n5DiWwm/A94NiIPUFsVR7mZBfKS1lq+o7Wv+lUgDkMgjsgcuMrw+olS7od7sKHDrD0K2fKgcYtHbvR4XkLPuuWQLfSLR77XvXSHogPVMsa34URXVJ8NtFGmbEdV4zMpuF9XzXNf85EjhcfDgbz7kkYo0ocCarXowDEL2KDvRyevBJ/h9qiM/MZAt6oHupxVvRahlLt5iT3jhk9js84Zr+x9GhEchIhlgLQwUO75xSBuBLH9uv2TPMWhb5Q2UL7M7P4GjB0xYf6ETF6UrDHQ00jnD+5zb2Ql2mr9c7C3F2k72k7uHgYIA==';

        $this->testPhone = '254708374149'; //Test MSISDN
    }


    /**
     * @param $endPoint
     * @param $data
     * @return \Psr\Http\Message\ResponseInterface
     * @throws Exception
     */
    private function remotePostCall($endPoint, $data) {
        $client = new Client();

        $url = $this->isLive ? $this->safaricomLiveUrl.'/'.$endPoint : $this->safaricomSandboxUrl.'/'.$endPoint;

        return $client->post($url,
            [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $this->isLive ? $this->generateToken(true) : $this->generateToken()
                ],
                'body' => json_encode($data)
            ]);
    }

    /**
     * To make an API call, you will need to authenticate your app.
     * The given access_token is set to expire in a minute. So I see no need to store it. I request for each use.
     *
     * @param bool $liveApi
     * @return mixed
     * @throws Exception
     */
    public function generateToken($liveApi = false){

        $tokenLiveUrl = $this->safaricomLiveUrl.'/oauth/v1/generate?grant_type=client_credentials';
        $tokenSandboxUrl = $this->safaricomSandboxUrl.'/oauth/v1/generate?grant_type=client_credentials';

        $tokenUrl = $liveApi ? $tokenLiveUrl : $tokenSandboxUrl;
        try{
            $credentials = base64_encode($this->consumerKey.':'.$this->consumerSecret);

            $client = new Client();
            $response = $client->get($tokenUrl,
                [
                    'headers' => [
                        'Authorization' => 'Basic ' . $credentials,
                    ],
                ]);

            $response = (string) $response->getBody();
            $json = json_decode($response);
            $access_token = $json->access_token;

            return $access_token;
        }catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Whenever M-Pesa receives a transaction on the shortcode, M-Pesa triggers a validation request against the validation URL.
     * M-Pesa completes or cancels the transaction depending on the validation response it receives from the 3rd party system.
     * A confirmation request of the transaction is then sent by M-Pesa through the confirmation URL back to the 3rd party
     * which then should respond with a success acknowledging the confirmation.
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws Exception
     */
    public function registerUrlC2B() {
        $data = [
            'ShortCode'         =>  $this->shortCode,
            'ResponseType'      => ' ',
            'ConfirmationURL'   => $this->apiBaseUrl.'/mobilepay/c2b/confirmation',
            'ValidationURL'     => $this->apiBaseUrl.'/mobilepay/c2b/validation_url'
        ];
        return $this->remotePostCall('mpesa/c2b/v1/registerurl', $data);
    }

    /**
     * The Account Balance API requests for the account balance of a shortcode.
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function accountBalance() {
        $data = [
            'Initiator'             => $this->initiatorUsername,
            'SecurityCredential'    => $this->securityCredential,
            'CommandID'             => 'AccountBalance',
            'PartyA'                => $this->shortCode,
            'IdentifierType'        => '4',
            'Remarks'               => 'Checking Account Balance',
            'QueueTimeOutURL'       => $this->apiBaseUrl.'/mobilepay/bal/time_out',
            'ResultURL'             => $this->apiBaseUrl.'/mobilepay/bal/result'
        ];
        return $this->remotePostCall('mpesa/accountbalance/v1/query', $data);
    }

    /**
     * Transaction Status API checks the status of a B2B, B2C and C2B APIs transactions.
     * @return \Psr\Http\Message\ResponseInterface
     * @throws Exception
     */
    private function transactionStatus(){
        $data = [
            'Initiator'             => ' ',
            'SecurityCredential'    => ' ',
            'CommandID'             => 'TransactionStatusQuery',
            'TransactionID'         => ' ',
            'PartyA'                => ' ',
            'IdentifierType'        => '1',
            'QueueTimeOutURL'       => $this->apiBaseUrl.'/mobilepay/tran_status/time_out',
            'ResultURL'             => $this->apiBaseUrl.'/mobilepay/tran_status/result',
            'Remarks'               => ' ',
            'Occasion'              => ' '
        ];
        return $this->remotePostCall('mpesa/transactionstatus/v1/query', $data);
    }

    /**
     * Reverses a B2B, B2C or C2B M-Pesa transaction.
     * @return \Psr\Http\Message\ResponseInterface
     * @throws Exception
     */
    private function reverseTransaction() {
        $data = [
            'Initiator'                 => ' ',
            'SecurityCredential'        => ' ',
            'CommandID'                 => 'TransactionReversal',
            'TransactionID'             => ' ',
            'Amount'                    => ' ',
            'ReceiverParty'             => ' ',
            'RecieverIdentifierType'    => '4',
            'QueueTimeOutURL'           => $this->apiBaseUrl.'/mobilepay/tran_reverse/time_out',
            'ResultURL'                 => $this->apiBaseUrl.'/mobilepay/tran_reverse/result',
            'Remarks'                   => ' ',
            'Occasion'                  => ' '
        ];
        return $this->remotePostCall('mpesa/reversal/v1/request', $data);
    }

    /**
     * This simulation can be done directly via POSTMAN
     * @throws \Exception
     */
    public function simulateC2BTransaction() {
        $data = [
            'ShortCode'     => $this->shortCode,
            'CommandID'     => 'CustomerPayBillOnline',
            'Amount'        => '16000',
            'Msisdn'        => '0724475357',
            'BillRefNumber' => '00000'
        ];
        return $this->remotePostCall('mpesa/c2b/v1/simulate', $data);
    }

    /**
     * This API enables Business to Customer (B2C) transactions between
     * a company and customers who are the end-users of its products or services.
     *
     * @param $phone
     * @param $amount
     * @return \Psr\Http\Message\ResponseInterface
     * @throws Exception
     */
    public function sendMoneyB2C($phone, $amount){
        try{
          $data = [
                'InitiatorName'         => $this->initiatorUsername,
                'SecurityCredential'    => $this->securityCredential,
                'CommandID'             => 'BusinessPayment',
                'Amount'                => $amount,
                'PartyA'                => $this->shortCode,
                'PartyB'                => $phone,
                'Remarks'               => 'Salary',
                'QueueTimeOutURL'       => $this->apiBaseUrl.'/mobilepay/b2c_request/time_out',
                'ResultURL'             => $this->apiBaseUrl.'/mobilepay/b2c_request/result',
                'Occasion'              => 'Salary June'
            ];
            return $this->remotePostCall('mpesa/b2c/v1/paymentrequest', $data);
        }catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
