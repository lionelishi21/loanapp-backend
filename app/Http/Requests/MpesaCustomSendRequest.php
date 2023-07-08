<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 24/01/2020
 * Time: 23:13
 */

namespace App\Http\Requests;

class MpesaCustomSendRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone'         => '',
            'amount'        => '',
            'description'   => ''
        ];
    }
}

