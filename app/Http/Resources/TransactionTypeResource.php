<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 14:42
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionTypeResource extends JsonResource
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

            'code' => $this->code,
            'description' => $this->description,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
