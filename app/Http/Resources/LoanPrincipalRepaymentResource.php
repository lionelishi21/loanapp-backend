<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 21/09/2019
 * Time: 21:25
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanPrincipalRepaymentResource extends JsonResource
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
            'id' => $this->id,

            'branch_id'     => $this->branch_id,
            'loan_id'       => $this->loan_id,
            'loan'          => $this->loan,
            'period_count'  => $this->period_count,
            'due_date'      => formatDate($this->due_date),
            'amount'        => $this->amount,
            'paid_on'       => $this->paid_on,
            'paid_amount'   => $this->paid_amount,
            'balance'       => $this->balance,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
