<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 15/09/2019
 * Time: 13:23
 */

namespace App\Http\Resources;

class AccountBalanceResource extends BaseResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'account_id'    => $this->account_id,
            'balance'       => $this->formatMoney($this->balance),
        ];
    }
}
