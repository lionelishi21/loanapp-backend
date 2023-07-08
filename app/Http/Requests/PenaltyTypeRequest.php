<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 10/11/2019
 * Time: 16:06
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PenaltyTypeRequest extends BaseRequest
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
                        'name' => 'required|unique:penalty_types,name,NULL,id,deleted_at,NULL',
                        'display_name' => 'required|unique:penalty_types,display_name,NULL,id,deleted_at,NULL',
                        'description' => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'name' => ['required', 'exists:penalty_types', Rule::unique('penalty_types')->ignore($this->penalty_type, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],

                        'display_name' => ['required', Rule::unique('penalty_types')->ignore($this->penalty_type, 'id')
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