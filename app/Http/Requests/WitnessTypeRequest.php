<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/07/2019
 * Time: 07:34
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class WitnessTypeRequest extends BaseRequest
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
                        'name'          => 'required|unique:witness_types,name,NULL,id,deleted_at,NULL',
                        'display_name'  => 'required|unique:witness_types,display_name,NULL,id,deleted_at,NULL',
                        'description'   => '',
                    ];
                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'name' => ['required', Rule::unique('witness_types')->ignore($this->witness_type, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],

                        'display_name'                 => ['required', Rule::unique('witness_types')->ignore($this->witness_type, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],

                        'description' => ''
                    ];
                    break;
                }
            default:
                break;
        }
        return $rules;
    }
}