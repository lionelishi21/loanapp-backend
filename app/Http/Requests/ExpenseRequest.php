<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/08/2019
 * Time: 16:03
 */

namespace App\Http\Requests;

class ExpenseRequest extends BaseRequest
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
                        'branch_id'                 => 'exists:branches,id',
                        'title'                     => 'required',
                        'amount'                    => 'required',
                        'expense_date'              => '',
                        'attachment'                => '',
                        'registered_by_user_id'     => '',
                        'category_id'               => 'required|exists:accounts,id',
                        'notes'                     => '',
                        'created_by'                => '',
                        'updated_by'                => '',
                        'deleted_by'                => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'                 => 'exists:branches,id',
                        'title'                     => 'required',
                        'amount'                    => 'required',
                        'expense_date'              => '',
                        'attachment'                => '',
                        'registered_by_user_id'     => '',
                        'category_id'               => 'required|exists:accounts,id',
                        'notes'                     => '',
                        'created_by'                => '',
                        'updated_by'                => '',
                        'deleted_by'                => ''
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}