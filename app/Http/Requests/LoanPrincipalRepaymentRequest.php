<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 21/09/2019
 * Time: 21:25
 */

namespace App\Http\Requests;

class LoanPrincipalRepaymentRequest extends BaseRequest
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
                        'branch_id'     => 'required|exists:branches,id',
                        'loan_id'       => 'required|exists:loans,id',
                        'period_count'  => '',
                        'due_date'      => '',
                        'amount'        => 'required',
                        'paid_on'       => ''
                    ];
                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'     => 'required|exists:branches,id',
                        'loan_id'       => 'required|exists:loans,id',
                        'period_count'  => '',
                        'due_date'      => '',
                        'amount'        => 'required',
                        'paid_on'       => ''
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}