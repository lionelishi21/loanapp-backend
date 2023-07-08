<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 06/11/2019
 * Time: 02:05
 */

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserProfileRequest extends BaseRequest
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
                        'branch_id' => 'required|exists:branches,id',
                        'first_name' => 'required',
                        'middle_name' => '',
                        'last_name' => 'required',
                        'user_photo' => '',
                        'photo' => '',
                        'postal_code' => '',
                        'postal_address' => '',
                        'physical_address' => '',
                        'city' => '',
                        'country' => '',
                        'phone'                 => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',

                        'role_id' => 'required|exists:roles,id',
                        'email' => 'email|required|unique:users,email,NULL,id,deleted_at,NULL',
                        'password' => 'required|min:3|confirmed',
                        'password_confirmation' => 'required_with:password'
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id' => 'required|exists:branches,id',
                        'first_name' => '',
                        'middle_name' => '',
                        'last_name' => 'required',
                        'photo' => '',
                        'postal_code' => '',
                        'postal_address' => '',
                        'physical_address' => '',
                        'city' => '',
                        'country' => '',
                        'phone'                 => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
                        'role_id' => 'required|exists:roles,id',
                        'email' => ['required', Rule::unique('users')->ignore($this->user_profile, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                         'current_password' => ['nullable', 'required_with:password', function ($attribute, $value, $fail) {
                                if (!Hash::check($value, Auth::user()->password)) {
                                    return $fail(__('The current password is incorrect.'));
                                }
                            }],
                        'password' => 'nullable|min:3|confirmed',
                        'password_confirmation' => 'required_with:password'

                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}