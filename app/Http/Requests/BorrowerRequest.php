<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 26/10/2018
 * Time: 12:10
 */

namespace App\Http\Requests;

class BorrowerRequest extends BaseRequest
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
                        'branch_id'             => 'required|exists:branches,id',
                        'member_id'                     => 'required',
                        'credit_score'                  => '',
                        'borrower_status_id'            => '',
                        'witness_type_id'               => 'required',
                        'witness_first_name'            => 'required',
                        'witness_last_name'             => 'required',
                        'witness_country'               => 'required',
                        'witness_city'                  => 'required',
                        'witness_national_id'           => 'required',
                        'witness_phone'                 => 'required',
                        'witness_email'                 => 'email',
                        'witness_postal_address'        => 'required',
                        'witness_residential_address'   => 'required',
                        'notes'                         => '',
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'             => 'required|exists:branches,id',
                        'member_id'                     => 'required',
                        'credit_score'                  => '',
                        'borrower_status_id'            => '',
                        'witness_type_id'               => 'required',
                        'witness_first_name'            => 'required',
                        'witness_last_name'             => 'required',
                        'witness_country'               => 'required',
                        'witness_city'                  => 'required',
                        'witness_national_id'           => 'required',
                        'witness_phone'                 => 'required',
                        'witness_email'                 => 'email',
                        'witness_postal_address'        => 'required',
                        'witness_residential_address'   => 'required',
                        'notes'                         => '',
                    ];
                    break;
                }
            default:break;
        }
        return $rules;
    }
}