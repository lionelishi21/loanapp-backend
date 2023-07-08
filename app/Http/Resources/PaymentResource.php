<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 16/12/2018
 * Time: 11:12
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'branch'            => $this->branch,

            'member_id'         => $this->member_id,
            'amount'            => $this->amount,
            'method_id'         => $this->method_id,
            'transaction_id'    => $this->transaction_id,
            'payment_date'      => $this->payment_date,
            'receipt_number'    => $this->receipt_number,
            'attachment'        => $this->attachment,
            'notes'             => $this->notes,

            //bank fields
            'cheque_number'     => $this->cheque_number,
            'bank_name'         => $this->bank_name,
            'bank_branch'       => $this->bank_branch,
            'cheque_date'       => $this->cheque_date,

            'member'            => $this->member,
            'paymentMethod'     => $this->paymentMethod,

            // Mpesa fields
            'is_mpesa'              => $this->is_mpesa,
            'transaction_type'      => $this->transaction_type,
            'trans_id'              => $this->trans_id,
            'trans_time'            => $this->trans_time,
            'business_short_code'   => $this->business_short_code,
            'bill_ref_number'       => $this->bill_ref_number,
            'invoice_number'        => $this->invoice_number,  //loan_id or account number

            'mpesa_number'          => $this->mpesa_number,
            'mpesa_first_name'            => $this->mpesa_first_name,
            'mpesa_middle_name'           => $this->mpesa_middle_name,
            'mpesa_last_name'             => $this->mpesa_last_name,

            'org_account_balance'   => $this->org_account_balance,
            'third_party_trans_id'  => $this->third_party_trans_id,

            'created_by'        => $this->created_by,
            'updated_by'        => $this->updated_by,

            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at
        ];
    }
}
