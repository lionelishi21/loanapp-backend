<?php


namespace App\Http\Requests;


use Illuminate\Validation\Rule;

class UserRequest extends BaseRequest
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
                        'first_name'            => 'required',
                        'middle_name'           => '',
                        'last_name'             => 'required',
                        'user_photo'            => '',
                        'photo'                 => '',
                        'postal_code'           => '',
                        'postal_address'        => '',
                        'physical_address'      => '',
                        'city'                  => '',
                        'country'               => '',
                        'role_id'               => 'required|exists:roles,id',
                        'email'                 => 'email|required|unique:users,email,NULL,id,deleted_at,NULL',
                        'password'              => 'required|min:3|confirmed',
                        'password_confirmation' => 'required_with:password'
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'             => 'required|exists:branches,id',
                        'first_name'            => '',
                        'middle_name'           => '',
                        'last_name'             => 'required',
                        'photo'                 => '',
                        'postal_code'           => '',
                        'postal_address'        => '',
                        'physical_address'      => '',
                        'city'                  => '',
                        'country'               => '',
                        'phone'                 => '',
                        'role_id'               => 'required|exists:roles,id',
                        'email'                 => ['required', Rule::unique('users')->ignore($this->user, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'password'              => 'nullable|min:3|confirmed',
                        'password_confirmation' => 'required_with:password'
                    ];
                    break;
                }
            default:break;
        }
        return $rules;
    }
}