<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 31/01/2020
 * Time: 10:32
 */

namespace App\Http\Requests;

class MpesaScheduledDisbursementRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'member_id'     => 'exists:members,id',
            'mpesa_number'  => '',
            'amount'        => '',
        ];
    }
}

