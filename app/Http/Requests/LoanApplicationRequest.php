<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 26/10/2018
 * Time: 12:26
 */

namespace App\Http\Requests;

use App\Rules\MpesaNumberRule;

class LoanApplicationRequest extends BaseRequest
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
                        'branch_id'                     => 'exists:branches,id',
                        'member_id'                     => 'required|exists:members,id',
                        'loan_officer_id'               => 'required|exists:users,id',

                        'loan_type_id'                  => 'required|exists:loan_types,id',
                        'interest_type_id'              => 'exists:interest_types,id',
                        'service_fee'                   => 'nullable|numeric',

                        'penalty_type_id'       => 'nullable',
                        'penalty_value'         => 'nullable|numeric',
                        'penalty_frequency_id'  => 'nullable',

                        'reduce_principal_early'    => '',

                        'amount_applied'                => 'required|numeric|min:3|max:999999999',

                        'interest_rate'                 => 'nullable|numeric',
                        'repayment_period'              => 'nullable|numeric',
                        'payment_frequency_id'          => 'exists:payment_frequencies,id',
                        'periodic_payment_amount'       => 'nullable|numeric',

                        'application_date'              => 'required|date',
                        'disburse_method_id'            => 'required|exists:payment_methods,id',
                        'disburse_note'         => '',

                        'mpesa_number'                => [new MpesaNumberRule(request()->disburse_method_id)],

                        'mpesa_first_name'  => '',
                        'mpesa_middle_name'  => '',
                        'mpesa_last_name'   => '',

                        // bank fields
                        'cheque_number'     => '',
                        'bank_name'         => '',
                        'bank_branch'       => '',
                        'cheque_date'       => '',

                        'witness_type_id'               => '',
                        'witness_first_name'            => '',
                        'witness_last_name'             => '',
                        'witness_country'               => '',
                        'witness_county'                => '',
                        'witness_city'                  => '',
                        'witness_national_id'           => '',
                        'witness_phone'                 => '',
                        'witness_email'                 => '',
                        'witness_postal_address'        => '',
                        'witness_residential_address'   => '',
                        'status_id'                     => '',
                        'witnessed_by_user_id'          => '',
                        'reviewed_by_user_id'           => '',
                        'reviewed_on'                   => '',
                        'approved_on'                   => '',
                        'rejected_on'                   => '',
                        'rejection_notes'               => '',
                        'attach_application_form'       => 'nullable|file|mimes:doc,pdf,docx,zip|max:10000'
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'                     => 'exists:branches,id',
                        'member_id'                     => 'required|exists:members,id',
                        'loan_officer_id'               => 'required|exists:users,id',

                        'loan_type_id'                  => 'required|exists:loan_types,id',
                        'interest_type_id'              => 'required|exists:interest_types,id',
                        'service_fee'                   => '',

                        'penalty_type_id'       => 'nullable',
                        'penalty_value'         => 'nullable|numeric',
                        'penalty_frequency_id'  => 'nullable',

                        'amount_applied'                => 'required',
                        'interest_rate'                 => '',
                        'repayment_period'              => '',
                        'payment_frequency'             => '',
                        'periodic_payment_amount'       => '',

                        'application_date'              => 'required',
                        'disburse_method_id'            => 'required|exists:payment_methods,id',
                        'disburse_note'         => '',

                        //mpesa field
                        'mpesa_number'      => '',
                        'mpesa_first_name'  => '',
                        'mpesa_middle_name'  => '',
                        'mpesa_last_name'   => '',

                        // bank fields
                        'cheque_number'=> '',
                        'bank_name'=> '',
                        'bank_branch'=> '',
                        'cheque_date'=> '',

                        'witness_type_id'               => '',
                        'witness_first_name'            => '',
                        'witness_last_name'             => '',
                        'witness_country'               => '',
                        'witness_county'                => '',
                        'witness_city'                  => '',
                        'witness_national_id'           => '',
                        'witness_phone'                 => '',
                        'witness_email'                 => '',
                        'witness_postal_address'        => '',
                        'witness_residential_address'   => '',
                        'status_id'                     => '',
                        'witnessed_by_user_id'          => '',
                        'reviewed_by_user_id'           => '',
                        'reviewed_on'                   => '',
                        'approved_on'                   => '',
                        'rejected_on'                   => '',
                        'rejection_notes'               => '',
                        'attach_application_form'       => 'nullable|file|mimes:doc,pdf,docx,zip|max:10000'
                    ];
                    break;
                }
            default:break;
        }
        return $rules;
    }
}