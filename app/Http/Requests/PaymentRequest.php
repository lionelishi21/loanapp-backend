<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 16/12/2018
 * Time: 11:13
 */

namespace App\Http\Requests;

class PaymentRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [];

        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                    break;
                }
            case 'POST':
                {
                    $rules = [
                        'branch_id'         => 'exists:branches,id',
                        'member_id'         => 'exists:members,id',
                        'amount'            => 'required|numeric|min:0|not_in:0',
                        'method_id'         => 'exists:payment_methods,id',
                        'transaction_id'    => '',
                        'payment_date'      => 'required',
                        'receipt_number'    => '',
                        'attachment'        => '',
                        'notes'             => '',

                        // Bank fields
                        'cheque_number' => '',
                        'bank_name'     => '',
                        'bank_branch'   => '',
                        'cheque_date'   => '',

                        // Mpesa fields
                        'is_mpesa'              => '',
                        'transaction_type'      => '',
                        'trans_id'              => '',
                        'trans_time'            => '',
                        'business_short_code'   => '',
                        'bill_ref_number'       => '',
                        'invoice_number'        => '',  //loan_id or account number

                        'phone_number'          => '',
                        'first_name'            => '',
                        'middle_name'           => '',
                        'last_name'             => '',

                        'mpesa_number'          => '',
                        'mpesa_first_name'      => '',
                        'mpesa_middle_name'     => '',
                        'mpesa_last_name'       => '',

                        'org_account_balance'   => '',
                        'third_party_trans_id'  => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'         => 'exists:branches,id',
                        'member_id'         => 'exists:members,id',
                        'amount'            => 'required|numeric|min:0|not_in:0',
                        'method_id'         => 'exists:payment_methods,id',
                        'transaction_id'    => '',
                        'payment_date'      => 'required',
                        'receipt_number'    => '',
                        'attachment'        => '',
                        'notes'             => '',

                        // Bank fields
                        'cheque_number' => '',
                        'bank_name'     => '',
                        'bank_branch'   => '',
                        'cheque_date'   => '',

                        // Mpesa fields
                        'is_mpesa'              => '',
                        'transaction_type'      => '',
                        'trans_id'              => '',
                        'trans_time'            => '',
                        'business_short_code'   => '',
                        'bill_ref_number'       => '',
                        'invoice_number'        => '',  //loan_id or account number

                        'phone_number'          => '',
                        'first_name'            => '',
                        'middle_name'           => '',
                        'last_name'             => '',

                        'mpesa_number'          => '',
                        'mpesa_first_name'      => '',
                        'mpesa_middle_name'     => '',
                        'mpesa_last_name'       => '',

                        'org_account_balance'   => '',
                        'third_party_trans_id'  => ''
                    ];
                    break;
                }
            default:break;
        }

        return $rules;

    }
}