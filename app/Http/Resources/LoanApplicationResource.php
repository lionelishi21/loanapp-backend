<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 26/10/2018
 * Time: 12:26
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanApplicationResource extends JsonResource
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
            'id'                => $this->id,
            'branch_id'         => $this->branch_id,

            'member_id'         => $this->member_id,
            'loan_officer_id'         => $this->loan_officer_id,
            'loanOfficer'           => UserResource::make($this->loanOfficer),
            'member'            => $this->member,
            'guarantors'        => $this->guarantors,
            'assets'            => $this->assets,
            'interestType'      => $this->interestType,
            'loanType'          => LoanTypeResource::make($this->loanType),
            'loan'              => $this->loan,

            'loan_type_id'                  => $this->loan_type_id,
            'interest_type_id'              => $this->interest_type_id,
            'service_fee'                   => $this->service_fee,
            'service_fee_display'                   => formatMoney($this->service_fee),

            'penalty_type_id'       => $this->penalty_type_id,
            'penalty_value'         => $this->penalty_value,
            'penalty_frequency_id'  => $this->penalty_frequency_id,

            'reduce_principal_early'  => $this->reduce_principal_early,

            'amount_to_disburse'                => ($this->amount_applied) - ($this->service_fee),
            'amount_to_disburse_display'                => formatMoney(($this->amount_applied) - ($this->service_fee)),

            'amount_applied'                => $this->amount_applied,
            'amount_applied_display'                => formatMoney($this->amount_applied),

            'interest_rate'                 => $this->interest_rate,
            'repayment_period'              => $this->repayment_period,
            'payment_frequency_id'          => $this->payment_frequency_id,
            'paymentFrequency'              => $this->paymentFrequency,
            'periodic_payment_amount'       => $this->periodic_payment_amount,

            'application_date'              => $this->application_date,
            'application_date_display'      => formatDate($this->application_date),

            'disburse_method_id'            => $this->disburse_method_id,
            'disburseMethod'            => $this->disburseMethod,
            'disburse_note'         => $this->disburse_note,

            //mpesa field
            'mpesa_number'      => $this->mpesa_number,
            'mpesa_first_name'  => $this->mpesa_first_name,
            'mpesa_middle_name'  => $this->mpesa_middle_name,
            'mpesa_last_name'   => $this->mpesa_last_name,

            // bank fields
            'cheque_number'     => $this->cheque_number,
            'bank_name'         => $this->bank_name,
            'bank_branch'       => $this->bank_branch,
            'cheque_date'       => formatDate($this->cheque_date),

            'witness_type_id'               => $this->witness_type_id,
            'witnessType'               => $this->witnessType,
            'witness_first_name'            => $this->witness_first_name,
            'witness_last_name'             => $this->witness_last_name,
            'witness_country'               => $this->witness_country,
            'witness_county'                => $this->witness_county,
            'witness_city'                  => $this->witness_city,
            'witness_national_id'           => $this->witness_national_id,
            'witness_phone'                 => $this->witness_phone,
            'witness_email'                 => $this->witness_email,
            'witness_postal_address'        => $this->witness_postal_address,
            'witness_residential_address'   => $this->witness_residential_address,

            'status_id'                     => $this->status_id,
            'witnessed_by_user_id'          => $this->witnessed_by_user_id,
            'reviewed_by_user_id'           => $this->reviewed_by_user_id,
            'reviewed_on'                   => $this->reviewed_on,
            'reviewed_on_display'                   => formatDate($this->reviewed_on),
            'approved_on'                   => $this->approved_on,
            'rejected_on'                   => $this->rejected_on,
            'rejection_notes'               => $this->rejection_notes,
            'attach_application_form'       => $this->attach_application_form,

            'status' => $this->when($this->reviewed_on, function () {
                return $this->approved_on ? 'Approved' : 'Rejected';
            }),

            'created_by'        => $this->created_by,
            'updated_by'        => $this->updated_by,

            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
        ];
    }
}
