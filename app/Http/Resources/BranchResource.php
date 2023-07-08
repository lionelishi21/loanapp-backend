<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 26/10/2018
 * Time: 21:38
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'id'            => $this->id,
            'name'          => $this->name,
            'location'      => $this->location,

            'assets'            => $this->assets,
            'employees'         => $this->employees,
            'loans'             => $this->loans,
            'loanApplications'  => $this->loanApplications,
            'members'           => $this->members,
            'users'             => $this->users,


            'description'   => $this->description,
            'country'       => $this->country,
            'county'        => $this->county,
            'town'          => $this->town,
            'address'       => $this->address,
            'branch_code'   => $this->branch_code,

            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
