<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 10:38
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AccountStatusRequest extends BaseRequest
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
                        'name' => 'required|unique:account_statuses,name,NULL,id,deleted_at,NULL',
                        'description' => '',
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'name' => ['required', Rule::unique('account_statuses')->ignore($this->account_status, 'id')
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