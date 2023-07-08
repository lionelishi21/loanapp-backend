<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 11:10
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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

            'employee_number'       => $this->employee_number,
            'first_name'            => $this->first_name,
            'last_name'             => $this->last_name,
            'salutation'            => $this->salutation,
            'country'               => $this->country,
            'national_id_number'    => $this->national_id_number,
            'passport_number'       => $this->passport_number,
            'email'                 => $this->email,
            'telephone_number'      => $this->telephone_number,
            'address'               => $this->address,
            'postal_code'           => $this->postal_code,
            'county'                => $this->county,
            'city'                  => $this->city,
            'nhif_number'           => $this->nhif_number,
            'nssf_number'           => $this->nssf_number,
            'kra_pin'               => $this->kra_pin,
            'gender'                => $this->gender,
            'job_group'             => $this->job_group,
            'designation_id'        => $this->designation_id,
            'department_id'         => $this->department_id,
            'birth_day'             => $this->birth_day,
            'profile_picture'       => $this->profile_picture,
            'national_id_image'     => $this->national_id_image,

            'created_by'        => $this->created_by,
            'updated_by'        => $this->updated_by,

            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at
        ];
    }
}
