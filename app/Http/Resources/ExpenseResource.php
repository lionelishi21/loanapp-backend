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

class ExpenseResource extends JsonResource
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
            'id'                        => $this->id,
            'branch_id'                 => $this->branch_id,
            'title'                     => $this->title,
            'category'                  => $this->category,
            'amount'                    => $this->amount,
            'expense_date'              => $this->expense_date,
            'attachment'                => $this->attachment,
            'registered_by_user_id'     => $this->registered_by_user_id,
            'category_id'               => $this->category_id,
            'notes'                     => $this->notes,
            'created_by'                => $this->created_by,
            'updated_by'                => $this->updated_by,
            'deleted_by'                => $this->deleted_by,

            'created_at'                => $this->created_at,
            'updated_at'                => $this->updated_at,
        ];
    }
}
