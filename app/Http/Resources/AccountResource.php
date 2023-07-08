<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 10:41
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id'                => $this->id,
            'branch_id'         => $this->branch_id,
            'accountBalance'    => $this->accountBalance,

            'account_number'    => $this->account_number,
            'account_code'      => $this->account_code,

            'account_name'      => $this->account_name,
            'account_namexxxx'      => $this->account_name,

            'member'        => $this->member,
            'loan'          => $this->loan,
            'account_type_id'   => $this->account_type_id,
            'accountType'   => AccountTypeResource::make($this->accountType),
            'account_status_id' => $this->account_status_id,
            'other_details'     => $this->other_details,
            'closed_on'         => $this->closed_on,

            'statement' => $this->when(isset($this->statement), function () {
                return $this->transformStatement($this->statement);
            }),

            'created_by'        => $this->created_by,
            'updated_by'        => $this->updated_by,

            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,

        ];

        if(isset($this->member)){
            $data['account_display_name'] = $this->member->first_name.' '.$this->member->last_name;
        }
        elseif(isset($this->loan)){
            $data['account_display_name'] = 'Loan# '.$this->loan->loan_reference_number;
        }else {
            $data['account_display_name'] = $this->account_name;
        }

        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    private function transformStatement($data) {
        return $data->map(function($item) {
            return [
                'account_id'    => $item->account_id,
                'journal_id'    => $item->journal_id,
                'created_at'    => formatDate($item->created_at),
                'amount'        => $item->amount,
                'display_amount' => $this->displayAmount($item->amount),
                'is_cr'         => $this->isCr($item->amount),
                'is_dr'         => $this->isDr($item->amount),
                'narration'     => $item->narration,
                'balance'       => $this->formatMoney($item->balance)
            ];
        })->toArray();
    }

    /**
     * @param $amount
     * @return string
     */
    private function formatMoney($amount) {
        return number_format($amount, 2, '.', ',');
    }

    /**
     * @param $amount
     * @return bool
     */
    private function isCr($amount) {
        return $amount < 0 ? true : false;
    }

    /**
     * @param $amount
     * @return bool
     */
    private function isDr($amount) {
       return $amount > 0 ? true : false;
    }

    /**
     * @param $amount
     * @return float|int
     */
    private function displayAmount($amount) {
        return $this->isCr($amount) ? $this->formatMoney($amount*-1) : $this->formatMoney($amount);
    }
}
