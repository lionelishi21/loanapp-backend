<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 31/01/2020
 * Time: 10:33
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MpesaScheduledDisbursementResource extends JsonResource
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
            'member_id'     => $this->member_id,
            'branch'        => $this->branch,
            'mpesa_number'  => $this->mpesa_number,
            'amount'        => $this->amount,
            'created_by'    => $this->created_by,
            'createdBy'     => $this->createdBy,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
