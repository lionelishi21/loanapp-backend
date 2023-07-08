<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/08/2019
 * Time: 16:01
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseCategoryResource extends JsonResource
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
            'account_name'  => $this->account_name,
            'description'   => $this->description,

            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ];
    }
}
