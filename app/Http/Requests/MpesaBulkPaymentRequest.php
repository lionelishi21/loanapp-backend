<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 24/01/2020
 * Time: 09:21
 */

namespace App\Http\Requests;

class MpesaBulkPaymentRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'transaction_amount'                        => '',
            'transaction_receipt'                       => '',
            'b2C_recipientIs_registered_customer'       => '',
            'b2C_charges_paid_account_available_funds'  => '',
            'receiver_party_public_name'                => '',
            'transaction_completed_date_time'           => '',
            'b2C_utility_account_available_funds'       => '',
            'b2C_working_account_available_funds'       => ''
        ];
    }
}

