<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/08/2019
 * Time: 16:55
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FiscalPeriodResource extends JsonResource
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
            'id'            => $this->id,
            'start_on'      => $this->start_on,
            'end_on'        => $this->end_on,
            'closed_on'     => $this->closed_on,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
