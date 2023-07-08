<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 20/09/2019
 * Time: 12:29
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class LoanRepaymentRequest extends BaseRequest
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
                        'name' => 'required|unique:loan_types,name,NULL,id,deleted_at,NULL',
                        'description' => '',
                        'active_status' => '',
                        'interest_rate' => '',
                        'interest_type_id' => 'required|exists:interest_types,id',
                        'payment_frequency_id' => 'required|exists:payment_frequencies,id',
                        'repayment_period' => '',
                        'service_fee' => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'name' => ['required', Rule::unique('loan_types')->ignore($this->loan_type, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'description' => '',
                        'active_status' => '',
                        'interest_rate' => '',
                        'interest_type_id' => 'required|exists:interest_types,id',
                        'payment_frequency_id' => 'required|exists:payment_frequencies,id',
                        'repayment_period' => '',
                        'service_fee' => ''
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}