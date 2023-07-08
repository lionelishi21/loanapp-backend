<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/11/2019
 * Time: 20:34
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CapitalResource extends JsonResource
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
            'id'                => $this->id,
            'branch_id'         => $this->branch_id,
            'method_id'         => $this->method_id,
            'capital_date'      => $this->capital_date,
            'branch'         => $this->branch,
            'paymentMethod'         => $this->paymentMethod,

            'amount'            => $this->amount,
            'description'       => $this->description,
            'fiscal_period_id'  => $this->fiscal_period_id,

            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at
        ];
    }
}
