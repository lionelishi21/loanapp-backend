<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 22/01/2020
 * Time: 18:08
 */

namespace App\Http\Requests;

use App\Rules\MoreThanAccountBalance;

class WithdrawalRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'branch_id'             => 'exists:branches,id',
            'member_id'             => 'exists:members,id',
            'amount'                => ['required', 'numeric', 'min:0', 'not_in:0', new MoreThanAccountBalance(request()->member_id)],
            'withdrawal_date'       => '',
            'method_id'             => 'exists:payment_methods,id',
            'notes'             => '',
            'withdrawal_number'             => '',
            'withdrawal_charges'            => '',
            'balance_before_withdrawal'     => '',
            'balance_after_withdrawal'      => '',
            'status'                        => '',
            'mpesa_number'                  => '',
            'first_name'                    => '',
            'last_name'                     => '',

            // Bank fields
            'cheque_number' => '',
            'bank_name'     => '',
            'bank_branch'   => '',
            'cheque_date'   => '',
        ];
    }
}

