<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 12:39
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GuarantorResource extends JsonResource
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
            'branch_id'         => $this->branch_id,
            'branch'            => $this->branch,

            'member_id'             => $this->member_id,

            'loan_application_id'   => $this->loan_application_id,
            'loanApplication'   =>  LoanApplicationResource::make($this->loanApplication),

            'notes'                 => $this->notes,
            'guarantee_amount'      => $this->guarantee_amount,

            'member'    => $this->member,
            'createdBy' => UserResource::make($this->createdBy),

            'created_by'        => $this->created_by,
            'updated_by'        => $this->updated_by,

            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
