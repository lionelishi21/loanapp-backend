<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 26/10/2018
 * Time: 12:22
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanTypeResource extends JsonResource
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
            'name'                  => $this->name,
            'description'           => $this->description,
            'active_status'         => $this->active_status,
            'interest_rate'         => $this->interest_rate,
            'interest_type_id'      => $this->interest_type_id,
            'payment_frequency_id'  => $this->payment_frequency_id,
            'paymentFrequency'      => $this->paymentFrequency,
            'interestType'          => $this->interestType,
            'repayment_period'      => $this->repayment_period,
            'service_fee'           => (float) $this->service_fee,

            'penalty_type_id'       => $this->penalty_type_id ?? '',

            'penalty_value'         => (float) $this->penalty_value,
            'penalty_frequency_id'  => $this->penalty_frequency_id ? $this->penalty_frequency_id : null,

            'reduce_principal_early'  => (boolean) $this->reduce_principal_early,

            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
