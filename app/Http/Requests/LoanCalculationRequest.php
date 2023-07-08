<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 26/02/2020
 * Time: 02:52
 */

namespace App\Http\Requests;

class LoanCalculationRequest extends BaseRequest
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
                        'start_date'    => 'required',
                        'amount'        => 'required|numeric|min:0|not_in:0',
                        'loan_type_id'  => 'required|exists:loan_types,id'
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [

                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}