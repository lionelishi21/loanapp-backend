<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 22/10/2019
 * Time: 11:42
 */

namespace App\Http\Requests;

class FinanceStatementRequest extends BaseRequest
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
                        'branch_id' => 'required|exists:branches,id',
                        'start_date' => 'required',
                        'end_date' => 'required',
                        'statement_type_id' => '',
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id' => 'required|exists:branches,id',
                        'start_date' => 'required',
                        'end_date' => 'required',
                        'statement_type_id' => '',
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}
