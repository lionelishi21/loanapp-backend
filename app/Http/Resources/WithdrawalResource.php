<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 22/01/2020
 * Time: 18:08
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                            => $this->id,

            'branch_id'                     => $this->branch_id,
            'branch'         => $this->branch,
            'member_id'                     => $this->member_id,
            'member'         => $this->member,
            'amount'                        => $this->amount,
            'withdrawal_date'               => $this->withdrawal_date,
            'withdrawal_number'             => $this->withdrawal_number,
            'method_id'                     => $this->method_id,
            'paymentMethod'  => $this->paymentMethod,
            'withdrawal_charges'            => $this->withdrawal_charges,
            'balance_before_withdrawal'     => $this->balance_before_withdrawal,
            'balance_after_withdrawal'      => $this->balance_after_withdrawal,
            'status'                        => $this->status,

            'mpesa_number'                  => $this->mpesa_number,
            'first_name'                    => $this->first_name,
            'last_name'                     => $this->last_name,

            //bank fields
            'cheque_number'     => $this->cheque_number,
            'bank_name'         => $this->bank_name,
            'bank_branch'       => $this->bank_branch,
            'cheque_date'       => $this->cheque_date,

            'created_by'                    => $this->created_by,
            'updated_by'                    => $this->updated_by,
            'deleted_by'                    => $this->deleted_by,

            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at
        ];
    }
}
