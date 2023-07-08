<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/11/2019
 * Time: 20:32
 */

namespace App\Http\Requests;

class CapitalRequest extends BaseRequest
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
                        'branch_id'         => 'required|exists:branches,id',
                        'method_id'         => 'required|exists:payment_methods,id',
                        'amount'            => 'required',
                        'capital_date'      => 'required',
                        'fiscal_period_id'  => '',
                        'description'       => ''
                    ];
                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'         => 'required|exists:branches,id',
                        'method_id'         => 'required|exists:payment_methods,id',
                        'amount'            => 'required',
                        'capital_date'      => 'required',
                        'fiscal_period_id'  => '',
                        'description'       => ''
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}