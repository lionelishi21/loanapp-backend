<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 14:42
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'fiscal_period_id'              => $this->fiscal_period_id,
            'loan_id'                       => $this->loan_id,
            'loan'                          => $this->loan,

            'payment_id'                    => $this->payment_id,
            'payment'                       => PaymentResource::make($this->payment),
            'amount'                        => $this->amount,
            'transaction_date'              => formatDate($this->transaction_date),

            'loan_interest_repayments_id'   => $this->loan_interest_repayments_id,
            'loan_principal_repayments_id'  => $this->loan_principal_repayments_id,
            'loan_penalties_id'             => $this->loan_penalties_id,

            'transaction_type'              => $this->transaction_type,

            'created_by'                    => $this->created_by,
            'updated_by'                    => $this->updated_by,
            'deleted_by'                    => $this->deleted_by,

            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at
        ];
    }
}
