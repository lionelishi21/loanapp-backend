<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/07/2019
 * Time: 13:24
 */

namespace App\Http\Requests;

use Illuminate\Database\Schema\Builder;
use Illuminate\Validation\Rule;

class AssetRequest extends BaseRequest
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
                        'branch_id'             => 'exists:branches,id',
                        'member_id'             => 'exists:members,id',
                        'asset_number'          => '',
                        'title'                 => 'required',
                        'description'           => 'required',
                        'valuation_date'        => 'required',
                        'valued_by'             => 'required',
                        'valuer_phone'          => 'required',
                        'valuation_amount'      => 'required|numeric',
                        'location'              => 'required',
                        'registration_number'   => '',
                        'registered_to'         => '',
                        'condition'             => '',
                        'notes'                 => '',
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'             => 'exists:branches,id',
                        'member_id'             => 'exists:members,id',
                        'asset_number'          => '',
                        'title'                 => 'required',
                        'description'           => 'required',
                        'valuation_date'        => 'required',
                        'valued_by'             => 'required',
                        'valuer_phone'          => 'required',
                        'valuation_amount'      => 'required|numeric',
                        'location'              => 'required',
                        'registration_number'   => '',
                        'registered_to'         => '',
                        'condition'             => '',
                        'notes'                 => '',
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}