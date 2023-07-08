<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 12:39
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class GuarantorRequest extends BaseRequest
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
                        'branch_id'             => 'exists:branches,id',
                        'member_id'             => 'required|exists:members,id',
                        'loan_application_id'   => 'required',
                        'guarantee_amount'      => '',
                        'notes'      => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'             => 'exists:branches,id',
                        'member_id'             => 'required|exists:members,id',
                        'loan_application_id'   => 'required',
                        'notes'                 => '',
                        'guarantee_amount'      => '',
                    ];
                    break;
                }
            default:break;
        }

        return $rules;

    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'member_id.unique' => 'This member is already a guarantor.',
        ];
    }
}