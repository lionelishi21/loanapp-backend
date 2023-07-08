<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 07/08/2019
 * Time: 08:52
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class InterestTypeRequest extends BaseRequest
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
                        'name'         => 'required|unique:interest_types,name,NULL,id,deleted_at,NULL',
                        'display_name' => 'required|unique:interest_types,display_name,NULL,id,deleted_at,NULL',
                        'description'  => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'name'                 => ['required', 'exists:interest_types', Rule::unique('interest_types')->ignore($this->interest_type, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],

                        'display_name'           => ['required', Rule::unique('interest_types')->ignore($this->interest_type, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                    ];
                    break;
                }
            default:
                break;
        }
        return $rules;
    }
}