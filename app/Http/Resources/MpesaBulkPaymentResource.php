<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 24/01/2020
 * Time: 09:21
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MpesaBulkPaymentResource extends JsonResource
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

            'transaction_amount'                        => $this->transaction_amount,
            'transaction_receipt'                       => $this->transaction_receipt,
            'b2C_recipientIs_registered_customer'       => $this->b2C_recipientIs_registered_customer,
            'b2C_charges_paid_account_available_funds'  => $this->b2C_charges_paid_account_available_funds,
            'receiver_party_public_name'                => $this->receiver_party_public_name,
            'transaction_completed_date_time'           => $this->transaction_completed_date_time,
            'b2C_utility_account_available_funds'       => $this->b2C_utility_account_available_funds,
            'b2C_working_account_available_funds'       => $this->b2C_working_account_available_funds,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
