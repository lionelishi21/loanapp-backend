<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 26/10/2018
 * Time: 12:17
 */

namespace App\Http\Resources;

class LoanResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                    => $this->id,
            'branch_id'             => $this->branch_id,
            'loan_reference_number' => $this->loan_reference_number,
            'loan_application_id'   => $this->loan_application_id,
            'member_id'             => $this->member_id,
            'loan_officer_id'             => $this->loan_officer_id,
            'loanOfficer'           => UserResource::make($this->loanOfficer),
            'loan_type_id'          => $this->loan_type_id,
            'loanType'              => $this->loanType,
            'balance'               => $this->balance,
            'paid_amount'           => $this->paid_amount,
            'member'                => MemberResource::make($this->member),
            'interest_rate'         => $this->interest_rate,
            'interest_type_id'      => $this->interest_type_id,
            'repayment_period'      => $this->repayment_period,
            'loan_status_id'        => $this->loan_status_id,
            'approved_by_user_id'   => $this->approved_by_user_id,

            'amount_approved'       => $this->amount_approved,
            'amount_approved_display'       => $this->formatMoney($this->amount_approved),

            'service_fee'           => $this->service_fee,
            'disburse_amount'           => $this->disburse_amount,

            // penalties
            'penalty_type_id'       => $this->penalty_type_id,
            'penalty_value'         => $this->penalty_value,
            'penalty_frequency_id'  => $this->penalty_frequency_id,
            'reduce_principal_early'  => $this->reduce_principal_early,
            'loan_disbursed'        => $this->loan_disbursed,
            'start_date'            => formatDate($this->start_date),
            'end_date'              => formatDate($this->end_date),
            'payment_frequency_id'  => $this->payment_frequency_id,
            'paymentFrequency'      => $this->paymentFrequency,
            'next_repayment_date'   => formatDate($this->next_repayment_date),
            'amortization'          => $this->amortization,
            'disburse_method_id'    => $this->disburse_method_id,
            'mpesa_number'          => $this->mpesa_number,
            'mpesa_first_name'      => $this->mpesa_first_name,
            'mpesa_middle_name'      => $this->mpesa_middle_name,
            'mpesa_last_name'      => $this->mpesa_last_name,
            'bank_name'             => $this->bank_name,
            'bank_branch'           => $this->bank_branch,
            'bank_account'          => $this->bank_account,
            'other_banking_details' => $this->other_banking_details,
            'created_by'            => $this->created_by,
            'updated_by'            => $this->updated_by,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
