<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 26/10/2018
 * Time: 21:38
 */

namespace App\Http\Requests;

use Illuminate\Database\Schema\Builder;
use Illuminate\Validation\Rule;

class BranchRequest extends BaseRequest
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
                        'name'          => 'required|unique:branches,name,NULL,id,deleted_at,NULL',
                        'branch_code'   => 'required|unique:branches,branch_code,NULL,id,deleted_at,NULL',
                        'location'      => '',
                        'description'   => '',
                        'country'       => '',
                        'county'        => '',
                        'town'          => '',
                        'address'       => '',
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'name'             => ['required', Rule::unique('branches')->ignore($this->branch, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'branch_code'       => ['required', Rule::unique('branches')->ignore($this->branch, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'location'          => '',
                        'description'   => '',
                        'country'       => '',
                        'county'        => '',
                        'town'          => '',
                        'address'       => '',
                    ];
                    break;
                }
            default:break;
        }

        return $rules;

    }
}