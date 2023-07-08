<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 27/10/2018
 * Time: 11:17
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class MemberRequest extends BaseRequest
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
                        'first_name'            => 'required',
                        'middle_name'           => '',
                        'last_name'             => '',
                        'date_of_birth'         => 'required|date',
                        'date_became_member'    => 'required|date',
                        'nationality'           => 'required',
                        'county'                => '',
                        'city'                  => '',
                        'extra_images'      => '',
                        'id_number'             => 'required|unique:members,id_number,NULL,id,deleted_at,NULL',
                        'passport_number'       => '',
                        'phone'                 => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|digits_between:9,12|unique:members,phone,NULL,id,deleted_at,NULL',
                        'email'                 => 'nullable|email',
                        'postal_address'        => 'required',
                        'residential_address'   => 'required',
                        'status_id'             => '',
                        'passport_photo'        => '',
                        'membership_form'       => 'nullable|file|mimes:doc,pdf,docx,zip|max:10000'

                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'             => 'exists:branches,id',
                        'first_name'            => 'required',
                        'middle_name'           => 'required',
                        'last_name'             => '',
                        'date_of_birth'         => 'required|date',
                        'date_became_member'    => 'required|date',
                        'nationality'           => 'required',
                        'county'                => '',
                        'city'                  => '',
                        'extra_images'     => '',
                        'id_number'                 => ['required', Rule::unique('members')->ignore($this->member, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'phone'                 => ['required','regex:/^([0-9\s\-\+\(\)]*)$/', 'digits_between:9,12', Rule::unique('members')->ignore($this->member, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'passport_number'       => '',
                        'email'                 => 'nullable|email',
                        'postal_address'        => 'required',
                        'residential_address'   => 'required',
                        'status_id'             => '',
                        'passport_photo'        => '',
                        'membership_form'       => 'nullable|file|mimes:doc,pdf,docx,zip|max:10000'
                    ];
                    break;
                }
            default:break;
        }

        return $rules;

    }
}