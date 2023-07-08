<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 16/12/2018
 * Time: 11:23
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PaymentMethodRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [];

        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                    break;
                }
            case 'POST':
                {
                    $rules = [
                        'name'          => 'required|unique:payment_methods,name,NULL,id,deleted_at,NULL',
                        'display_name'  => 'required|unique:payment_methods,display_name,NULL,id,deleted_at,NULL',
                        'description'   => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'name'                 => ['required', Rule::unique('payment_methods')->ignore($this->payment_method, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'display_name'                 => ['required', Rule::unique('payment_methods')->ignore($this->payment_method, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'description'           => '',
                    ];
                    break;
                }
            default:break;
        }

        return $rules;

    }
}