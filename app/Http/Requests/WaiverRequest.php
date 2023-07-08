<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 05/12/2019
 * Time: 11:43
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class WaiverRequest extends BaseRequest
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
                        'balance'       => 'required|numeric',
                        'waiver_amount'    => 'required|numeric|min:0.01|lte:balance',
                        'id'            => 'required|exists:loan_penalties,id',
                        'loan_id'       => 'required|exists:loans,id',
                        'loan'          => 'required',
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'balance'       => 'required|numeric',
                        'waiver_amount'    => 'required|numeric|lte:balance',
                        'id'            => 'required|exists:loan_penalties,id',
                        'loan_id'       => 'required|exists:loans,id',
                        'loan'          => 'required',
                    ];
                    break;
                }
            default:
                break;
        }
        return $rules;
    }
}