<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/08/2019
 * Time: 16:03
 */

namespace App\Http\Requests;

class ExpenseCategoryRequest extends BaseRequest
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
                        'account_type_id'   => 'exists:account_types,id',
                        'account_name'      => 'required',
                        'description'       => '',
                        'created_by'        => '',
                        'updated_by'        => '',
                        'deleted_by'        => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'account_type_id'   => 'exists:account_types,id',
                        'account_name'      => 'required',
                        'description'       => '',
                        'created_by'        => '',
                        'updated_by'        => '',
                        'deleted_by'        => ''
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}