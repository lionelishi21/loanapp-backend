<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 26/10/2018
 * Time: 12:18
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class LoanRequest extends BaseRequest
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
                        'branch_id'             => 'exists:branches,id',
                        'loan_reference_number' => '',
                        'loan_officer_id'       => 'required|exists:users,id',

                        'loan_application_id'   => 'required|exists:loan_applications,id|unique:loans,loan_application_id,NULL,id,deleted_at,NULL',
                        'member_id' => [
                            'required', 'exists:members,id',
                            Rule::unique('loans')->where(function ($query) {
                                $query->where('deleted_at', NULL)->where('end_date', NULL);
                            })
                        ],
                        'loan_type_id'          => 'required|exists:loan_types,id',
                        'interest_rate'         => 'required|numeric|between:0,99.99',
                        'interest_type_id'      => 'required',
                        'repayment_period'      => 'required',
                        'loan_status_id'        => '',
                        'approved_by_user_id'   => '',
                        'amount_approved'       => 'required',
                        'service_fee'           => 'nullable|numeric',
                        'disburse_amount'       => '',

                        'penalty_type_id'       => 'nullable',
                        'penalty_value'         => 'nullable|numeric',
                        'penalty_frequency_id'  => 'nullable',

                        'reduce_principal_early'    => '',

                        'loan_disbursed'        => '',
                        'start_date'            => 'required|date',
                        'end_date'              => 'nullable|date|after_or_equal:start_date',
                        'next_repayment_date'   => '',

                        'disburse_method_id'    => '',
                        'mpesa_number'          => '',
                        'mpesa_first_name'      => '',
                        'mpesa_middle_name'      => '',
                        'mpesa_last_name'      => '',

                        'bank_name'             => '',
                        'bank_branch'           => '',
                        'bank_account'          => '',
                        'other_banking_details' => '',

                        'payment_frequency_id'  => 'nullable|exists:payment_frequencies,id'
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'             => 'exists:branches,id',
                        'loan_officer_id'       => 'required|exists:users,id',

                        'loan_reference_number'                 => [Rule::unique('loans')->ignore($this->loan, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],

                        'loan_application_id'                 => ['exists:loan_applications,id', Rule::unique('loans')->ignore($this->loan, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'member_id'             => 'exists:members,id',
                        'loan_type_id'          => 'exists:loan_types,id',
                        'interest_rate'         => 'numeric|between:0,99.99',
                        'interest_type_id'      => '',
                        'repayment_period'      => '',
                        'loan_status_id'        => '',
                        'approved_by_user_id'   => '',
                        'amount_approved'       => '',
                        'service_fee'           => '',

                        'penalty_type_id'       => 'nullable',
                        'penalty_value'         => 'nullable|numeric',
                        'penalty_frequency_id'  => 'nullable',

                        'loan_disbursed'        => '',
                        'start_date'            => '',
                        'end_date'              => 'nullable|date|after_or_equal:start_date',
                        'next_repayment_date'         => '',
                        'payment_frequency_id'  => 'exists:payment_frequencies,id'
                    ];
                    break;
                }
            default:break;
        }

        return $rules;

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'member_id.unique' => 'This member has an active loan.',
        ];
    }
}