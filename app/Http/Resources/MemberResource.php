<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 11:17
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MemberResource extends JsonResource
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
            'branch_id'             => $this->branch_id,
            'branch'             => $this->branch,
            'first_name'            => $this->first_name,
            'middle_name'           => $this->middle_name,
            'last_name'             => $this->last_name,

            'date_of_birth'         => $this->date_of_birth,
            'date_of_birth_display'         => formatDate($this->date_of_birth),
            'date_became_member'    => $this->date_became_member,
            'date_became_member_display'    => formatDate($this->date_became_member),

            'nationality'           => $this->nationality,
            'county'                => $this->county,
            'city'                  => $this->city,
            'extra_images'          => $this->extra_images,
            'id_number'             => $this->id_number,
            'passport_number'       => $this->passport_number,
            'phone'                 => $this->phone,
            'email'                 => $this->email,
            'postal_address'        => $this->postal_address,
            'residential_address'   => $this->residential_address,
            'status_id'             => $this->status_id,

            'passport_photo'        => $this->passport_photo,

            'assets'                => $this->assets,
            'account'               => AccountResource::make($this->account),
            'guaranteedLoans'       => $this->guaranteedLoans,
            'membership_form'       => $this->membership_form,

            'created_by'        => $this->created_by,
            'updated_by'        => $this->updated_by,

            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
