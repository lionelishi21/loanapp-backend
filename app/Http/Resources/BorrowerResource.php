<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 26/10/2018
 * Time: 12:10
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BorrowerResource extends JsonResource
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
            'id'                            => $this->id,
            'branch_id'         => $this->branch_id,

            'member_id'                     => $this->member_id,
            'credit_score'                  => $this->credit_score,
            'borrower_status_id'            => $this->borrower_status_id,
            'witness_type_id'               => $this->witness_type_id,
            'witness_first_name'            => $this->witness_first_name,
            'witness_country'               => $this->witness_country,
            'witness_city'                  => $this->witness_city,
            'witness_last_name'             => $this->witness_last_name,
            'witness_national_id'           => $this->witness_national_id,
            'witness_phone'                 => $this->witness_phone,
            'witness_email'                 => $this->witness_email,
            'witness_postal_address'        => $this->witness_postal_address,
            'witness_residential_address'   => $this->witness_residential_address,
            'notes'                         => $this->notes,

            'created_by'        => $this->created_by,
            'updated_by'        => $this->updated_by,

            'created_at'                    => $this->created_at,
            'updated_at'                    => $this->updated_at,
        ];
    }
}
