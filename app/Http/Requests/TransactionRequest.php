<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 14:41
 */

namespace App\Http\Requests;

class TransactionRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [];

        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                    break;
                }
            case 'POST':
                {
                    $rules = [
                        'branch_id'                     => 'exists:branches,id',
                        'fiscal_period_id'              => '',
                        'loan_id'                       => 'required|exists:loans,id',

                        'payment_id'                    => 'exists:payments,id',
                        'amount'                        => 'required',
                        'transaction_date'              => 'required',

                        'loan_interest_repayments_id'   => 'exists:loan_interest_repayments,id',
                        'loan_principal_repayments_id'  => 'exists:loan_principal_repayments,id',
                        'loan_penalties_id'             => 'exists:loan_penalties,id',

                        'transaction_type'              => '',

                        'created_by'                    => '',
                        'updated_by'                    => '',
                        'deleted_by'                    => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'                     => 'exists:branches,id',
                        'fiscal_period_id'              => '',
                        'loan_id'                       => 'required|exists:loans,id',

                        'payment_id'                    => 'exists:payments,id',
                        'amount'                        => 'required',
                        'transaction_date'              => 'required',

                        'loan_interest_repayments_id'   => 'exists:loan_interest_repayments,id',
                        'loan_principal_repayments_id'  => 'exists:loan_principal_repayments,id',
                        'loan_penalties_id'             => 'exists:loan_penalties,id',

                        'transaction_type'              => '',

                        'created_by'                    => '',
                        'updated_by'                    => '',
                        'deleted_by'                    => ''
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}