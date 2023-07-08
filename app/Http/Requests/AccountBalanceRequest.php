<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 15/09/2019
 * Time: 13:23
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AccountBalanceRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [];

        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                    break;
                }
            case 'POST':
                {
                    $rules = [
                        'account_id'    => 'required|exists:accounts,id',
                        'balance'       => 'required',
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'account_id'    => 'required|exists:accounts,id',
                        'balance'       => 'required',
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}