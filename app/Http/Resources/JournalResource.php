<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 14:42
 */

namespace App\Http\Resources;

class JournalResource extends BaseResource
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
            'branch_id'         => $this->branch_id,

            'transaction_id'        => $this->transaction_id,
            'debit_account_id'      => $this->debit_account_id,
            'credit_account_id'     => $this->credit_account_id,
            'amount'                => $this->formatMoney($this->amount),
            'narration'             => $this->narration,
            'preparedBy'             => $this->preparedBy,

            'debitAccount'          => AccountResource::make($this->debitAccount),
            'creditAccount'         => AccountResource::make($this->creditAccount),

            'created_by'        => $this->created_by,
            'updated_by'        => $this->updated_by,

            'created_at' => $this->formatDateTime($this->created_at),
            'updated_at' => $this->updated_at
        ];
    }
}
